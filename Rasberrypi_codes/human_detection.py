import torch
import cv2

class YoloHumanDetector:
    def __init__(self):
        self.model = torch.hub.load('ultralytics/yolov5', 'yolov5s', pretrained=True)

    def detect(self, frame):
        results = self.model(frame)
        detections = results.xyxy[0].cpu().numpy()

        for det in detections:
            x1, y1, x2, y2, conf, cls = det
            if int(cls) == 0 and conf > 0.5:
                x1, y1, x2, y2 = map(int, (x1, y1, x2, y2))
                return (x1, y1, x2 - x1, y2 - y1)
        return None
