# 🤖 Autonomous Assistive Robot

An intelligent indoor robot that can **follow humans**, **respond to voice commands**, and be **remotely controlled via a web interface**. Designed for assistive roles in smart homes, hospitals, classrooms, and care facilities.

---

## 🚀 Project Overview

This project aims to build an **Autonomous Assistive Robot** that combines **computer vision**, **robotic control**, **voice recognition**, and a **web-based control interface**. The robot can:

- Detect and follow a human in real-time using a camera.
- Understand voice commands like "follow me" or "stop".
- Smoothly move without jerky motion using control algorithms.
- Be controlled remotely through a live web dashboard.

---

## 🎯 Key Features

- 🧠 **Human Detection**: Uses YOLOv5 to detect humans in real-time from the camera feed.
- 📍 **Human Tracking**: Uses Kalman Filter to predict and follow the person’s path.
- 📈 **Path Planning**: Employs ADMM optimization for smooth and stable motion.
- 🎛️ **PID Control**: Ensures stable movement and prevents overshooting or delay.
- 🎤 **Voice Command Recognition**: Recognizes commands like "follow me" and "stop" using FFT-based voice filtering.
- 🌐 **Web Interface**: Allows real-time monitoring and control from a remote device (PC/mobile).

---

## 🛠️ Technologies Used

| Technology/Tool | Description |
|-----------------|-------------|
| YOLOv5 | Real-time human detection using computer vision |
| Kalman Filter | Predictive tracking of human position |
| ADMM Optimizer | Optimal path generation for smooth motion |
| PID Controller | Maintains precise robot movement |
| FFT Filter | Removes noise from audio input |
| Raspberry Pi | Main controller to run all modules |
| CoppeliaSim | Simulation environment (for testing and development) |

---


