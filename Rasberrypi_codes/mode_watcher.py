import firebase_admin
from firebase_admin import credentials, db
import subprocess
import time
import os
import signal

# Path to your Firebase service account key
cred = credentials.Certificate("/home/SwaroopReddy/myenv/firebase-credentials.json")

# Initialize Firebase app
firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://companionapp-acc4c-default-rtdb.firebaseio.com/'
})

# Firebase path for the mode
mode_ref = db.reference('robot_mode')

# Initialize mode if not set
if not mode_ref.get():
    print("robot_mode node not found. Initializing to 'robot_movement'.")
    mode_ref.set("robot_movement")

# Store running process
current_process = None
current_mode = None

def stop_current_process():
    global current_process
    if current_process:
        print(f"Stopping {current_mode}")
        os.killpg(os.getpgid(current_process.pid), signal.SIGTERM)
        current_process = None

def start_process(script_path):
    global current_process
    print(f"Starting {script_path}")
    current_process = subprocess.Popen(
        ['python3', script_path],
        preexec_fn=os.setsid
    )

while True:
    try:
        mode = mode_ref.get()
        if mode != current_mode:
            stop_current_process()

            if mode == "human_following":
                start_process("/home/SwaroopReddy/myenv/robotics1/web.py")
                current_mode = "human_following"

            elif mode == "robot_movement":
                start_process("/home/SwaroopReddy/myenv/robot_controller.py")
                current_mode = "robot_movement"

            else:
                print("Invalid mode:", mode)
                current_mode = None

        time.sleep(2)  # poll every 2 seconds

    except KeyboardInterrupt:
        stop_current_process()
        print("Exiting watcher.")
        break

    except Exception as e:
        print("Error:", e)
        time.sleep(5)
