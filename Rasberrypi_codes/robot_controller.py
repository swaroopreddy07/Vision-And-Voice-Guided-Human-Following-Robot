import firebase_admin
from firebase_admin import credentials, db
import RPi.GPIO as GPIO
import time
import threading
import cv2
from flask import Flask, Response

# ---------- Firebase Setup ----------
cred = credentials.Certificate("/home/SwaroopReddy/myenv/firebase-credentials.json")
firebase_admin.initialize_app(cred, {
    "databaseURL": "https://companionapp-acc4c-default-rtdb.firebaseio.com/"
})

# ---------- Motor Setup ----------
IN1 = 17  # Left Motor +
IN2 = 18  # Left Motor -
IN3 = 22  # Right Motor +
IN4 = 23  # Right Motor -
ENA = 24  # PWM for Left Motor
ENB = 25  # PWM for Right Motor

GPIO.setmode(GPIO.BCM)
GPIO.setup(IN1, GPIO.OUT)
GPIO.setup(IN2, GPIO.OUT)
GPIO.setup(IN3, GPIO.OUT)
GPIO.setup(IN4, GPIO.OUT)
GPIO.setup(ENA, GPIO.OUT)
GPIO.setup(ENB, GPIO.OUT)

pwmA = GPIO.PWM(ENA, 250)  # Left PWM
pwmB = GPIO.PWM(ENB, 250)  # Right PWM
pwmA.start(0)
pwmB.start(0)

# ---------- Motor Control Functions ----------
def stop():
    GPIO.output(IN1, GPIO.LOW)
    GPIO.output(IN2, GPIO.LOW)
    GPIO.output(IN3, GPIO.LOW)
    GPIO.output(IN4, GPIO.LOW)
    pwmA.ChangeDutyCycle(0)
    pwmB.ChangeDutyCycle(0)
    print("Motors stopped")

def move_forward():
    GPIO.output(IN1, GPIO.HIGH)
    GPIO.output(IN2, GPIO.LOW)
    GPIO.output(IN3, GPIO.HIGH)
    GPIO.output(IN4, GPIO.LOW)
    pwmA.ChangeDutyCycle(80)
    pwmB.ChangeDutyCycle(80)
    print("Moving forward")
    time.sleep(2)
    stop()

def move_backward():
    GPIO.output(IN1, GPIO.LOW)
    GPIO.output(IN2, GPIO.HIGH)
    GPIO.output(IN3, GPIO.LOW)
    GPIO.output(IN4, GPIO.HIGH)
    pwmA.ChangeDutyCycle(80)
    pwmB.ChangeDutyCycle(80)
    print("Moving backward")
    time.sleep(2)
    stop()

def turn_left():
    # Left motors stop
    GPIO.output(IN1, GPIO.LOW)
    GPIO.output(IN2, GPIO.LOW)
    pwmA.ChangeDutyCycle(0)

    # Right motors forward
    GPIO.output(IN3, GPIO.HIGH)
    GPIO.output(IN4, GPIO.LOW)
    pwmB.ChangeDutyCycle(80)
    print("Turning left")
    time.sleep(1)
    stop()

def turn_right():
    # Right motors stop
    GPIO.output(IN3, GPIO.LOW)
    GPIO.output(IN4, GPIO.LOW)
    pwmB.ChangeDutyCycle(0)

    # Left motors forward
    GPIO.output(IN1, GPIO.HIGH)
    GPIO.output(IN2, GPIO.LOW)
    pwmA.ChangeDutyCycle(80)
    print("Turning right")
    time.sleep(1)
    stop()

# ---------- Firebase Listener ----------
def control_robot():
    ref = db.reference("robot_control/command")
    last_command = ""

    while True:
        direction = ref.get()
        if direction is None:
            continue

        if direction != last_command:
            print(f"Command received: {direction}")
            last_command = direction

            if direction == "forward":
                move_forward()
            elif direction == "backward":
                move_backward()
            elif direction == "left":
                turn_left()
            elif direction == "right":
                turn_right()
            elif direction == "stop":
                stop()

        time.sleep(0.2)

# ---------- Flask App for Live Camera ----------
app = Flask(__name__)
camera = cv2.VideoCapture(0)

def generate_frames():
    while True:
        success, frame = camera.read()
        if not success:
            continue
        frame = cv2.resize(frame, (1000, 600))
        _, buffer = cv2.imencode('.jpg', frame)
        yield (b'--frame\r\nContent-Type: image/jpeg\r\n\r\n' + buffer.tobytes() + b'\r\n')

@app.route('/video_feed')
def video_feed():
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

# ---------- Start Everything ----------
if __name__ == "__main__":
    try:
        # Start Flask stream in a separate thread
        threading.Thread(target=lambda: app.run(host='0.0.0.0', port=5000, debug=False, use_reloader=False)).start()

        # Run robot control loop
        control_robot()

    except KeyboardInterrupt:
        print("Shutting down...")

    finally:
        stop()
        camera.release()
        GPIO.cleanup()
        print("? GPIO cleaned up. Camera released.")
