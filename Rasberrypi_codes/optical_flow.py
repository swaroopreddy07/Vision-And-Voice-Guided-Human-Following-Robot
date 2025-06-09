import cv2

def compute_optical_flow(prev_gray, next_gray, prev_points):
    next_points, status, error = cv2.calcOpticalFlowPyrLK(prev_gray, next_gray, prev_points, None)
    return next_points
