// Données extraites de l'algorithme Java original (Dm.Exo1)
const villes = ["Brest", "Grenoble", "Lille", "Lyon", "Marseille", "Nantes", "Paris", "Rennes", "Strasbourg", "Toulouse"];

const distances = [
    [0, 996, 723, 890, 1286, 305, 564, 245, 1026, 884],
    [996, 0, 750, 104, 286, 711, 576, 747, 505, 543],
    [723, 750, 0, 668, 979, 593, 224, 515, 524, 905],
    [890, 104, 668, 0, 316, 607, 472, 645, 434, 467],
    [1286, 286, 979, 316, 0, 890, 769, 938, 750, 400],
    [305, 711, 593, 607, 890, 0, 386, 106, 832, 559],
    [564, 576, 224, 472, 769, 386, 0, 348, 447, 681],
    [245, 747, 515, 645, 938, 106, 348, 0, 799, 665],
    [1026, 505, 524, 434, 750, 832, 447, 799, 0, 901],
    [884, 543, 905, 467, 400, 559, 681, 665, 901, 0]
];

// Initialisation des listes déroulantes au chargement de la page
document.addEventListener("DOMContentLoaded", () => {
    const selectDepart = document.getElementById("villeDepart");
    const selectArrivee = document.getElementById("villeArrivee");

    villes.forEach((ville, index) => {
        let option1 = document.createElement("option");
        option1.value = index;
        option1.textContent = ville;
        selectDepart.appendChild(option1);

        let option2 = document.createElement("option");
        option2.value = index;
        option2.textContent = ville;
        selectArrivee.appendChild(option2);
    });
});

// Fonction déclenchée par le bouton "Calculer"
function calculerDistance() {
    const selectDepart = document.getElementById("villeDepart");
    const selectArrivee = document.getElementById("villeArrivee");
    const resultatBox = document.getElementById("resultat");
    const erreurBox = document.getElementById("erreur");
    const distanceValue = document.getElementById("distance-value");

    const indexDepart = selectDepart.value;
    const indexArrivee = selectArrivee.value;

    // Réinitialisation de l'affichage
    resultatBox.classList.add("hidden");
    erreurBox.classList.add("hidden");

    // Vérification de la sélection (l'utilisateur doit choisir 2 villes)
    if (indexDepart === "" || indexArrivee === "") {
        erreurBox.classList.remove("hidden");
        return;
    }

    // Récupération de la distance dans la matrice
    const distance = distances[parseInt(indexDepart)][parseInt(indexArrivee)];

    // Affichage animé du résultat
    distanceValue.textContent = distance;
    resultatBox.classList.remove("hidden");
}
