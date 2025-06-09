import time
import math
from coppeliasim_zmqremoteapi_client import RemoteAPIClient

# Connect to CoppeliaSim
client = RemoteAPIClient()
sim = client.require('sim')

# Stop simulation if already running
sim.stopSimulation()
time.sleep(1)

# Start the simulation
print("🔄 Starting Simulation...")
sim.startSimulation()
time.sleep(1)

# Get Human Handle (updated to match "Bill" from the scene)
try:
    human_handle = sim.getObject('/Bill')
    print("✅ Human Handle Retrieved!")
except Exception as e:
    print(f"⚠ Warning: Could not retrieve human handle: {e}")
    human_handle = None

# Get Wheel Handles
left_wheel_front = sim.getObject('/Revolute_joint_left_front')
left_wheel_back = sim.getObject('/Revolute_joint_left_back')
right_wheel_front = sim.getObject('/Revolute_joint_right_front')
right_wheel_back = sim.getObject('/Revolute_joint_right_back')

# Get Robot Base Handle
try:
    robot_base_handle = sim.getObject('/Base')
    print("✅ Robot Base Handle Retrieved!")
except Exception as e:
    print(f"⚠ Warning: Could not retrieve robot base handle: {e}")
    robot_base_handle = None

# Function to get human position from CoppeliaSim
def get_human_position():
    if human_handle is None:
        return None
    try:
        position = sim.getObjectPosition(human_handle, -1)
        print(f"Raw human position: {position}")  # Debug output
        return position
    except Exception as e:
        print(f"⚠ Error retrieving human position: {e}")
        return None

# Function to get robot position and orientation from CoppeliaSim
def get_robot_state():
    if robot_base_handle is None:
        return [0, 0, 0], 0
    try:
        position = sim.getObjectPosition(robot_base_handle, -1)
        orientation = sim.getObjectOrientation(robot_base_handle, -1)  # Returns [roll, pitch, yaw]
        return position, orientation[2]  # Use yaw (z-axis rotation) as heading
    except Exception as e:
        print(f"⚠ Error retrieving robot state: {e}")
        return [0, 0, 0], 0

# Function to move the robot
def move_robot(left_speed, right_speed):
    max_speed = 2.5
    left_speed = max(min(left_speed, max_speed), -max_speed)
    right_speed = max(min(right_speed, max_speed), -max_speed)
    sim.setJointTargetVelocity(left_wheel_front, left_speed)
    sim.setJointTargetVelocity(left_wheel_back, left_speed)
    sim.setJointTargetVelocity(right_wheel_front, right_speed)
    sim.setJointTargetVelocity(right_wheel_back, right_speed)

# Function to calculate movement based on human position
def follow_human(robot_pos, robot_theta, human_pos):
    if human_pos is None:
        return 0, 0
    
    # Extract x, y coordinates (ignoring z for 2D movement)
    rx, ry, _ = robot_pos
    hx, hy, _ = human_pos
    dx = hx - rx
    dy = hy - ry
    distance = math.sqrt(dx*2 + dy*2)
    
    print(f"Distance to human: {distance:.2f}, dx: {dx:.2f}, dy: {dy:.2f}")  # Debug output
    
    if distance < 0.03:  # Stopping distance
        print("✅ Reached human, stopping.")
        return 0, 0
    
    # Calculate desired angle toward human
    target_angle = math.atan2(dy, dx)
    
    # Calculate angle error relative to robot's current orientation
    angle_error = (target_angle - robot_theta + math.pi) % (2 * math.pi) - math.pi
    print(f"Robot theta: {math.degrees(robot_theta):.2f}°, Target angle: {math.degrees(target_angle):.2f}°, Angle error: {math.degrees(angle_error):.2f}")
    
    # Dampen angular response with a threshold
    max_angular_speed = 1.0  # Reduced to minimize over-rotation
    if abs(angle_error) < 0.1:
        omega = angle_error * 0.3  # Further reduced for small errors
    else:
        omega = max(min(angle_error * 0.8, max_angular_speed), -max_angular_speed)  # Reduced amplification
    
    # Dynamic speed based on distance
    v = min(0.8 * (distance / 0.8), 0.8)
    
    # Differential drive
    wheel_base = 0.3
    v_l = v - omega * wheel_base / 2
    v_r = v + omega * wheel_base / 2
    
    print(f"v: {v:.2f}, omega: {omega:.2f}, v_l: {v_l:.2f}, v_r: {v_r:.2f}")
    return v_l, v_r

print("📸 Starting Human Following Robot...")

try:
    while True:
        # Get positions and orientation
        robot_pos, robot_theta = get_robot_state()
        human_pos = get_human_position()
        
        if human_pos is not None:
            print(f"👁 Human detected at: {human_pos}")
            v_l, v_r = follow_human(robot_pos, robot_theta, human_pos)
            move_robot(v_l, v_r)
        else:
            print("🔄 Human lost! Stopping movement...")
            move_robot(0, 0)
        
        time.sleep(0.1)
        
except Exception as e:
    print(f"⚠ Error in main loop: {e}")
finally:
    print("🛑 Stopping Simulation...")
    move_robot(0, 0)
    time.sleep(0.5)
    sim.stopSimulation()