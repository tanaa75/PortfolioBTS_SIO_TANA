const themes = {
    Chiffres: ["1", "2", "3", "4", "5", "6", "7", "8"],
    Fruits: ["🍎", "🍌", "🍊", "🍇", "🍓", "🥝", "🍑", "🍒"],
    Sports: ["⚽", "🏀", "🏈", "⚾", "🎾", "🏐", "🏓", "🏸"]
};

let currentTheme = "Chiffres";
let cards = [];
let flippedCards = [];
let matchedCount = 0;
let moves = 0;
let timer = 0;
let timerInterval;
let isLocked = false;

const board = document.getElementById('board');
const movesLabel = document.getElementById('moves');
const timerLabel = document.getElementById('timer');
const matchesLabel = document.getElementById('matches');
const victoryModal = document.getElementById('victory-modal');
const victoryMsg = document.getElementById('victory-msg');

function initGame() {
    // Reset variables
    cards = [];
    flippedCards = [];
    matchedCount = 0;
    moves = 0;
    timer = 0;
    isLocked = false;
    
    clearInterval(timerInterval);
    updateStats();
    victoryModal.classList.add('hidden');
    
    // Create pair list
    const items = [...themes[currentTheme], ...themes[currentTheme]];
    // Shuffle
    items.sort(() => Math.random() - 0.5);
    
    board.innerHTML = '';
    items.forEach((item, index) => {
        const card = document.createElement('div');
        card.classList.add('card');
        card.dataset.value = item;
        card.dataset.index = index;
        
        card.innerHTML = `
            <div class="card-face card-back"></div>
            <div class="card-face card-front">${item}</div>
        `;
        
        card.addEventListener('click', () => flipCard(card));
        board.appendChild(card);
    });
    
    startTimer();
}

function startTimer() {
    timerInterval = setInterval(() => {
        timer++;
        timerLabel.textContent = timer + 's';
    }, 1000);
}

function updateStats() {
    movesLabel.textContent = moves;
    timerLabel.textContent = timer + 's';
    matchesLabel.textContent = `${matchedCount}/${themes[currentTheme].length}`;
}

function flipCard(card) {
    if (isLocked || card.classList.contains('flipped') || card.classList.contains('matched')) return;
    
    card.classList.add('flipped');
    flippedCards.push(card);
    
    if (flippedCards.length === 2) {
        moves++;
        updateStats();
        checkMatch();
    }
}

function checkMatch() {
    isLocked = true;
    const [card1, card2] = flippedCards;
    
    if (card1.dataset.value === card2.dataset.value) {
        matchedCount++;
        card1.classList.add('matched');
        card2.classList.add('matched');
        flippedCards = [];
        isLocked = false;
        updateStats();
        
        if (matchedCount === themes[currentTheme].length) {
            gameWon();
        }
    } else {
        setTimeout(() => {
            card1.classList.remove('flipped');
            card2.classList.remove('flipped');
            flippedCards = [];
            isLocked = false;
        }, 1000);
    }
}

function changeTheme(theme) {
    if (isLocked) return;
    currentTheme = theme;
    
    // Update UI active buttons
    document.querySelectorAll('.theme-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById(`btn-${theme}`).classList.add('active');
    
    initGame();
}

function gameWon() {
    clearInterval(timerInterval);
    victoryMsg.textContent = `Vous avez trouvé toutes les paires en ${moves} coups et ${timer} secondes !`;
    victoryModal.classList.remove('hidden');
}

function toggleHelp() {
    if (isLocked) return;
    
    const hiddenCards = [...document.querySelectorAll('.card:not(.flipped):not(.matched)')];
    if (hiddenCards.length < 2) return;
    
    // Show 2 cards briefly
    isLocked = true;
    const itemsToShow = hiddenCards.slice(0, 2);
    itemsToShow.forEach(c => c.classList.add('flipped'));
    
    setTimeout(() => {
        itemsToShow.forEach(c => c.classList.remove('flipped'));
        isLocked = false;
    }, 1500);
}

// Start game on load
initGame();
