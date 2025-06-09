import numpy as np

class FFTFilter:
    def __init__(self, threshold=10):
        self.threshold = threshold  # Frequency threshold for filtering

    def filter(self, signal):
        """
        Apply FFT to filter out high-frequency noise from the signal.
        """
        # Apply FFT
        fft_signal = np.fft.fft(signal)
        frequencies = np.fft.fftfreq(len(signal))

        # Remove high-frequency noise by zeroing out high frequencies
        fft_signal[np.abs(frequencies) > self.threshold] = 0

        # Apply inverse FFT to get the filtered signal back
        filtered_signal = np.fft.ifft(fft_signal)
        return np.real(filtered_signal)
