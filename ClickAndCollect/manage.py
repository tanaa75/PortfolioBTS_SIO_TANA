#!/usr/bin/env python
"""
manage.py est l'utilitaire en ligne de commande de Django.
Il permet d'exécuter diverses tâches utiles pour le projet.
Par exemple: démarrer le serveur de test (`runserver`), ou migrer la BDD (`migrate`).
Pour info: tapez `python manage.py help` pour voir toutes les commandes possibles.
"""
import os
import sys


def main():
    """Fonction principale gérant les tâches administratives au lancement."""
    # Indique à Django où trouver le fichier "settings.py" avec toute la configuration du projet
    os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'config.settings')
    try:
        from django.core.management import execute_from_command_line
    except ImportError as exc:
        raise ImportError(
            "Impossible d'importer Django. Êtes-vous sûr qu'il est installé et "
            "disponible sur votre variable d'environnement PYTHONPATH ? Avez-vous "
            "oublié d'activer l'environnement virtuel ?"
        ) from exc
    # Exécute la commande passée dans la console/le terminal (sys.argv)
    execute_from_command_line(sys.argv)


if __name__ == '__main__':
    main()
