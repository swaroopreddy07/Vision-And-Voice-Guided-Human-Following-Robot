import RPi.GPIO as GPIO

class MotorController:
    def __init__(self):
        GPIO.setmode(GPIO.BCM)
        
        # Left motor pin assignments
        self.LEFT_IN1 = 17
        self.LEFT_IN2 = 18
        self.LEFT_ENA = 24  # PWM-capable pin for left motor speed
        
        # Right motor pin assignments
        self.RIGHT_IN1 = 22
        self.RIGHT_IN2 = 23
        self.RIGHT_ENB = 25  # PWM-capable pin for right motor speed
        
        # Setup direction pins as outputs
        GPIO.setup(self.LEFT_IN1, GPIO.OUT)
        GPIO.setup(self.LEFT_IN2, GPIO.OUT)
        GPIO.setup(self.RIGHT_IN1, GPIO.OUT)
        GPIO.setup(self.RIGHT_IN2, GPIO.OUT)
        
        # Setup enable pins for PWM
        GPIO.setup(self.LEFT_ENA, GPIO.OUT)
        GPIO.setup(self.RIGHT_ENB, GPIO.OUT)
        
        # Create PWM instances on ENA and ENB at 100 Hz frequency
        self.left_pwm = GPIO.PWM(self.LEFT_ENA, 100)
        self.right_pwm = GPIO.PWM(self.RIGHT_ENB, 100)
        
        # Start PWM at 0 duty cycle (motors off)
        self.left_pwm.start(0)
        self.right_pwm.start(0)
    
    def move(self, angle, base_speed):
        GPIO.output(self.LEFT_IN1, GPIO.HIGH)
        GPIO.output(self.LEFT_IN2, GPIO.LOW)
        GPIO.output(self.RIGHT_IN1, GPIO.HIGH)
        GPIO.output(self.RIGHT_IN2, GPIO.LOW)
        
        left_speed = max(min(base_speed + angle, 100), 0)
        right_speed = max(min(base_speed - angle, 100), 0)
        
        self.left_pwm.ChangeDutyCycle(left_speed)
        self.right_pwm.ChangeDutyCycle(right_speed)
        print(f"Moving: left_speed={left_speed}, right_speed={right_speed}")
        
    def stop(self):
        self.left_pwm.ChangeDutyCycle(0)
        self.right_pwm.ChangeDutyCycle(0)
        GPIO.output(self.LEFT_IN1, GPIO.LOW)
        GPIO.output(self.LEFT_IN2, GPIO.LOW)
        GPIO.output(self.RIGHT_IN1, GPIO.LOW)
        GPIO.output(self.RIGHT_IN2, GPIO.LOW)
        print("Motors stopped")
        
    def search(self):
        GPIO.output(self.LEFT_IN1, GPIO.HIGH)
        GPIO.output(self.LEFT_IN2, GPIO.LOW)
        GPIO.output(self.RIGHT_IN1, GPIO.LOW)
        GPIO.output(self.RIGHT_IN2, GPIO.HIGH)
        self.left_pwm.ChangeDutyCycle(30)
        self.right_pwm.ChangeDutyCycle(30)
        print("Searching for human")
