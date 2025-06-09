
from simple_pid import PID

class RobotPIDControl:
    def __init__(self):
        self.pid_steering = PID(0.5, 0.05, 0.01, setpoint=0)
        self.pid_steering.output_limits = (-100, 100)

        self.pid_speed = PID(0.2, 0.01, 0.01, setpoint=50)
        self.pid_speed.output_limits = (0, 100)

    def get_steering_adjustment(self, error):
        return self.pid_steering(error)

    def get_speed_adjustment(self, error):
        return self.pid_speed(error)
