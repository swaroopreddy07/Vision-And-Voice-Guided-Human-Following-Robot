import cv2
import time
import threading
import numpy as np
from flask import Flask, Response, render_template_string

from sensor_interface import Sensor
from human_detection import YoloHumanDetector
from motor_control import MotorController
from voice_control import VoiceController
from kalman_filter import KalmanTracker
from pid_control import RobotPIDControl
from admm_optimizer import ADMM
from fft_filter import FFTFilter

app = Flask(__name__)
latest_frame = None
frame_lock = threading.Lock()

class FrameGrabber(threading.Thread):
    def __init__(self, sensor):
        super().__init__()
        self.sensor = sensor
        self.running = True

    def run(self):
        global latest_frame
        while self.running:
            frame = self.sensor.get_frame()
            if frame is not None:
                with frame_lock:
                    latest_frame = frame
            time.sleep(0.01)

    def stop(self):
        self.running = False

class RobotLogic(threading.Thread):
    def __init__(self, sensor):
        super().__init__()
        self.sensor = sensor
        self.running = True
        self.following = False
        self.frame_center = (320, 240)

        self.detector = YoloHumanDetector()
        self.motor = MotorController()
        self.voice = VoiceController()
        self.kalman = KalmanTracker()
        self.pid = RobotPIDControl()
        self.admm = ADMM()
        self.fft = FFTFilter()

        self.frame_count = 0

    def run(self):
        global latest_frame
        print("[RobotLogic] Say 'follow me' or 'stop'.")

        while self.running:
            with frame_lock:
                frame = latest_frame.copy() if latest_frame is not None else None
            if frame is None:
                continue

            gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
            gray = self.fft.filter(gray.flatten()).reshape(gray.shape)

            if self.frame_count % 10 == 0:
                command = self.voice.get_command()
                if command:
                    print("[Voice] Command received:", command)
                    if command.lower() == "stop":
                        self.following = False
                        self.motor.stop()
                    elif command.lower() == "follow me":
                        self.following = True

            if self.following:
                if self.frame_count % 3 == 0:
                    detected_box = self.detector.detect(frame)
                    if detected_box:
                        x, y, w, h = detected_box
                        cx = x + w // 2
                        cy = y + h // 2

                        cv2.rectangle(frame, (x, y), (x+w, y+h), (0, 255, 0), 2)

                        kalman_pos = self.kalman.update((cx, cy))
                        error = cx - self.frame_center[0]

                        _ = self.admm.optimize(
                            current_position=kalman_pos,
                            target_position=(cx, cy),
                            speed_limits=[np.array([0, 0]), np.array([640, 480])]
                        )

                        center_error = abs(error)
                        angle = self.pid.get_steering_adjustment(error)
                        speed = self.pid.get_speed_adjustment(center_error)

                        # ?? Force forward when person is centered (0 pixels)
                        if center_error < 60:
                            print("[Forward] Person centere moving forward")
                            angle = 0                # go straight
                            speed = 50               # higher default speed to ensure forward motion

                        # ?? Limit turn angle to prevent overcorrection
                        angle = max(min(angle, 100), -100)

                        print(f"[Control] center_error={center_error}, angle={angle}, speed={speed}")
                        self.motor.move(angle, speed)

                    else:
                        print("Searching for human...")
                        self.motor.search()

            with frame_lock:
                latest_frame = frame
            self.frame_count += 1
            time.sleep(0.01)

    def stop(self):
        self.running = False
        self.motor.stop()

@app.route('/')
def index():
    return render_template_string("""
        <html>
        <head><title>Robot Camera</title></head>
        <body>
            <h1>Live Robot Camera Stream</h1>
            <img src="{{ url_for('video_feed') }}" width="1000" height="600">
        </body>
        </html>
    """)

@app.route('/video_feed')
def video_feed():
    def generate():
        while True:
            with frame_lock:
                frame = latest_frame.copy() if latest_frame is not None else None
            if frame is None:
                continue
            frame = cv2.resize(frame, (1000, 600))
            ret, buffer = cv2.imencode('.jpg', frame)
            if not ret:
                continue
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + buffer.tobytes() + b'\r\n')
            time.sleep(0.03)
    return Response(generate(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    sensor = Sensor()
    frame_grabber = FrameGrabber(sensor)
    robot_logic = RobotLogic(sensor)

    frame_grabber.start()
    robot_logic.start()

    try:
        print("🔗 Visit http://localhost:5000 to view the camera feed.")
        app.run(host='0.0.0.0', port=5000, threaded=True)
    finally:
        print("🛑 Shutting down...")
        frame_grabber.stop()
        robot_logic.stop()
        sensor.release()
        motor.cleanup()
