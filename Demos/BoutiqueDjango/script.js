const products = [
    { id: 1, name: "PC Portable Gaming", price: 1299.99, desc: "Intel i7, 16Go RAM, RTX 3060", icon: "laptop" },
    { id: 2, name: "Écran 27\" 4K", price: 349.50, desc: "Dalle IPS, 144Hz, HDR400", icon: "desktop" },
    { id: 3, name: "Clavier Mécanique", price: 89.00, desc: "Switchs Red, RGB, AZERTY", icon: "keyboard" },
    { id: 4, name: "Souris Sans Fil", price: 59.90, desc: "16000 DPI, Autonomie 60h", icon: "mouse" },
    { id: 5, name: "Casque Audio 7.1", price: 115.00, desc: "Micro antibruit, Spatial Sound", icon: "headphones" },
    { id: 6, name: "Chaise Gaming", price: 249.00, desc: "Ergonomique, Dossier inclinable", icon: "chair" }
];

let cart = [];

const productList = document.getElementById('product-list');
const cartItemsContainer = document.getElementById('cart-items');
const cartCount = document.getElementById('cart-count');
const cartTotal = document.getElementById('cart-total');
const sideCart = document.getElementById('side-cart');
const orderModal = document.getElementById('order-modal');
const orderCode = document.getElementById('order-code');
const checkoutBtn = document.getElementById('checkout-btn');

function initShop() {
    productList.innerHTML = '';
    products.forEach(p => {
        const card = document.createElement('div');
        card.classList.add('product-card');
        card.innerHTML = `
            <div class="product-img"><i class="fas fa-${p.icon}"></i></div>
            <div class="product-info">
                <h3>${p.name}</h3>
                <p style="font-size: 0.8rem; color: #aaa; margin-bottom: 0.5rem;">${p.desc}</p>
                <span class="price">${p.price.toFixed(2)} €</span>
                <button class="btn-add" onclick="addToCart(${p.id})">
                    <i class="fas fa-plus"></i> Ajouter
                </button>
            </div>
        `;
        productList.appendChild(card);
    });
    updateCartUI();
}

function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    const existing = cart.find(item => item.id === productId);
    
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ ...product, qty: 1 });
    }
    
    updateCartUI();
    // Effet visuel sur le bouton panier
    cartCount.style.transform = 'scale(1.3)';
    setTimeout(() => cartCount.style.transform = 'scale(1)', 200);
}

function updateCartUI() {
    cartItemsContainer.innerHTML = '';
    let total = 0;
    let count = 0;
    
    cart.forEach(item => {
        const el = document.createElement('div');
        el.classList.add('cart-item');
        el.innerHTML = `
            <div class="qty">${item.qty}</div>
            <div style="flex-grow: 1">
                <div style="font-weight: 600; font-size: 0.9rem;">${item.name}</div>
                <div style="font-size: 0.8rem; color: #888;">${(item.price * item.qty).toFixed(2)} €</div>
            </div>
            <button onclick="removeFromCart(${item.id})" style="background:none; border:none; color:#ff5555; cursor:pointer;">
                <i class="fas fa-trash"></i>
            </button>
        `;
        cartItemsContainer.appendChild(el);
        total += item.price * item.qty;
        count += item.qty;
    });
    
    cartCount.textContent = count;
    cartTotal.textContent = total.toFixed(2) + ' €';
    checkoutBtn.disabled = cart.length === 0;
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p style="text-align: center; color: #666; margin-top: 2rem;">Votre panier est vide.</p>';
    }
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCartUI();
}

function toggleCart() {
    sideCart.classList.toggle('hidden');
}

function checkout() {
    // Génère un code de retrait comme dans le projet Django
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 8; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    orderCode.textContent = code;
    orderModal.classList.remove('hidden');
    
    // Reset panier
    cart = [];
    updateCartUI();
    toggleCart();
}

function closeModal() {
    orderModal.classList.add('hidden');
}

initShop();
