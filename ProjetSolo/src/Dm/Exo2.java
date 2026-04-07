package Dm;

public class Exo2 {
	 public static void main(String[] args){
	// Déclaration des variables
    int N, i, j, minIndex, temp;
    
    // 1. Demander le nombre d'éléments (N doit être > 5)
    do {
         N = Saisie.lire_int("Entrez le nombre d'éléments (N > 5) : "); // Utilisation de la méthode fournie pour la saisie
    } while (N <= 4);

    // 2. Initialiser le tableau et demander les valeurs à l'utilisateur
    int[] tab = new int[N];
    for (i = 0; i < N; i++) {
         tab[i] = Saisie.lire_int("Entrez l'élément " + (i + 1) + " : "); // Utilisation de la méthode fournie
    }

    // 3. Tri par sélection avec affichage progressif
    System.out.println("\nDéroulement du tri par sélection :");
    for (i = 0; i < N - 1; i++) {
        minIndex = i;
        for (j = i + 1; j < N; j++) {
            if (tab[j] < tab[minIndex]) {
                minIndex = j;
            }
        }
        // Échanger tab[i] avec le plus petit élément trouvé
        temp = tab[i];
        tab[i] = tab[minIndex];
        tab[minIndex] = temp;

        // Affichage de l'état du tableau après chaque itération
        System.out.print("Étape " + (i + 1) + " : ");
        for (int k = 0; k < N; k++) {
            System.out.print(tab[k] + " ");
        }
        System.out.println();
    }

    // 4. Afficher le tableau trié final
    System.out.println("\nTableau trié :");
    for (i = 0; i < N; i++) {
        System.out.print(tab[i] + " ");
    }
}
}

