# Gère le plateau de jeu (grille de cartes)
# Le Board (plateau) crée toutes les cartes, les mélange, et les pose sur la grille.

import random
from card import Card


class Board:
    def __init__(self, rows=4, cols=4, cheat_values=None):
        """
        Crée un plateau de jeu.
        - rows / cols : nombre de lignes et colonnes de la grille
        - cheat_values : si on fournit une liste de valeurs ici, la grille sera
          recréée EXACTEMENT dans cet ordre (sans mélange). C'est le mode triche !
        """
        self.rows = rows
        self.cols = cols
        self.cards = []
        # On lance la création du plateau, en passant les valeurs de triche si elles existent
        self.create_board(cheat_values)

    def create_board(self, cheat_values=None):
        """Génère la grille avec des paires de cartes mélangées (ou selon la config de triche)."""
        total_cards = self.rows * self.cols  # ex : 4 lignes × 4 cols = 16 cartes
        num_pairs = total_cards // 2         # ex : 16 cartes = 8 paires

        if cheat_values:
            # --- MODE TRICHE ---
            # On utilise directement la liste fournie (sauvegardée depuis une partie précédente).
            # Les cartes seront placées dans le même ordre qu'avant → même grille !
            values = cheat_values
        else:
            # --- MODE NORMAL ---
            # On crée une liste avec chaque valeur en double (pour les paires)
            values = []
            for i in range(1, num_pairs + 1):
                values.extend([i, i])  # ex : [1, 1, 2, 2, 3, 3, ...]
            # On mélange la liste aléatoirement pour que chaque partie soit différente
            random.shuffle(values)

        # On remplit la grille ligne par ligne avec les valeurs
        self.cards = []
        for row in range(self.rows):
            card_row = []  # une ligne de cartes
            for col in range(self.cols):
                index = row * self.cols + col  # position linéaire dans la liste values
                card = Card(values[index], col, row)  # on crée la carte avec sa valeur
                card_row.append(card)
            self.cards.append(card_row)

    def get_card(self, row, col):
        """Renvoie la carte à la position donnée, ou None si hors grille."""
        if 0 <= row < self.rows and 0 <= col < self.cols:
            return self.cards[row][col]
        return None  # position invalide (ne devrait normalement pas arriver)

    def all_matched(self):
        """Vérifie si toutes les cartes ont été trouvées (partie terminée ?)."""
        for row in self.cards:
            for card in row:
                if not card.is_matched:
                    return False  # au moins une carte non trouvée → partie pas finie
        return True  # toutes les cartes sont trouvées → victoire !