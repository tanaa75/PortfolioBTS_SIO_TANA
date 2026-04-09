# Gestion de la base de données SQLite pour les scores

import sqlite3


class Database:
    def __init__(self, db_name="scores.db"):
        self.connection = sqlite3.connect(db_name)
        self.cursor = self.connection.cursor()
        self.create_table()

    def create_table(self):
        """Crée la table des records si elle n'existe pas encore."""
        self.cursor.execute("""
            CREATE TABLE IF NOT EXISTS records (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                pseudo TEXT,
                niveau TEXT,
                theme TEXT,
                temps REAL,
                coups INTEGER
            )
        """)
        self.connection.commit()

    def ajouter_score(self, pseudo, niveau, theme, temps, coups):
        """Insère un nouveau score dans la base."""
        self.cursor.execute("""
            INSERT INTO records (pseudo, niveau, theme, temps, coups)
            VALUES (?, ?, ?, ?, ?)
        """, (pseudo, niveau, theme, temps, coups))
        self.connection.commit()

    def recuperer_top_scores(self, niveau, theme):
        """Récupère les 5 meilleurs scores pour un niveau et un thème donnés."""
        self.cursor.execute("""
            SELECT pseudo, temps, coups
            FROM records
            WHERE niveau = ? AND theme = ?
            ORDER BY coups ASC, temps ASC
            LIMIT 5
        """, (niveau, theme))
        return self.cursor.fetchall()

    def close(self):
        self.connection.close()