from django.db import models
from django.contrib.auth.models import User
import uuid


class Produit(models.Model):
    """
    Modèle représentant un article en vente dans la boutique.
    Hérite de models.Model : Django créera une table correspondante dans la base de données.
    """
    # CharField pour un texte court. max_length est obligatoire.
    nom = models.CharField(max_length=200)
    
    # TextField pour un texte long (sans limite stricte).
    description = models.TextField()
    
    # DecimalField idéal pour les prix (évite les erreurs d'arrondi des nombres flottants).
    prix = models.DecimalField(max_digits=6, decimal_places=2)
    
    # IntegerField pour la quantité en stock, initialisé à 0.
    stock = models.IntegerField(default=0)
    
    # ImageField gère l'enregistrement de fichiers. 
    # 'upload_to' définit le sous-dossier (dans le dossier MEDIA).
    # blank=True (facultatif dans les formulaires), null=True (peut être vide en BDD).
    image = models.ImageField(upload_to='produits/', blank=True, null=True)

    def __str__(self):
        """
        Méthode magique définissant l'affichage de l'objet sous forme de chaîne de caractères
        (très utile dans l'interface d'administration Django).
        """
        return self.nom


class Commande(models.Model):
    """
    Modèle représentant la commande globale d'un client.
    Peut contenir plusieurs LigneCommande.
    """
    # STATUT_CHOICES définit les options d'un menu déroulant.
    # Format: ('VALEUR_EN_BDD', 'Texte affiché à l'utilisateur')
    STATUT_CHOICES = [
        ('EN_ATTENTE', 'En attente de préparation'),
        ('PRETE', 'Prête à être retirée'),
        ('RETIREE', 'Retirée'),
        ('ANNULEE', 'Annulée'),
    ]

    # ForeignKey = relation 1-à-N. Un utilisateur peut avoir plusieurs commandes.
    # on_delete=models.CASCADE : si l'utilisateur est supprimé, ses commandes le sont aussi.
    client = models.ForeignKey(User, on_delete=models.CASCADE)
    
    # auto_now_add=True enregistre la date et l'heure exactes lors de la création en base.
    date_commande = models.DateTimeField(auto_now_add=True)
    
    statut = models.CharField(
        max_length=10,
        choices=STATUT_CHOICES,
        default='EN_ATTENTE',
    )
    
    # Utilisation de uuid4 de Python pour créer un code de retrait (chaîne de caractères) unique.
    code_retrait = models.CharField(max_length=36, unique=True, default=uuid.uuid4)

    @property
    def get_cart_total(self):
        """
        Calcule le coût total de la commande.
        Le décorateur @property permet de l'appeler comme un attribut (ex: commande.get_cart_total).
        """
        # lignecommande_set est créé automatiquement par Django pour accéder aux lignes liées à cette commande.
        lignescommande = self.lignecommande_set.all()
        # Additionne les totaux de chaque ligne
        total = sum([item.get_total for item in lignescommande])
        return total

    def __str__(self):
        return f"Commande n°{self.id} par {self.client.username}"


class LigneCommande(models.Model):
    """
    Modèle de la table de jointure enrichie : relie une Commande à un Produit,
    en précisant la quantité et le prix au moment de l'achat.
    """
    commande = models.ForeignKey(Commande, on_delete=models.CASCADE)
    
    # on_delete=models.SET_NULL permet de garder l'historique de la commande
    # même si le produit est supprimé du catalogue (le champ deviendra null).
    produit = models.ForeignKey(Produit, on_delete=models.SET_NULL, null=True)
    
    quantite = models.IntegerField(default=1)
    
    # Archive du prix à l'instant de la commande (pour que les factures passées
    # ne changent pas si le prix du produit évolue dans le futur).
    prix_unitaire_au_moment = models.DecimalField(max_digits=6, decimal_places=2, default=0)

    @property
    def get_total(self):
        """Calcule le sous-total de cette ligne."""
        return self.prix_unitaire_au_moment * self.quantite

    # --- LA CORRECTION EST ICI ---
    def __str__(self):
        # Sécurité : on vérifie que le produit existe toujours 
        # (car la relation peut être null s'il a été supprimé)
        if self.produit:
            return f"{self.quantite} x {self.produit.nom} (Commande {self.commande.id})"
        return f"{self.quantite} x [Produit supprimé] (Commande {self.commande.id})"