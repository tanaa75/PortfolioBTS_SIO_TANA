# click_and_collect_project/urls.py

from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static

# Fichier de routage principal du projet web entier.
urlpatterns = [
    # Route native vers l'interface d'administration de Django (/admin/)
    path('admin/', admin.site.urls),

    # Inclusion des URLs natives de Django pour la gestion de l'authentification (login, logout, etc.)
    path('accounts/', include('django.contrib.auth.urls')),

    # Inclusion de toutes les URLs de notre application locale 'boutique'. 
    # Tout préfixe d'URL par 'boutique/' sera géré en sous-marin par le fichier boutique/urls.py
    path('boutique/', include('boutique.urls')),
]

# Mode de Développement Uniquement (DEBUG=True) :
# Permet à Django de servir (afficher) les fichiers médias (i.e. images uploadées) sur le serveur local de test.
# En production, ce sont les serveurs web (comme Nginx ou Apache) qui servent ces fichiers statiques.
if settings.DEBUG:
    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)