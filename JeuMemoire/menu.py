# Menu principal du jeu de mémoire - sélection du niveau, thème et accès aux records

import tkinter as tk
from tkinter import ttk
import sys
from database import Database
from game import Game


class MenuPrincipal:

    def __init__(self):
        self.window = tk.Tk()
        self.window.title("Jeu de Mémoire - Menu Principal")
        self.window.geometry("750x750")
        self.window.state('zoomed')  # Lancer en plein écran (maximisé)
        self.window.configure(bg="lightblue")

        self.selected_level = "Normal"
        self.selected_theme = "Chiffres"

        self.create_widgets()

    def create_widgets(self):
        """Construit l'interface du menu avec scroll."""

        # conteneur scrollable
        self.canvas = tk.Canvas(self.window, bg="lightblue")
        self.scrollbar = tk.Scrollbar(self.window, orient="vertical", command=self.canvas.yview)
        self.scrollable_frame = tk.Frame(self.canvas, bg="lightblue")

        self.scrollable_frame.bind(
            "<Configure>",
            lambda e: self.canvas.configure(scrollregion=self.canvas.bbox("all"))
        )

        self.canvas.create_window((0, 0), window=self.scrollable_frame, anchor="n")
        self.canvas.configure(yscrollcommand=self.scrollbar.set)

        def on_canvas_configure(event):
            self.canvas.itemconfig(self.canvas.find_all()[0], width=event.width)
        self.canvas.bind("<Configure>", on_canvas_configure)

        self.scrollbar.pack(side="right", fill="y")
        self.canvas.pack(side="left", fill="both", expand=True)

        def on_mousewheel(event):
            self.canvas.yview_scroll(int(-1 * (event.delta / 120)), "units")
        self.window.bind_all("<MouseWheel>", on_mousewheel)

        # titre
        tk.Label(self.scrollable_frame, text="🧠 JEU DE MÉMOIRE 🧠",
                 font=("Arial", 28, "bold"), fg="darkblue", bg="lightblue").pack(pady=30)

        tk.Label(self.scrollable_frame, text="Testez votre mémoire !",
                 font=("Arial", 14), fg="navy", bg="lightblue").pack(pady=10)

        # section difficulté
        diff_frame = tk.LabelFrame(self.scrollable_frame, text="🎯 NIVEAU DE DIFFICULTÉ",
                                   font=("Arial", 14, "bold"),
                                   fg="darkgreen", bg="lightblue", bd=2)
        diff_frame.pack(pady=20, padx=40, fill="x")

        self.level_var = tk.StringVar(value="Normal")

        levels = [
            ("🟢 Facile (3x4)", "Facile", "12 cartes - Parfait pour débuter"),
            ("🔵 Normal (4x4)", "Normal", "16 cartes - Équilibré"),
            ("🟠 Difficile (5x6)", "Difficile", "30 cartes - Pour experts"),
            ("🔴 Expert (6x6)", "Expert", "36 cartes - Maximum challenge!")
        ]

        for text, value, desc in levels:
            frame = tk.Frame(diff_frame, bg="lightblue")
            frame.pack(fill="x", pady=5, padx=10)

            tk.Radiobutton(frame, text=text, variable=self.level_var,
                           value=value, font=("Arial", 12, "bold"),
                           bg="lightblue", fg="darkgreen",
                           command=self.update_level).pack(anchor="w")

            tk.Label(frame, text=desc, font=("Arial", 10),
                     fg="gray", bg="lightblue").pack(anchor="w", padx=20)

        # section thème
        theme_frame = tk.LabelFrame(self.scrollable_frame, text="🎨 THÈME",
                                    font=("Arial", 14, "bold"),
                                    fg="purple", bg="lightblue", bd=2)
        theme_frame.pack(pady=20, padx=40, fill="x")

        self.theme_var = tk.StringVar(value="Chiffres")

        themes = [
            ("🔢 Chiffres", "Chiffres", "Classique avec nombres"),
            ("🍎 Fruits", "Fruits", "Délicieux fruits colorés"),
            ("⚽ Sports", "Sports", "Pour les sportifs!")
        ]

        for text, value, desc in themes:
            frame = tk.Frame(theme_frame, bg="lightblue")
            frame.pack(fill="x", pady=5, padx=10)

            tk.Radiobutton(frame, text=text, variable=self.theme_var,
                           value=value, font=("Arial", 12, "bold"),
                           bg="lightblue", fg="purple",
                           command=self.update_theme).pack(anchor="w")

            tk.Label(frame, text=desc, font=("Arial", 10),
                     fg="gray", bg="lightblue").pack(anchor="w", padx=20)

        # info sur le niveau sélectionné
        self.info_frame = tk.Frame(self.scrollable_frame, bg="lightyellow", bd=2, relief="solid")
        self.info_frame.pack(pady=20, padx=40, fill="x")

        self.info_label = tk.Label(self.info_frame,
                                   text="📊 Normal sélectionné - 16 cartes",
                                   font=("Arial", 12, "bold"),
                                   fg="darkorange", bg="lightyellow")
        self.info_label.pack(pady=10)

        # boutons d'action
        button_frame = tk.Frame(self.scrollable_frame, bg="lightblue")
        button_frame.pack(pady=30)

        tk.Button(button_frame, text="🚀 COMMENCER LE JEU",
                  font=("Arial", 16, "bold"), bg="green", fg="white",
                  padx=30, pady=10, command=self.start_game).pack(pady=10)

        tk.Button(button_frame, text="🏆 Voir les Records",
                  font=("Arial", 12), bg="gold", fg="black",
                  padx=20, pady=5, command=self.show_records).pack(pady=5)

        tk.Button(button_frame, text="❌ Quitter",
                  font=("Arial", 12), bg="red", fg="white",
                  padx=20, pady=5, command=self.quit_app).pack(pady=5)

    def quit_app(self):
        """Ferme complètement l'application."""
        self.window.destroy()
        sys.exit()

    def update_level(self):
        """Met à jour l'affichage quand on change de niveau."""
        level = self.level_var.get()
        self.selected_level = level

        level_info = {
            "Facile": ("🟢", "12 cartes", "3x4"),
            "Normal": ("🔵", "16 cartes", "4x4"),
            "Difficile": ("🟠", "30 cartes", "5x6"),
            "Expert": ("🔴", "36 cartes", "6x6")
        }

        emoji, cards, grid = level_info[level]
        self.info_label.config(text=f"📊 {level} sélectionné {emoji} - {cards} ({grid})")

    def update_theme(self):
        """Met à jour le thème sélectionné."""
        self.selected_theme = self.theme_var.get()

    def start_game(self):
        """Ferme le menu et lance le jeu avec les options choisies."""
        self.window.destroy()

        # dimensions de la grille selon le niveau
        level_config = {
            "Facile": (3, 4),
            "Normal": (4, 4),
            "Difficile": (5, 6),
            "Expert": (6, 6)
        }

        rows, cols = level_config[self.selected_level]
        jeu = Game(rows, cols, self.selected_level, self.selected_theme)
        jeu.run()

        # quand le jeu se ferme, on réouvre le menu
        nouveau_menu = MenuPrincipal()
        nouveau_menu.run()

    def show_records(self):
        """Ouvre une fenêtre avec le classement des meilleurs scores."""
        records_window = tk.Toplevel(self.window)
        records_window.title("🏆 Hall of Fame")
        records_window.geometry("500x600")
        records_window.configure(bg="lightyellow")

        tk.Label(records_window, text="🏆 HALL OF FAME 🏆",
                 font=("Arial", 20, "bold"), fg="gold", bg="lightyellow").pack(pady=15)

        # filtres niveau et thème
        filter_frame = tk.Frame(records_window, bg="lightyellow")
        filter_frame.pack(pady=10)

        tk.Label(filter_frame, text="Niveau :", bg="lightyellow",
                 font=("Arial", 10)).grid(row=0, column=0, padx=5)
        level_combo = ttk.Combobox(filter_frame,
                                   values=["Facile", "Normal", "Difficile", "Expert"],
                                   state="readonly", width=10)
        level_combo.current(1)
        level_combo.grid(row=0, column=1, padx=5)

        tk.Label(filter_frame, text="Thème :", bg="lightyellow",
                 font=("Arial", 10)).grid(row=0, column=2, padx=5)
        theme_combo = ttk.Combobox(filter_frame,
                                   values=["Chiffres", "Fruits", "Sports"],
                                   state="readonly", width=10)
        theme_combo.current(0)
        theme_combo.grid(row=0, column=3, padx=5)

        # zone d'affichage des résultats
        results_frame = tk.Frame(records_window, bg="white", bd=2, relief="sunken")
        results_frame.pack(fill="both", expand=True, padx=40, pady=20)

        def refresh_list(event=None):
            """Recharge la liste des scores selon les filtres."""
            for widget in results_frame.winfo_children():
                widget.destroy()

            choix_niveau = level_combo.get()
            choix_theme = theme_combo.get()

            # en-tête du tableau
            header = tk.Frame(results_frame, bg="orange")
            header.pack(fill="x")
            tk.Label(header, text="Joueur", width=20, font=("Arial", 10, "bold"), bg="orange").pack(side="left")
            tk.Label(header, text="Temps", width=10, font=("Arial", 10, "bold"), bg="orange").pack(side="left")
            tk.Label(header, text="Coups", width=10, font=("Arial", 10, "bold"), bg="orange").pack(side="left")

            # récup des scores en base
            db = Database()
            scores = db.recuperer_top_scores(choix_niveau, choix_theme)
            db.close()

            if not scores:
                tk.Label(results_frame, text="\nPas encore de record\npour cette catégorie !",
                         bg="white", fg="gray").pack()
            else:
                for pseudo, temps, coups in scores:
                    row = tk.Frame(results_frame, bg="white")
                    row.pack(fill="x", pady=2)
                    tk.Label(row, text=pseudo, width=20, bg="white").pack(side="left")
                    tk.Label(row, text=f"{temps}s", width=10, bg="white").pack(side="left")
                    tk.Label(row, text=str(coups), width=10, bg="white").pack(side="left")

        # rafraîchir quand on change de filtre
        level_combo.bind("<<ComboboxSelected>>", refresh_list)
        theme_combo.bind("<<ComboboxSelected>>", refresh_list)

        # premier affichage
        refresh_list()

        tk.Button(records_window, text="Fermer", command=records_window.destroy,
                  bg="tomato", fg="white").pack(pady=10)

    def run(self):
        self.window.mainloop()


if __name__ == "__main__":
    menu = MenuPrincipal()
    menu.run()
