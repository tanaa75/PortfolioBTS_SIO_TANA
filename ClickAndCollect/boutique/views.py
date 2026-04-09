from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from django.contrib.auth.forms import UserCreationForm
from django.views import generic
from django.urls import reverse_lazy
from django.contrib import messages
import uuid

# Import des modèles locaux
from .models import Produit, Commande, LigneCommande

# --- 1. AUTHENTIFICATION ---
class SignUpView(generic.CreateView):
    """
    Vue basée sur une classe (Class-Based View) pour gérer l'inscription des utilisateurs.
    Elle automatise la création d'un utilisateur en base de données.
    """
    # Utilisation du formulaire de création d'utilisateur fourni par Django
    form_class = UserCreationForm
    # reverse_lazy permet d'attendre que la configuration soit complètement chargée 
    # avant de résoudre l'URL de redirection 'login' après une inscription réussie.
    success_url = reverse_lazy('login')
    # Définition du template HTML à afficher
    template_name = 'registration/register.html'

    def get_form(self, form_class=None):
        """
        Surcharge de la méthode get_form pour personnaliser le champ 'username'.
        """
        # Récupère le formulaire initial généré par la classe parente
        form = super().get_form(form_class)
        # On limite l'attribut HTML 'maxlength' à 25 caractères pour que le navigateur bloque la saisie
        form.fields['username'].widget.attrs.update({'maxlength': '25'})
        # On ajoute une petite aide pour guider l'utilisateur sous le champ de saisie
        form.fields['username'].help_text = "25 caractères maximum."
        return form

# --- 2. CATALOGUE ---
def liste_produits(request):
    """
    Vue basée sur une fonction (Function-Based View).
    Affiche tous les produits ayant du stock.
    """
    # Requête ORM : Sélectionne tous les produits dont le stock est strictement supérieur à 0 (__gt = greater than),
    # et les trie par ordre alphabétique de leur nom.
    produits = Produit.objects.filter(stock__gt=0).order_by('nom')
    # Rend le template en lui passant la variable 'produits' dans le contexte (dictionnaire)
    return render(request, 'boutique/liste_produits.html', {'produits': produits})

def detail_produit(request, produit_id):
    """
    Affiche la fiche détaillée d'un produit spécifique, avec génération des tailles disponibles.
    Le paramètre produit_id provient de l'URL.
    """
    # get_object_or_404 retourne l'objet désiré, ou renvoie une erreur 404 (Page non trouvée) si l'ID n'existe pas en base.
    produit = get_object_or_404(Produit, id=produit_id)

    # Logique métier : Génération dynamique d'une liste de pointures de chaussures (de 38.5 à 45)
    tailles = []
    current_size = 38.5
    while current_size <= 45:
        # Si c'est un nombre entier (ex: 39.0), on le convertit en int pour l'affichage ("39" au lieu de "39.0")
        if current_size % 1 == 0:
            tailles.append(int(current_size))
        else:
            tailles.append(current_size)
        current_size += 0.5

    # Envoi du produit ET de la liste des tailles générée au template
    return render(request, 'boutique/detail_produit.html', {
        'produit': produit,
        'tailles': tailles
    })

# --- 3. GESTION DU PANIER (AVEC VALIDATION DE TAILLE) ---

# Le décorateur @login_required empêche l'accès à cette vue si l'utilisateur n'est pas connecté.
# Il le redirigera automatiquement vers la page de connexion.
@login_required
def ajouter_au_panier(request, produit_id):
    """Ajoute un produit au panier en forçant le choix d'une taille."""
    produit = get_object_or_404(Produit, id=produit_id)

    # SECURITÉ : On vérifie que la requête est de type POST (les données ont été envoyées via le formulaire)
    # Si quelqu'un accède à l'URL d'ajout directement en tapant l'adresse (méthode GET), on le redirige.
    if request.method != "POST":
        messages.warning(request, "Veuillez sélectionner une pointure avant d'ajouter l'article.")
        return redirect('detail_produit', produit_id=produit.id)

    # Récupération de la donnée envoyée par l'utilisateur via le formulaire (attribut name="taille")
    taille_choisie = request.POST.get('taille')

    # Validation : Si la taille n'est pas fournie (formulaire trafiqué ou mal soumis)
    if not taille_choisie:
        messages.error(request, "Erreur : vous devez choisir une taille.")
        return redirect('detail_produit', produit_id=produit.id)

    # Le panier est stocké dans la session de l'utilisateur (un espace de stockage côté serveur lié à son navigateur).
    # request.session.get('panier', {}) récupère le panier existant, ou un dictionnaire vide '{}' s'il n'existe pas encore.
    panier = request.session.get('panier', {})
    
    # Création d'une clé unique pour cet article dans le panier, combinant l'ID du produit ET la taille choisie.
    # Ainsi, une pointure 39 et une pointure 40 du même modèle compteront pour deux lignes séparées dans le panier.
    cle_panier = f"{produit_id}_{taille_choisie}"

    # Si ce produit dans cette taille n'est pas encore dans le panier, on l'initialise avec une quantité à 0.
    if cle_panier not in panier:
        panier[cle_panier] = {
            'produit_id': produit_id,
            'nom': produit.nom,
            'quantite': 0,
            # Le prix (Decimal) doit être converti en chaîne (str) car les sessions n'acceptent que du texte JSON standard.
            'prix': str(produit.prix),
            'taille': taille_choisie
        }

    # Vérification des stocks avant d'augmenter la quantité dans le panier
    if panier[cle_panier]['quantite'] < produit.stock:
        panier[cle_panier]['quantite'] += 1
        # Ajout d' un message flash de succès qui ne s'affichera qu'une seule fois à la prochaine page chargée.
        messages.success(request, f"{produit.nom} (Taille {taille_choisie}) ajouté au panier.")
    else:
        messages.error(request, f"Stock maximum atteint pour cette pointure.")

    # Sauvegarde du panier mis à jour dans la session globale de Django
    request.session['panier'] = panier
    # Force Django à sauvegarder la session (nécessaire lorsqu'on modifie un dictionnaire imbriqué dans la session)
    request.session.modified = True
    
    # Redirige l'utilisateur vers la vue qui affiche son panier
    return redirect('voir_panier')

def voir_panier(request):
    """
    Récupère le panier de l'utilisateur depuis la session
    et calcule le total pour affichage.
    """
    panier = request.session.get('panier', {})
    articles = []
    total_general = 0

    # On boucle sur chaque article (clé) du panier
    for cle, data in panier.items():
        try:
            # On récupère l'objet Produit frais depuis la BDD pour vérifier son prix et son nom actuels
            p = Produit.objects.get(id=int(data['produit_id']))
            # st = sous-total = prix unitaire * quantité
            st = p.prix * data['quantite']
            total_general += st
            
            # Ajout des informations complètes de cette ligne dans la liste 'articles'
            articles.append({
                'cle': cle,
                'produit': p,
                'taille': data['taille'],
                'quantite': data['quantite'],
                'sous_total': st
            })
        except Produit.DoesNotExist:
            # Si le produit a été supprimé du catalogue entre-temps, on l'ignore silencieusement.
            continue

    # Envoi des articles enrichis et du total à la vue HTML
    return render(request, 'boutique/panier.html', {
        'articles': articles,
        'total_general': total_general
    })

def modifier_panier(request, cle, action):
    """
    Permet d'augmenter, diminuer ou supprimer la quantité d'un article dans le panier.
    L'action provient de l'URL ('plus', 'moins', 'supprimer').
    """
    panier = request.session.get('panier', {})

    if cle in panier:
        # On vérifie la capacité du stock en récupérant le produit
        produit = get_object_or_404(Produit, id=int(panier[cle]['produit_id']))

        if action == 'plus':
            # On ajoute 1 uniquement si le stock le permet
            if panier[cle]['quantite'] < produit.stock:
                panier[cle]['quantite'] += 1
            else:
                messages.error(request, "Stock épuisé.")
        elif action == 'moins':
            # On retire 1. Si la quantité tombe à 0, on supprime carrément l'article (la clé) du panier.
            panier[cle]['quantite'] -= 1
            if panier[cle]['quantite'] <= 0:
                del panier[cle]
        elif action == 'supprimer':
            # Retrait direct de la ligne
            del panier[cle]

    # Enregistrement des modifications en session
    request.session['panier'] = panier
    request.session.modified = True
    return redirect('voir_panier')

def vider_panier(request):
    """
    Supprime entièrement la clé 'panier' de la session utilisateur.
    """
    if 'panier' in request.session:
        del request.session['panier']
    return redirect('liste_produits')

# --- 4. GESTION DES COMMANDES ---

@login_required
def checkout(request):
    """
    Transforme le panier en session en une vraie Commande en base de données.
    Déduit le stock des produits achetés et vide le panier.
    """
    panier = request.session.get('panier', {})
    
    # Si le panier est vide (par exemple, refresh abusif de la page de paiement), on redirige.
    if not panier:
        return redirect('liste_produits')

    # À ce stade, la commande est créée. On lui génère un code de retrait court de 6 caractères (hexadécimal majuscule)
    commande = Commande.objects.create(
        client=request.user,
        code_retrait=uuid.uuid4().hex[:6].upper()
    )

    # Ajout des LigneCommande associées pour chaque article du panier
    for cle, data in panier.items():
        p = Produit.objects.get(id=int(data['produit_id']))
        # Historisation du prix d'achat
        LigneCommande.objects.create(
            commande=commande,
            produit=p,
            quantite=data['quantite'],
            prix_unitaire_au_moment=p.prix
        )
        # On déduit les stocks vendus
        p.stock -= data['quantite']
        p.save()

    # Le panier a été transformé en commande, on peut le vider de la session
    del request.session['panier']
    
    # Redirection vers la page de succès
    return redirect('confirmation_commande', commande_id=commande.id)

@login_required
def confirmation_commande(request, commande_id):
    """
    Affiche un récapitulatif pour que l'utilisateur puisse voir son numéro de commande et son code de retrait.
    """
    # On force client=request.user pour qu'un utilisateur ne puisse pas voir la commande d'un autre via l'URL.
    commande = get_object_or_404(Commande, id=commande_id, client=request.user)
    return render(request, 'boutique/confirmation_commande.html', {'commande': commande})

@login_required
def historique_commandes(request):
    """
    Liste des commandes passées par l'utilisateur connecté ("Mon compte").
    -date_commande permet de trier des plus récentes aux plus anciennes.
    """
    commandes = Commande.objects.filter(client=request.user).order_by('-date_commande')
    return render(request, 'boutique/historique_commandes.html', {'commandes': commandes})