let currentOrder = [];
let total = 0;

const receiptItems = document.getElementById('receipt-items');
const subtotalEl = document.getElementById('subtotal');
const taxEl = document.getElementById('tax');
const grandTotalEl = document.getElementById('grand-total');
const receiptModal = document.getElementById('receipt-modal');
const finalItems = document.getElementById('final-items');
const finalTotalVal = document.getElementById('final-total-val');
const receiptDate = document.getElementById('receipt-date');

function addItem(name, price) {
    const existing = currentOrder.find(item => item.name === name);
    if (existing) {
        existing.qty++;
    } else {
        currentOrder.push({ name, price, qty: 1 });
    }
    updateUI();
}

function updateUI() {
    receiptItems.innerHTML = '';
    total = 0;
    
    currentOrder.forEach(item => {
        const row = document.createElement('div');
        row.classList.add('receipt-item');
        row.innerHTML = `
            <span>${item.name}</span>
            <span>x${item.qty}</span>
            <span>${(item.price * item.qty).toFixed(2)} €</span>
        `;
        receiptItems.appendChild(row);
        total += item.price * item.qty;
    });
    
    const tax = total * 0.20;
    const subtotal = total - tax;
    
    subtotalEl.textContent = subtotal.toFixed(2) + ' €';
    taxEl.textContent = tax.toFixed(2) + ' €';
    grandTotalEl.textContent = total.toFixed(2) + ' €';
}

function pressNum(num) {
    // Simulation simple : on ne gère pas la saisie manuelle de prix pour le moment
    console.log("Num pressed:", num);
}

function clearAll() {
    currentOrder = [];
    updateUI();
}

function processPayment() {
    if (currentOrder.length === 0) return;
    
    // Prepare Modal
    finalItems.innerHTML = '';
    currentOrder.forEach(item => {
        const div = document.createElement('div');
        div.style.display = 'flex';
        div.style.justifyContent = 'space-between';
        div.style.fontSize = '0.9rem';
        div.innerHTML = `<span>${item.name} x${item.qty}</span> <span>${(item.price * item.qty).toFixed(2)}€</span>`;
        finalItems.appendChild(div);
    });
    
    finalTotalVal.textContent = total.toFixed(2) + ' €';
    receiptDate.textContent = new Date().toLocaleString();
    
    receiptModal.classList.remove('hidden');
}

function closeReceipt() {
    receiptModal.classList.add('hidden');
    clearAll();
}
