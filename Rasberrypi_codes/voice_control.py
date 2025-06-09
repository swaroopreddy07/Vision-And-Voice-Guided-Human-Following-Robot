import pvporcupine
import pyaudio
import struct
import threading
import numpy as np
from fft_filter import FFTFilter

class VoiceController:
    def __init__(self):
        self.keyword_paths = [
            "/home/SwaroopReddy/myenv/NEW_UPDATED_9_04_2025/Follow-me_en_raspberry-pi_v3_0_0.ppn",
            "/home/SwaroopReddy/myenv/NEW_UPDATED_9_04_2025/Stop-there_en_raspberry-pi_v3_0_0.ppn"
        ]
        self.porcupine = pvporcupine.create(
            access_key="LWa3HRAwDTLY4EmXsmUwQgIVxBtqVsRhzCgG92rAfmPadaDnQW7eDA==",
            keyword_paths=self.keyword_paths,
            sensitivities=[0.5, 0.5]
        )
        self.fft = FFTFilter()
        self.pa = pyaudio.PyAudio()
        self.audio_stream = self.pa.open(
            rate=self.porcupine.sample_rate,
            channels=1,
            format=pyaudio.paInt16,
            input=True,
            frames_per_buffer=self.porcupine.frame_length)
        self.commands = []
        self.listening = True
        self.thread = threading.Thread(target=self._listen)
        self.thread.daemon = True
        self.thread.start()

    def _listen(self):
        while self.listening:
            try:
                pcm = self.audio_stream.read(self.porcupine.frame_length, exception_on_overflow=False)
            except IOError:
                continue
            pcm = struct.unpack_from("h" * self.porcupine.frame_length, pcm)
            filtered_pcm = self.fft.filter(np.array(pcm))
            filtered_pcm = filtered_pcm.astype(np.int16).tolist()
            result = self.porcupine.process(filtered_pcm)
            if result >= 0:
                command = self._get_command_from_index(result)
                print("Detected command:", command)
                self.commands.append(command)

    def _get_command_from_index(self, index):
        if index == 0:
            return "follow me"
        elif index == 1:
            return "stop"
        return ""

    def get_command(self):
        if self.commands:
            return self.commands.pop(0)
        return None

    def stop_listening(self):
        self.listening = False
        self.thread.join()
        self.audio_stream.close()
        self.pa.terminate()
        self.porcupine.delete()
