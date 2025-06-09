import cv2
import numpy as np

from sensor_interface import Sensor
from human_detection import YoloHumanDetector
from motor_control import MotorController
from voice_control import VoiceController
from kalman_filter import KalmanTracker
from pid_control import RobotPIDControl
from optical_flow import compute_optical_flow
from path_smoothing import PathSmoothing
from admm_optimizer import ADMM
from fft_filter import FFTFilter


def main():
    # Initialize components
    sensor = Sensor()
    detector = YoloHumanDetector()
    motor = MotorController()
    voice = VoiceController()
    kalman_tracker = KalmanTracker()
    pid_control = RobotPIDControl()
    path_smoothing = PathSmoothing()
    admm_optimizer = ADMM()
    fft_filter = FFTFilter()

    # Robot state
    following = False
    frame_center = (320, 240)  # Assuming 640x480 resolution
    prev_gray = None
    prev_points = None
    positions = []

    print("Robot is running. Awaiting voice commands (say 'follow me' or 'stop').")

    while True:
        frame = sensor.get_frame()
        if frame is None:
            continue

        # Convert to grayscale
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

        # Apply FFT noise reduction
        gray = fft_filter.filter(gray.flatten()).reshape(gray.shape)

        # Poll for voice commands
        command = voice.get_command()
        if command:
            print("Voice command received:", command)
            if command.lower() == "stop":
                following = False
                motor.stop()
            elif command.lower() == "follow me":
                following = True

        # Initialize default
        human_position = None

        if following:
            human_position = detector.detect(frame)

            if human_position:
                kalman_position = kalman_tracker.update(human_position)
                error = human_position[0] - frame_center[0]

                # ADMM optimization
                optimized_position = admm_optimizer.optimize(
                    current_position=kalman_position,
                    target_position=human_position,
                    speed_limits=None
                )

                # PID control
                angle = pid_control.get_steering_adjustment(error)
                speed = pid_control.get_speed_adjustment(abs(error))

                motor.move(angle, speed)
            else:
                motor.search()

        # Save previous info only if detection was successful
        if human_position:
            prev_gray = gray
            prev_points = human_position

        # Debug display
        cv2.imshow("Robot Camera", frame)
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    sensor.release()
    cv2.destroyAllWindows()
    voice.stop_listening()


if __name__ == "__main__":
    main()
