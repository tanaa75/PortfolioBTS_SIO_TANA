# boutique/admin.py

from django.contrib import admin
from .models import Produit, Commande, LigneCommande


# Registre simple : Ajoute la gestion des "Produits" dans l'interface d'administration par défaut.
admin.site.register(Produit)

# TabularInline permet d'afficher les éléments "enfants" (LigneCommande)
# directement à l'intérieur de la page du parent (Commande) sous forme de tableau.
class LigneCommandeInline(admin.TabularInline):
    model = LigneCommande
    # Rend le prix non modifiable pour éviter de fausser l'historique comptable
    readonly_fields = ('prix_unitaire_au_moment',)
    # extra = 0 évite d'afficher des lignes vides inutiles par défaut
    extra = 0

# Le décorateur @admin.register personnalise l'affichage du modèle détaillé "Commande" dans l'interface
@admin.register(Commande)
class CommandeAdmin(admin.ModelAdmin):
    # list_display définit les colonnes affichées dans la liste globale des commandes
    list_display = ('id', 'client', 'date_commande', 'statut', 'code_retrait', 'get_cart_total')
    # list_filter ajoute un panneau latéral permettant de trier les résultats (par statut ou date)
    list_filter = ('statut', 'date_commande')
    # search_fields ajoute une barre de recherche fonctionnelle (recherche par code de retrait ou par pseudo du client)
    search_fields = ('code_retrait', 'client__username')
    # inlines intègre le tableau des lignes de commande directement dans la vue détaillée d'une commande
    inlines = [LigneCommandeInline]
    # readonly_fields empêche la modification manuelle du code et de la date (sécurité & intégrité)
    readonly_fields = ('code_retrait', 'date_commande')
