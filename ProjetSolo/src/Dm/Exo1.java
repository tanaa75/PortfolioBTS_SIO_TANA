package Dm;

public class Exo1{
public static void main(String[] args){
	
// Déclaration des variables
String[] villes;
int[][] distances;
String villeDepart, villeArrivee;
int indexDepart, indexArrivee, distance;
boolean continuer = true;

// Initialisation des villes (triées alphabétiquement)
villes = new String[]{"Brest", "Grenoble", "Lille", "Lyon", "Marseille", "Nantes", "Paris", "Rennes", "Strasbourg", "Toulouse"};

// Initialisation de la matrice des distances (correspondant aux villes)
distances = new int[][]{
    {0, 996, 723, 890, 1286, 305, 564, 245, 1026, 884},
    {996, 0, 750, 104, 286, 711, 576, 747, 505, 543},
    {723, 750, 0, 668, 979, 593, 224, 515, 524, 905},
    {890, 104, 668, 0, 316, 607, 472, 645, 434, 467},
    {1286, 286, 979, 316, 0, 890, 769, 938, 750, 400},
    {305, 711, 593, 607, 890, 0, 386, 106, 832, 559},
    {564, 576, 224, 472, 769, 386, 0, 348, 447, 681},
    {245, 747, 515, 645, 938, 106, 348, 0, 799, 665},
    {1026, 505, 524, 434, 750, 832, 447, 799, 0, 901},
    {884, 543, 905, 467, 400, 559, 681, 665, 901, 0}
};

// Boucle principale pour permettre plusieurs recherches
while (continuer) {
    // Demande la ville de départ
    villeDepart = Saisie.lire_String("Entrez la ville de départ : ");

    // Vérifie si la ville existe
    indexDepart = chercherIndex(villes, villeDepart);
    if (indexDepart == -1) {
        System.out.println("❌ Ville non trouvée. Veuillez entrer une ville valide.");
        continue; // Recommence la boucle
    }

    // Demande la ville d'arrivée
    villeArrivee = Saisie.lire_String("Entrez la ville d'arrivée : ");

    // Vérifie si la ville existe
    indexArrivee = chercherIndex(villes, villeArrivee);
    if (indexArrivee == -1) {
        System.out.println("❌ Ville non trouvée. Veuillez entrer une ville d'arrivée.");
        continue; // Recommence la boucle
    }

    // Récupère la distance et l'affiche
    distance = distances[indexDepart][indexArrivee];
    System.out.println("📌 Distance entre " + villeDepart + " et " + villeArrivee + " : " + distance + " kms");

    // Demande si l'utilisateur veut recommencer
    String reponse = Saisie.lire_String("Voulez-vous faire une autre recherche ? (oui/non) : ");
    if (!reponse.equalsIgnoreCase("oui")) {  // On arrête si l'utilisateur ne tape pas "oui" (insensible à la casse)
        continuer = false;
    }
}

System.out.println("🚀 Programme terminé. Merci !");
}

// Méthode pour chercher l'index d'une ville dans le tableau
public static int chercherIndex(String[] villes, String ville) {
for (int i = 0; i < villes.length; i++) {
    if (villes[i].equalsIgnoreCase(ville)) {  // Comparaison insensible à la casse
        return i; // Retourne l'index si trouvé
    }
}
return -1; // Retourne -1 si la ville n'existe pas
}
}