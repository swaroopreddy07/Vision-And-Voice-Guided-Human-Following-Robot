from scipy.interpolate import CubicSpline
import numpy as np

class PathSmoothing:
    def __init__(self):
        self.positions = []

    def smooth_path(self, positions):
        if len(positions) < 4:
            return positions
        spline = CubicSpline(range(len(positions)), positions)
        smoothed = spline(np.arange(len(positions)))
        return smoothed
