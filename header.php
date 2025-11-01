<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="CSS/header.css"/>
    <link rel="stylesheet" href="CSS/footer.css"/>
    <link rel = "stylesheet" href="CSS/all-books.css"/>
    <link rel = "stylesheet" href = "CSS/book.css"/>
    <link rel = "stylesheet" href = "CSS/cart-btn.css"/>
    <link rel = "stylesheet" href = "CSS/cart.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;500;600;700&display=swap" rel="stylesheet">
    

</head>
<body>
    <div class = "head">
        <div class="box">
        <div class = "loqo">
           <a href = ""> <img width="154" height="38"
             src="https://websitedemos.net/book-store-04/wp-content/uploads/sites/1029/2022/02/logo.svg"
             class="custom-logo" alt="Book Store" decoding="async"> </a>
         </div>

         <div class = "nav-links"> <a href="index.php">ALL BOOKS</a>
               <a href="index.php">NEW ARRIVAL</a>
               <a href="index.php">BEST SELLER</a>
               <a href="index.php">EDITORS PICK</a>
               <a href="index.php">ABOUT</a>
               <a href="index.php">CONTACT</a>
         </div>
         <div class = "nav-end">
            <div class = "basket">
                <a href ="#" id = "cartBtn">
                   <img src = "images/shopping-bag.png" alt="basket">
                </a>
            </div>
            <div class = "profile"><a href = ""><img src = "images/profile.png"></a></div>
         </div>
         <div class="burger-container">
              <img src="images/burger-bar.png" alt="menu"/>
            </div>

         </div>
         
    </div>

<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-header">
        <span>🛒 Basket</span>
        <button id="closeCart">&times;</button>
    </div>
    <div class="cart-body" id="cartBody">
        <div class="cart-empty" id="cartEmpty">Your Basket is empty</div>
        <ul id="cartItems" style="list-style:none; padding:0; margin:0;"></ul>
    </div>
    <div class="cart-footer">
        <span class="total">Total: <span id="totalAmount">0</span> ₼</span>
     
            <a href="https://wa.me/994701234567?text=Salam%20Mən%20kitab%20barədə%20soruşmaq%20istəyirəm" target="_blank">
           <button>Complete your order</button>
              </a>
    </div>
</div>
<script>
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let cartSidebar = document.getElementById('cartSidebar');
let closeCart = document.getElementById('closeCart');
let cartItemsEl = document.getElementById('cartItems');
let cartEmpty = document.getElementById('cartEmpty');
let totalAmountEl = document.getElementById('totalAmount');
let addButtons = document.querySelectorAll('.add-btn'); // bütün səhifələrdəki düymələr

function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function updateCart() {
    cartItemsEl.innerHTML = '';
    let total = 0;

    if(cart.length === 0){
        cartEmpty.style.display = 'block';
    } else {
        cartEmpty.style.display = 'none';
        cart.forEach((item, index) => {
            const li = document.createElement('li');
            li.className = 'cart-item';
            li.innerHTML = `
                <span>${item.title} - ${item.price} ₼ x ${item.quantity}</span>
                <button class="qty-btn plus" onclick="changeQuantity(${index}, 1)">+</button>
                <button class="qty-btn minus" onclick="changeQuantity(${index}, -1)">-</button>
                <button class="qty-btn remove" onclick="removeItem(${index})">x</button>
            `;
            cartItemsEl.appendChild(li);
            total += parseFloat(item.price) * item.quantity;
        });
    }
    totalAmountEl.textContent = total.toFixed(2);
}

function changeQuantity(index, delta) {
    cart[index].quantity += delta;
    if(cart[index].quantity <= 0){
        cart.splice(index,1);
    }
    saveCart();
    updateCart();
}

function removeItem(index){
    cart.splice(index,1);
    saveCart();
    updateCart();
}

// Add to Cart event listener yalnız burada
addButtons.forEach(btn => {
    btn.addEventListener('click', ()=>{
        const title = btn.getAttribute('data-title');
        const price = btn.getAttribute('data-price');

        let exist = cart.find(item => item.title === title);
        if(exist){
            exist.quantity += 1;
        } else {
            cart.push({title, price, quantity: 1});
        }

        saveCart();
        cartSidebar.classList.add('active');
        updateCart();
    });
});

window.addEventListener('load', updateCart);

const cartBtn = document.getElementById('cartBtn');
cartBtn.addEventListener('click', (e) => {
    e.preventDefault();
    cartSidebar.classList.add('active');
});

closeCart.addEventListener('click', () => {
    cartSidebar.classList.remove('active');
});
</script>