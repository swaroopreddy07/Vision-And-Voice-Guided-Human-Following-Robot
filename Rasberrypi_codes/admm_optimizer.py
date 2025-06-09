import numpy as np

class ADMM:
    def __init__(self, rho=1.0, max_iter=100):
        self.rho = rho
        self.max_iter = max_iter

    def optimize(self, current_position, target_position, speed_limits):
        x = current_position
        z = target_position
        u = np.zeros_like(x)

        for _ in range(self.max_iter):
            x_new = self.update_primal(x, z, u, speed_limits)
            u = self.update_dual(u, x_new, z)
            if np.linalg.norm(x_new - x) < 1e-6:
                break
            x = x_new
        return x_new

    def update_primal(self, x, z, u, speed_limits):
        step_size = 0.1
        proposed = x + step_size * (z - x + u)
        if speed_limits is not None:
            proposed = np.clip(proposed, speed_limits[0], speed_limits[1])
        return proposed

    def update_dual(self, u, x_new, z):
        return u + (x_new - z)
