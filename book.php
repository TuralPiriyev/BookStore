<?php
require_once "db.php";
include "header.php";

if(isset($_GET['title']))
{
    $title = mysqli_real_escape_string($conn, $_GET['title']);
    $sql = " SELECT 
            b.*, 
            a.name AS author_name
        FROM 
            books b
        JOIN 
            book_authors ba ON b.id = ba.book_id
        JOIN 
            authors a ON ba.author_id = a.id
        WHERE 
            b.title = '$title'
            ";
    $result = mysqli_query($conn, $sql);
    if($book = mysqli_fetch_assoc($result))
    {
     ?>
       <div class="allcont">
       <div class="self-book">
          <div class="image">
            <img src="<?php echo $book['cover_image']?>" alt="">
          </div>
          <div class="book-info">
             <div class = "info-cont">
               <h1 class = "title">"<?php echo $book['title']?>"</h1>
               <span class = "price">$<?php echo $book['price']?></span>
               <hr/>
               <span class = "author2">Author:</span>
               <span class = "author"><?php echo $book['author_name']?></span><br/><br/>
               <span class = "desc2">Description:</span>
               <span class = "description"><?php echo $book['description']?></span>
              </div> 
              <div class = "basket">
              <button class="btn-github add-btn" 
                     data-title="<?php echo $book['title']?>" 
                     data-price="<?php echo $book['price']?>">
                     Add to Cart
             </button>

              </div>
        
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
        <button>Complete the order</button>
    </div>
</div>

<script>
 cartSidebar = document.getElementById('cartSidebar');
 closeCart = document.getElementById('closeCart');
 cartItemsEl = document.getElementById('cartItems');
 cartEmpty = document.getElementById('cartEmpty');
 totalAmountEl = document.getElementById('totalAmount');
 addButtons = document.querySelectorAll('.add-btn');

// localStorage-dən cart-i oxu
 cart = JSON.parse(localStorage.getItem('cart')) || [];

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

            // Quantity ilə + və - düymələri
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

// Quantity-ni dəyişən funksiya
function changeQuantity(index, delta) {
    cart[index].quantity += delta;

    // Əgər quantity 0 və ya aşağıdırsa, item sil
    if(cart[index].quantity <= 0){
        cart.splice(index, 1);
    }

    saveCart();
    updateCart();
}

// Cart-dən item silmək
function removeItem(index){
    cart.splice(index,1);
    saveCart();
    updateCart();
}

function removeItem(index){
    cart.splice(index,1);
    saveCart();
    updateCart();
}

addButtons.forEach(btn=>{
    btn.addEventListener('click', ()=>{
        const title = btn.getAttribute('data-title');
        const price = btn.getAttribute('data-price');

        // Əgər kitab artıq cart-də varsa, sayını artır
        let exist = cart.find(item => item.title === title);
        if(exist){
            exist.quantity = (exist.quantity || 1) + 1;
        } else {
            cart.push({title, price, quantity: 1});
        }

        saveCart();          // localStorage-yə yaz
        cartSidebar.classList.add('active');
        updateCart();
    });
});

window.addEventListener('load', updateCart);

closeCart.addEventListener('click', ()=>{
    cartSidebar.classList.remove('active');
});

</script>
     <?php  
    } 
    else echo "Kitab Tapilmadi!";
}
include "footer.php";
mysqli_close($conn);
?>