package Dm;

public class Exo3 {
	 public static void main(String[] args){
	// Déclaration des variables
    int N, i, ech, sauve;
    
    // 1. Saisie de la taille du tableau

    N = Saisie.lire_int("Quelle est la taille de votre tableau ? ");
    
    // Déclaration et remplissage du tableau
    int[] tab = new int[N];
    for (i = 0; i < N; i++) {
         tab[i] = Saisie.lire_int("Veuillez saisir le contenu de la case de rang " + (i + 1) + " : " );
    }

    // Sauvegarde de la valeur de N
    sauve = N;

    // 2. Tri par propagation (tri à bulles)
    while (N > 0) {
        i = 0;
        while (i < N - 1) {
            if (tab[i] > tab[i + 1]) {
                ech = tab[i];
                tab[i] = tab[i + 1];
                tab[i + 1] = ech;
            }
            i++;
        }
        N--;
    }

    // 3. Affichage du tableau trié
    System.out.println("\nTableau trié :");
    for (i = 0; i < sauve; i++) {
        System.out.print(tab[i] + " ");
    }
}
}


