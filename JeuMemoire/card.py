# Représente une carte du jeu de mémoire
# Chaque carte a une valeur (chiffre ou emoji), et deux états importants :
#   - est-ce qu'elle est visible (retournée) ?
#   - est-ce qu'elle fait partie d'une paire trouvée ?

class Card:
    def __init__(self, value, x=0, y=0):
        self.value = value       # la valeur de la carte, ex : 3, ou l'index d'un fruit

        self.is_visible = False  # False = face cachée ("?"), True = face visible (valeur affichée)
        self.is_matched = False  # False = paire pas encore trouvée, True = paire trouvée (carte définitivement visible)

        # --- Ajout pour le MODE AIDE ---
        # Si True, la carte est TOUJOURS visible, même si on la "flip" en sens inverse.
        # Cela permet à l'aide d'afficher des cartes sans qu'elles se recachent.
        self.is_permanently_visible = False

        self.x = x  # position colonne dans la grille
        self.y = y  # position ligne dans la grille

    def flip(self):
        """Retourne la carte (visible <-> cachée)."""
        # Si la carte est permanente (révélée par l'aide), on ne fait rien.
        # Elle doit rester visible quoi qu'il arrive.
        if getattr(self, 'is_permanently_visible', False):
            return
        # Sinon, on inverse son état : cachée devient visible, visible devient cachée.
        self.is_visible = not self.is_visible

    def match(self):
        """Marque la carte comme trouvée, elle reste visible."""
        self.is_matched = True   # la carte est officiellement trouvée
        self.is_visible = True   # on s'assure qu'elle reste face visible