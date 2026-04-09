let array = [];
let delay = 100; // Constante de vitesse pour l'animation
let isSorting = false;

const container = document.getElementById('array-container');
const sizeSlider = document.getElementById('arraySize');
const sizeValue = document.getElementById('sizeValue');
const compCountLabel = document.getElementById('comp-count');
const swapCountLabel = document.getElementById('swap-count');
const algoNameLabel = document.getElementById('algo-name');
const algoDescLabel = document.getElementById('algo-desc');

let compCount = 0;
let swapCount = 0;

// Met à jour la valeur affichée du slider
function updateSizeUI(val) {
    sizeValue.textContent = val;
    if (!isSorting) {
        genererTableau();
    }
}

// Génère un nouveau tableau aléatoire
function genererTableau() {
    if (isSorting) return;
    
    container.innerHTML = '';
    array = [];
    compCount = 0;
    swapCount = 0;
    updateStats();
    algoNameLabel.textContent = "Prêt";
    algoDescLabel.textContent = "Générez un tableau et lancez un tri pour voir l'algorithme en action.";
    
    const size = parseInt(sizeSlider.value);
    
    // Pour que les barres s'adaptent bien visuellement
    const containerHeight = 280; // height inside container
    const widthPercentage = 100 / size;
    
    for (let i = 0; i < size; i++) {
        // Valeurs de 10 à 100
        const value = Math.floor(Math.random() * 90) + 10;
        array.push(value);
        
        const bar = document.createElement('div');
        bar.classList.add('array-bar');
        bar.style.height = `${(value / 100) * containerHeight}px`;
        bar.style.width = `${widthPercentage}%`;
        bar.style.margin = `0 ${20 / size}px`; // dynamic margin
        
        // N'afficher le texte que s'il y a assez de place
        if (size <= 20) {
            bar.textContent = value;
        }
        
        container.appendChild(bar);
    }
}

// Fonction utilitaire pour mettre en pause l'exécution (animation)
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function updateStats() {
    compCountLabel.textContent = compCount;
    swapCountLabel.textContent = swapCount;
}

function setSortingState(state) {
    isSorting = state;
    document.getElementById('arraySize').disabled = state;
    document.querySelector('.btn-secondary').disabled = state;
    document.getElementById('btn-bulles').disabled = state;
    document.getElementById('btn-selection').disabled = state;
}

// --- TRI A BULLES (Bubble Sort) basé sur Exo3.java ---
async function lancerTriBulles() {
    if (isSorting) return;
    setSortingState(true);
    
    algoNameLabel.textContent = "Tri à Bulles (Propagation)";
    algoDescLabel.textContent = "Parcourt la liste plusieurs fois en échangeant les éléments adjacents s'ils sont dans le mauvais ordre. Les plus grands éléments 'remontent' comme des bulles.";
    
    let bars = document.getElementsByClassName('array-bar');
    let n = array.length;
    
    // Algorithme similaire au Java original: while (N > 0)
    while (n > 0) {
        let i = 0;
        while (i < n - 1) {
            // Effet de comparaison
            bars[i].classList.add('bar-comparing');
            bars[i+1].classList.add('bar-comparing');
            
            await sleep(delay);
            compCount++;
            updateStats();
            
            if (array[i] > array[i + 1]) {
                // Effet d'échange
                bars[i].classList.remove('bar-comparing');
                bars[i+1].classList.remove('bar-comparing');
                bars[i].classList.add('bar-swapping');
                bars[i+1].classList.add('bar-swapping');
                
                await sleep(delay);
                
                // Swap logique
                let temp = array[i];
                array[i] = array[i + 1];
                array[i + 1] = temp;
                
                // Swap visuel (hauteur et texte)
                let tempHeight = bars[i].style.height;
                bars[i].style.height = bars[i+1].style.height;
                bars[i+1].style.height = tempHeight;
                
                let tempText = bars[i].textContent;
                bars[i].textContent = bars[i+1].textContent;
                bars[i+1].textContent = tempText;
                
                swapCount++;
                updateStats();
                
                bars[i].classList.remove('bar-swapping');
                bars[i+1].classList.remove('bar-swapping');
            } else {
                bars[i].classList.remove('bar-comparing');
                bars[i+1].classList.remove('bar-comparing');
            }
            i++;
        }
        
        // Le dernier élément parcouru est à sa place définitive
        bars[n - 1].classList.add('bar-sorted');
        n--;
    }
    
    algoNameLabel.textContent = "Tri à Bulles Terminé !";
    setSortingState(false);
}

// --- TRI PAR SELECTION (Selection Sort) ---
async function lancerTriSelection() {
    if (isSorting) return;
    setSortingState(true);
    
    algoNameLabel.textContent = "Tri par Sélection";
    algoDescLabel.textContent = "Recherche le plus petit élément du reste du tableau et le place à la fin de la zone déjà triée.";
    
    let bars = document.getElementsByClassName('array-bar');
    let n = array.length;
    
    for (let i = 0; i < n - 1; i++) {
        let minIndex = i;
        bars[minIndex].classList.add('bar-swapping'); // Indique le min actuel
        
        for (let j = i + 1; j < n; j++) {
            bars[j].classList.add('bar-comparing');
            await sleep(delay);
            
            compCount++;
            updateStats();
            
            if (array[j] < array[minIndex]) {
                bars[minIndex].classList.remove('bar-swapping');
                minIndex = j;
                bars[minIndex].classList.add('bar-swapping');
            } else {
                bars[j].classList.remove('bar-comparing');
            }
        }
        
        if (minIndex !== i) {
            // Swap logique
            let temp = array[i];
            array[i] = array[minIndex];
            array[minIndex] = temp;
            
            // Swap visuel
            let tempHeight = bars[i].style.height;
            bars[i].style.height = bars[minIndex].style.height;
            bars[minIndex].style.height = tempHeight;
            
            let tempText = bars[i].textContent;
            bars[i].textContent = bars[minIndex].textContent;
            bars[minIndex].textContent = tempText;
            
            swapCount++;
            updateStats();
        }
        
        bars[minIndex].classList.remove('bar-swapping');
        // L'élément i est maintenant trié
        bars[i].classList.add('bar-sorted');
    }
    // Le dernier élément est forcément trié
    bars[n - 1].classList.add('bar-sorted');
    
    algoNameLabel.textContent = "Tri par Sélection Terminé !";
    setSortingState(false);
}

// Générer le tableau initial au lancement
window.onload = genererTableau;
