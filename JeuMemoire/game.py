# Classe principale du jeu - gère la fenêtre de jeu, les cartes et la logique

import tkinter as tk
from tkinter import messagebox, simpledialog
import time
from board import Board
from sounds import create_sound_manager
from database import Database


class Game:

    def __init__(self, rows=4, cols=4, level="Normal", theme="Chiffres"):
        # fenêtre principale
        self.window = tk.Tk()
        self.window.title(f"Jeu de Mémoire - {level} ({theme})")

        # taille adaptée au nombre de cartes
        width = 200 + cols * 130
        height = 300 + rows * 120
        self.window.geometry(f"{width}x{height}")
        self.window.state('zoomed')  # Lancer en plein écran (maximisé)

        # paramètres de la partie
        self.rows = rows
        self.cols = cols
        self.level = level
        self.theme = theme

        # plateau et variables de jeu
        # cheat_config stocke la liste des valeurs sauvegardées (pour rejouer la même grille)
        self.cheat_config = None
        self.board = Board(rows, cols)  # on crée un plateau aléatoire normal au départ
        self.selected_cards = []  # liste des cartes retournées par le joueur (max 2 à la fois)
        self.moves = 0
        self.start_time = time.time()
        self.best_moves = None
        self.best_time = None

        # sons
        self.sound_manager = create_sound_manager()

        self.create_widgets()

    def get_card_display(self, value):
        """Renvoie le texte à afficher sur la carte selon le thème."""
        if self.theme == "Fruits":
            fruits = ["🍎", "🍌", "🍊", "🍇", "🍓", "🥝", "🍑", "🍒",
                      "🥭", "🍍", "🥥", "🍈", "🍉", "🫐", "🍅", "🥑",
                      "🍋", "🍑", "🥭", "🍊"]
            return fruits[value - 1] if value <= len(fruits) else str(value)

        elif self.theme == "Sports":
            sports = ["⚽", "🏀", "🏈", "⚾", "🎾", "🏐", "🏓", "🏸",
                      "🏒", "🏑", "🎱", "🏊", "🏃", "🚴", "🤸", "🏋️",
                      "🤾", "🤺", "🏇", "🧗"]
            return sports[value - 1] if value <= len(sports) else str(value)

        else:
            return str(value)

    def create_widgets(self):
        """Met en place toute l'interface : titre, stats, grille et boutons."""

        # conteneur scrollable (Canvas + Scrollbar)
        self.canvas = tk.Canvas(self.window)
        self.scrollbar = tk.Scrollbar(self.window, orient="vertical", command=self.canvas.yview)
        self.scrollable_frame = tk.Frame(self.canvas)

        # maj de la zone de scroll quand le contenu change
        self.scrollable_frame.bind(
            "<Configure>",
            lambda e: self.canvas.configure(scrollregion=self.canvas.bbox("all"))
        )

        self.canvas.create_window((0, 0), window=self.scrollable_frame, anchor="n")
        self.canvas.configure(yscrollcommand=self.scrollbar.set)

        # pour que le contenu prenne toute la largeur
        def on_canvas_configure(event):
            self.canvas.itemconfig(self.canvas.find_all()[0], width=event.width)
        self.canvas.bind("<Configure>", on_canvas_configure)

        self.scrollbar.pack(side="right", fill="y")
        self.canvas.pack(side="left", fill="both", expand=True)

        # scroll à la molette
        def on_mousewheel(event):
            self.canvas.yview_scroll(int(-1 * (event.delta / 120)), "units")
        self.window.bind_all("<MouseWheel>", on_mousewheel)

        # titre
        tk.Label(self.scrollable_frame, text=f"JEU DE MÉMOIRE - {self.level.upper()}",
                 font=("Arial", 26, "bold"), fg="darkblue").pack(pady=15)

        tk.Label(self.scrollable_frame, text=f"Thème: {self.theme}",
                 font=("Arial", 15), fg="purple").pack(pady=5)

        # zone stats (partie en cours + records)
        stats_frame = tk.Frame(self.scrollable_frame)
        stats_frame.pack(pady=10)

        # stats partie actuelle
        current_frame = tk.Frame(stats_frame)
        current_frame.pack(side=tk.LEFT, padx=30)

        tk.Label(current_frame, text="PARTIE ACTUELLE",
                 font=("Arial", 14, "bold"), fg="darkgreen").pack()
        self.moves_label = tk.Label(current_frame, text="Coups: 0", font=("Arial", 13))
        self.moves_label.pack(pady=3)
        self.time_label = tk.Label(current_frame, text="Temps: 0s", font=("Arial", 13))
        self.time_label.pack(pady=3)

        # stats records
        records_frame = tk.Frame(stats_frame)
        records_frame.pack(side=tk.RIGHT, padx=30)

        tk.Label(records_frame, text=f"RECORDS {self.level.upper()}",
                 font=("Arial", 14, "bold"), fg="darkred").pack()
        self.best_moves_label = tk.Label(records_frame, text="Moins de coups: --", font=("Arial", 13))
        self.best_moves_label.pack(pady=3)
        self.best_time_label = tk.Label(records_frame, text="Meilleur temps: --", font=("Arial", 13))
        self.best_time_label.pack(pady=3)

        # séparateur
        tk.Frame(self.scrollable_frame, height=2, bg="gray").pack(fill=tk.X, padx=50, pady=15)

        # grille de cartes
        self.game_frame = tk.Frame(self.scrollable_frame)
        self.game_frame.pack(pady=25)

        self.buttons = []
        for row in range(self.rows):
            button_row = []
            for col in range(self.cols):
                btn = tk.Button(self.game_frame, text="?", width=8, height=3,
                                font=("Arial", 22, "bold"), bg="lightgray",
                                command=lambda r=row, c=col: self.card_clicked(r, c))
                btn.grid(row=row, column=col, padx=5, pady=5)
                button_row.append(btn)
            self.buttons.append(button_row)

        # boutons en bas
        button_frame = tk.Frame(self.scrollable_frame)
        button_frame.pack(pady=25)

        tk.Button(button_frame, text="🔄 Recommencer", command=self.restart_game,
                  font=("Arial", 13), bg="lightblue").pack(side=tk.LEFT, padx=12)
        tk.Button(button_frame, text="🏆 Reset Records", command=self.reset_records,
                  font=("Arial", 13), bg="orange").pack(side=tk.LEFT, padx=12)
        tk.Button(button_frame, text="🏠 Menu Principal", command=self.return_to_menu,
                  font=("Arial", 13), bg="lightgreen").pack(side=tk.LEFT, padx=12)

        self.sound_btn = tk.Button(button_frame, text="🔊 Sons ON", command=self.toggle_sound,
                                   font=("Arial", 12), bg="yellow")
        self.sound_btn.pack(side=tk.LEFT, padx=8)
        
        # Bouton d'aide étendue : permet de révéler N cartes au choix
        tk.Button(button_frame, text="💡 Aide", command=self.activate_help,
                  font=("Arial", 12), bg="pink").pack(side=tk.LEFT, padx=8)

        # --- BOUTON TRICHE SECRET ---
        # Ce bouton est intentionnellement invisible : même couleur que le fond (lightblue),
        # sans bordure (bd=0), positionné tout en haut à gauche à x=2, y=2.
        # Seul quelqu'un qui sait qu'il existe peut le trouver et cliquer dessus.
        # Il ouvre un petit menu pour sauvegarder ou recharger la disposition des cartes.
        self.cheat_btn = tk.Button(self.window, text=".", font=("Arial", 8), bg="lightblue", fg="lightblue",
                                   bd=0, activebackground="lightblue", activeforeground="lightblue",
                                   command=self.open_cheat_menu)
        self.cheat_btn.place(x=2, y=2)  # placé en pixels absolus, pas dans un pack/grid

        # lance le chronomètre (mis à jour toutes les secondes)
        self.update_time()

    def card_clicked(self, row, col):
        """Gère le clic sur une carte : la retourne et vérifie si on a une paire."""
        card = self.board.get_card(row, col)

        # Garde-fous avant d'autoriser le clic :
        # - la carte n'existe pas (hors grille)
        # - la carte est déjà une paire trouvée (is_matched)
        # - le joueur a déjà 2 cartes retournées en attente de comparaison
        if card is None or card.is_matched or len(self.selected_cards) >= 2:
            return

        # Empêche de cliquer deux fois sur la MÊME carte (ex: double-clic rapide)
        # On vérifie si la carte est déjà dans la liste des cartes sélectionnées
        if any(c[0] == card for c in self.selected_cards):
            return

        # Règle spéciale pour les cartes d'AIDE (is_permanently_visible = True) :
        # Une carte d'aide est déjà visible, mais le joueur PEUT l'utiliser pour former une paire.
        # On bloque uniquement les cartes visibles "normales" (ex : la 1ère carte qu'on vient de retourner)
        # pour éviter de la re-cliquer avant la comparaison.
        if card.is_visible and not getattr(card, 'is_permanently_visible', False):
            return

        # On retourne la carte (flip() ne fera rien sur les cartes permanentes, c'est voulu)
        card.flip()
        # On affiche la valeur (chiffre, fruit ou sport selon le thème)
        display_text = self.get_card_display(card.value)
        self.buttons[row][col].config(text=display_text, bg="lightblue", fg="darkblue")

        # On ajoute cette carte à la sélection en cours (avec ses coordonnées pour l'affichage)
        self.selected_cards.append((card, row, col))
        self.sound_manager.play_sound("click")  # petit son de clic

        # Quand on a 2 cartes retournées, on attend 1 seconde puis on les compare
        # (le délai permet au joueur de voir les 2 cartes avant qu'elles se recachent)
        if len(self.selected_cards) == 2:
            self.window.after(1000, self.check_match)

    def check_match(self):
        """Compare les 2 cartes retournées."""
        self.moves += 1
        self.moves_label.config(text=f"Coups: {self.moves}")

        card1, r1, c1 = self.selected_cards[0]
        card2, r2, c2 = self.selected_cards[1]

        if card1.value == card2.value:
            # paire trouvée
            card1.match()
            card2.match()
            # On colore les deux boutons en vert pour indiquer visuellement la paire trouvée
            for r, c, val in [(r1, c1, card1.value), (r2, c2, card2.value)]:
                self.buttons[r][c].config(text=self.get_card_display(val),
                                          bg="lightgreen", fg="darkgreen")
            self.sound_manager.play_sound("match")

            # On vérifie si toutes les cartes sont maintenant trouvées → victoire ?
            if self.board.all_matched():
                self.game_won()
        else:
            # Mauvaise paire → on recache les deux cartes (avec un délai d'1 seconde déjà passé)
            card1.flip()  # flip() remet is_visible à False (sauf si la carte est permanente)
            card2.flip()
            for card, r, c in [(card1, r1, c1), (card2, r2, c2)]:
                if not getattr(card, 'is_permanently_visible', False):
                    # Carte normale : on remet le "?" gris
                    self.buttons[r][c].config(text="?", bg="lightgray", fg="black")
                else:
                    # Carte d'aide : elle reste visible, on remet juste la couleur bleue d'aide
                    self.buttons[r][c].config(bg="lightblue")
            self.sound_manager.play_sound("no_match")

        self.selected_cards = []

    def update_time(self):
        """Met à jour le chronomètre toutes les secondes."""
        elapsed = int(time.time() - self.start_time)
        self.time_label.config(text=f"Temps: {elapsed}s")
        self.window.after(1000, self.update_time)

    def game_won(self):
        """Gère la victoire : affiche le message, demande le pseudo et enregistre le score."""
        elapsed = int(time.time() - self.start_time)
        self.sound_manager.play_sound("victory")

        msg = (f"🎉 FÉLICITATIONS! 🎉\n\n"
               f"Niveau: {self.level}\n"
               f"Temps: {elapsed}s\n"
               f"Coups: {self.moves}")
        messagebox.showinfo("Victoire!", msg)

        # on demande le pseudo pour le classement
        pseudo = simpledialog.askstring("Nouveau Record", "Entrez votre pseudo pour le classement :")

        if pseudo:
            try:
                db = Database()
                db.ajouter_score(pseudo, self.level, self.theme, elapsed, self.moves)
                db.close()
                messagebox.showinfo("Sauvegarde", "Score enregistré !")
            except Exception as e:
                messagebox.showerror("Erreur BDD", f"Impossible de sauvegarder : {e}")

        self.return_to_menu()

    def update_records_display(self):
        """Rafraîchit l'affichage des records."""
        if self.best_moves:
            self.best_moves_label.config(text=f"Moins de coups: {self.best_moves}")
        if self.best_time:
            self.best_time_label.config(text=f"Meilleur temps: {self.best_time}s")

    def restart_game(self, use_cheat=False):
        """
        Relance une nouvelle partie (même niveau, même thème).
        Si use_cheat=True ET qu'une config est sauvegardée, on recrée EXACTEMENT la même grille.
        Sinon, on génère une nouvelle grille aléatoire.
        """
        if use_cheat and self.cheat_config:
            # --- MODE TRICHE ACTIVÉ ---
            # On passe les valeurs sauvegardées au Board : les cartes seront placées dans le même ordre
            self.board = Board(self.rows, self.cols, cheat_values=self.cheat_config)
        else:
            # --- MODE NORMAL ---
            # Nouvelle grille aléatoire
            self.board = Board(self.rows, self.cols)

        # Remise à zéro de toutes les variables de la partie
        self.selected_cards = []       # on vide la sélection en cours
        self.moves = 0                 # compteur de coups à 0
        self.start_time = time.time()  # on relance le chronomètre
        self.moves_label.config(text="Coups: 0")

        # On remet tous les boutons à l'état initial : "?" gris
        for row in range(self.rows):
            for col in range(self.cols):
                self.buttons[row][col].config(text="?", bg="lightgray", fg="black")

    def reset_records(self):
        """Remet les records à zéro après confirmation."""
        if messagebox.askyesno("Reset Records", f"Effacer les records du niveau {self.level} ?"):
            self.best_moves = None
            self.best_time = None
            self.best_moves_label.config(text="Moins de coups: --")
            self.best_time_label.config(text="Meilleur temps: --")
            messagebox.showinfo("Records effacés", "Les records ont été remis à zéro !")

    def return_to_menu(self):
        """Ferme le jeu et retourne au menu."""
        self.window.destroy()
        from menu import MenuPrincipal
        MenuPrincipal().run()

    def activate_help(self):
        """
        Mode Aide Étendu : révèle N cartes choisies par l'utilisateur de manière permanente.
        Ces cartes restent visibles pendant toute la partie et peuvent être utilisées
        pour former des paires, sans se recacher.
        """
        import random

        # On parcourt toutes les cartes du plateau pour lister celles qui sont encore cachées
        # (on exclut les cartes déjà trouvées et celles déjà révélées par l'aide)
        hidden_cards = []
        for row in range(self.rows):
            for col in range(self.cols):
                card = self.board.get_card(row, col)
                if not card.is_matched and not getattr(card, 'is_permanently_visible', False):
                    hidden_cards.append((row, col, card))

        # Si toutes les cartes sont déjà visibles ou trouvées, rien à faire
        if not hidden_cards:
            messagebox.showinfo("Aide", "Toutes les cartes possibles sont déjà visibles ou trouvées.")
            return

        # On demande à l'utilisateur combien de cartes il veut révéler
        # La valeur N doit être entre 1 et le nombre total de cartes encore cachées
        max_n = len(hidden_cards)
        n = simpledialog.askinteger(
            "Aide Étendue",
            f"Combien de cartes voulez-vous révéler ? (1 à {max_n})",
            minvalue=1,
            maxvalue=max_n
        )

        if n:
            # On tire N cartes au hasard parmi les cartes cachées (sans doublons grâce à random.sample)
            chosen = random.sample(hidden_cards, n)
            for r, c, card in chosen:
                # On marque la carte comme "permanente" : elle ne pourra plus se recacher
                card.is_permanently_visible = True
                card.is_visible = True  # on la force visible immédiatement
                display_text = self.get_card_display(card.value)  # texte à afficher (chiffre ou emoji)
                # Mise à jour visuelle du bouton : fond bleu clair pour distinguer les cartes d'aide
                self.buttons[r][c].config(text=display_text, bg="lightblue", fg="darkblue")

    def open_cheat_menu(self):
        """
        Ouvre le menu SECRET de triche (accessible via le bouton invisible en haut à gauche).
        Ce menu propose deux actions :
          1. Sauvegarder config : mémorise l'ordre exact des cartes dans un fichier JSON
          2. Charger config : recrée la même grille à l'identique pour rejouer sans surprise
        """
        import json
        import os

        # Création d'un menu contextuel (pop-up qui s'affiche là où est la souris)
        menu = tk.Menu(self.window, tearoff=0)

        def save_config():
            """
            Parcourt toute la grille et enregistre les valeurs des cartes dans l'ordre
            dans un fichier cheat_config.json (dans le dossier du projet).
            """
            values = []
            for row in range(self.rows):
                for col in range(self.cols):
                    # On récupère la valeur de chaque carte (dans l'ordre ligne par ligne)
                    values.append(self.board.get_card(row, col).value)

            # On sauvegarde en JSON : la taille de la grille + la liste des valeurs dans l'ordre
            with open("cheat_config.json", "w") as f:
                json.dump({"rows": self.rows, "cols": self.cols, "values": values}, f)
            print("Config sauvegardée secrètement.")  # message discret dans la console

        def load_config():
            """
            Lit le fichier cheat_config.json (si il existe) et relance la partie
            avec exactement la même disposition de cartes qu'au moment de la sauvegarde.
            """
            if os.path.exists("cheat_config.json"):
                with open("cheat_config.json", "r") as f:
                    data = json.load(f)  # on relit le fichier JSON

                # Vérification de compatibilité : la config sauvegardée doit correspondre
                # au même niveau (même nombre de lignes et colonnes) sinon ça ne marchera pas
                if data.get("rows") == self.rows and data.get("cols") == self.cols:
                    self.cheat_config = data["values"]  # on stocke les valeurs dans l'attribut
                    self.restart_game(use_cheat=True)    # on relance en mode triche !
                    print("Config chargée secrètement et partie relancée.")
                else:
                    print("La config ne correspond pas aux dimensions de ce niveau.")
            else:
                print("Aucune configuration sauvegardée.")

        # On ajoute les deux options au menu
        menu.add_command(label="Sauvegarder config", command=save_config)
        menu.add_command(label="Charger config", command=load_config)

        # On affiche le menu là où se trouve la souris au moment du clic
        x = self.window.winfo_pointerx()
        y = self.window.winfo_pointery()
        menu.post(x, y)

    def toggle_sound(self):
        """Active/désactive les sons."""
        enabled = self.sound_manager.toggle_sounds()
        if enabled:
            self.sound_btn.config(text="🔊 Sons ON", bg="yellow")
        else:
            self.sound_btn.config(text="🔇 Sons OFF", bg="gray")

    def run(self):
        self.window.mainloop()