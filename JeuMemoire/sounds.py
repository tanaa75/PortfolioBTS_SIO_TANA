# Gestion des sons du jeu (bips système)

import threading
import time


class SoundManager:
    """Gestionnaire de sons basique avec des bips console."""

    def __init__(self):
        self.sounds_enabled = True

    def play_sound(self, sound_type):
        """Lance le son dans un thread pour pas bloquer le jeu."""
        if not self.sounds_enabled:
            return
        threading.Thread(target=self._play_beep, args=(sound_type,), daemon=True).start()

    def _play_beep(self, sound_type):
        """Joue un pattern de bips selon le type d'événement."""
        try:
            if sound_type == "click":
                print('\a')
            elif sound_type == "match":
                print('\a')
                time.sleep(0.1)
                print('\a')
            elif sound_type == "no_match":
                for _ in range(3):
                    print('\a')
                    time.sleep(0.05)
            elif sound_type == "victory":
                for _ in range(5):
                    print('\a')
                    time.sleep(0.15)
        except Exception:
            pass

    def toggle_sounds(self):
        """Active ou désactive les sons."""
        self.sounds_enabled = not self.sounds_enabled
        return self.sounds_enabled


# Version améliorée pour Windows avec winsound
try:
    import winsound

    class WindowsSoundManager(SoundManager):
        """Utilise winsound pour avoir de vrais sons sur Windows."""

        def _play_beep(self, sound_type):
            try:
                if sound_type == "click":
                    winsound.Beep(800, 100)
                elif sound_type == "match":
                    winsound.Beep(1000, 150)
                    time.sleep(0.05)
                    winsound.Beep(1200, 150)
                elif sound_type == "no_match":
                    winsound.Beep(400, 300)
                elif sound_type == "victory":
                    # petite mélodie montante pour la victoire
                    notes = [523, 587, 659, 698, 784]
                    for note in notes:
                        winsound.Beep(note, 200)
                        time.sleep(0.1)
            except Exception:
                super()._play_beep(sound_type)

    def create_sound_manager():
        try:
            return WindowsSoundManager()
        except Exception:
            return SoundManager()

except ImportError:
    # si winsound n'est pas dispo (Linux/Mac), on reste sur le basique
    def create_sound_manager():
        return SoundManager()