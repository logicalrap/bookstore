<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookweb Bookstore</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles for the cart modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Bookworms Bookstore</h1>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Browse Books</a></li>
                <li><a href="#">Best Sellers</a></li>
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">Sign In</a></li>
                <li><a href="#">Sign Up</a></li>
                <li class="profile"><a href="#">Profile</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="hero">
            <h2>Discover Your Next Great Read</h2>
            <p>Explore our vast collection of books in various genres.</p>
            <a href="#" class="btn">Browse Books</a>
        </section>
        <section class="featured-books">
            <h2>Featured Books</h2>
            <!-- Featured book cards will be dynamically generated here -->
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Bookweb Bookstore. All rights reserved.</p>
    </footer>

    <!-- Checkout Form -->
    <form id="checkoutForm" action="process_payment.php" method="post">
        <input type="hidden" name="total_amount" id="totalAmount">
        <input type="submit" value="Checkout" class="btn">
    </form>

    <!-- Cart Pop-up Modal -->
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Shopping Cart</h2>
            <div id="cartItems"></div>
            <p>Total Amount: <span id="cartTotal"></span></p>
            <button onclick="checkout()" class="btn">Checkout</button>
        </div>
    </div>

    <script>
        // Fetch book data from the API
        async function fetchBooks() {
            const response = await fetch('https://all-books-api.p.rapidapi.com/getBooks', {
                headers: {
                    'X-RapidAPI-Key': '8772c0b214mshc28655990f5482bp14e7a5jsn4c817925cca9',
                    'X-RapidAPI-Host': 'all-books-api.p.rapidapi.com'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch book data');
            }

            return response.json();
        }

        // Render featured book cards
        async function renderFeaturedBooks() {
            const featuredBooksSection = document.querySelector('.featured-books');

            try {
                const booksData = await fetchBooks();

                booksData.forEach(book => {
                    const bookCard = document.createElement('div');
                    bookCard.classList.add('featured-book');
                    // Generate random price
                    const randomPrice = (Math.random() * (700 - 80) + 80).toFixed(2); // Generating price between 80 and 700
                    bookCard.innerHTML = `
                        <h3>${book.bookTitle}</h3>
                        <img src="${book.bookImage}" alt="${book.bookTitle} Cover">
                        <p>${book.bookDescription}</p>
                        <p>Price: <strong> K </strong> ${randomPrice}</p>
                        <button class="add-to-cart" onclick="addToCart('${book.bookTitle}', '${book.bookImage}', '${randomPrice}')">Add to Cart</button>
                    `;
                    featuredBooksSection.appendChild(bookCard);
                });
            } catch (error) {
                console.error('Error fetching and rendering featured books:', error);
            }
        }

        // Call the function to render featured books
        renderFeaturedBooks();

        // Cart functionality
        let cartItems = [];
        let cartTotal = 0;

        function addToCart(title, image, price) {
            cartItems.push({ title, image, price });
            cartTotal += parseFloat(price);
            updateCart();
            displayCart();
        }

        function updateCart() {
            const totalAmountInput = document.getElementById('totalAmount');
            totalAmountInput.value = cartTotal.toFixed(2);
        }

        // Display Cart Pop-up
        function displayCart() {
    const modal = document.getElementById('cartModal');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotalSpan = document.getElementById('cartTotal');

    cartItemsContainer.innerHTML = '';
    cartItems.forEach((item, index) => {
        const cartItemDiv = document.createElement('div');
        cartItemDiv.classList.add('cart-item');
        cartItemDiv.innerHTML = `
            <img src="${item.image}" alt="${item.title} Image">
            <div class="item-details">
                <p>${item.title}</p>
                <p>Price: K ${item.price}</p>
            </div>
            <button class="remove-item" onclick="removeItem(${index})">Remove</button>
        `;
        cartItemsContainer.appendChild(cartItemDiv);
    });

    cartTotalSpan.textContent = cartTotal.toFixed(2);

    modal.style.display = 'block';

    // Close the modal when the close button is clicked
    const closeBtn = document.getElementsByClassName('close')[0];
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };

    // Close the modal when clicked outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
}

function removeItem(index) {
    const removedItem = cartItems.splice(index, 1)[0];
    cartTotal -= parseFloat(removedItem.price);
    updateCart();
    displayCart();
}






        // Checkout function (to be implemented)
        function checkout() {
            // You can send cartItems and cartTotal to your PHP file for further processing
            // For now, let's just log the cart items and total
            console.log('Cart Items:', cartItems);
            console.log('Cart Total:', cartTotal.toFixed(2));
        }
    </script>
</body>
</html>
