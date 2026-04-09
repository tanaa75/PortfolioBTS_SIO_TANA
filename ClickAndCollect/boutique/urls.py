from django.urls import path
from . import views

# urlpatterns est une liste attendue par Django qui définit les "routes" (URLs) de l'application.
# Chaque path() relie une adresse URL (ce que l'utilisateur tape) à une vue (fonction dans views.py).
urlpatterns = [
    # --- Accueil / Catalogue ---
    # URL vide '' correspond à la racine de l'application boutique (ex: monsite.com/boutique/)
    # name='...' permet de générer dynamiquement l'URL dans les templates HTML (ex: {% url 'liste_produits' %})
    path('', views.liste_produits, name='liste_produits'),

    # Nouvelle route pour la fiche produit détaillée
    # <int:produit_id> est un paramètre dynamique de type entier (integer) passé à la vue
    path('produit/<int:produit_id>/', views.detail_produit, name='detail_produit'),

    # --- Authentification ---
    # .as_view() est obligatoire pour lier une URL à une vue basée sur une classe (Class-Based View)
    path('register/', views.SignUpView.as_view(), name='register'),

    # --- Gestion du Panier ---
    path('panier/', views.voir_panier, name='voir_panier'),
    path('ajouter/<int:produit_id>/', views.ajouter_au_panier, name='ajouter_au_panier'),

    # CORRECTION : On utilise <str:cle> (string) pour accepter les clés complexes avec pointures (ex: "6_42.5")
    # L'URL passe 2 paramètres à la vue (la clé de l'article et l'action 'plus'/'moins'/'supprimer')
    path('modifier/<str:cle>/<str:action>/', views.modifier_panier, name='modifier_panier'),

    path('vider/', views.vider_panier, name='vider_panier'),

    # --- Processus de Commande ---
    path('checkout/', views.checkout, name='checkout'),
    path('confirmation/<int:commande_id>/', views.confirmation_commande, name='confirmation_commande'),
    path('commandes/', views.historique_commandes, name='historique_commandes'),
]