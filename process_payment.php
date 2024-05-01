<?php

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if JSON data is received
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Check if cart items data is received
    if (isset($data['cartItems'])) {
        // Retrieve cart items from the data
        $cartItems = $data['cartItems'];

        // Process cart items data (you can perform any desired operations here)
        // For now, let's simply print the cart items
        foreach ($cartItems as $item) {
            echo "Title: " . $item['title'] . ", Price: K " . $item['price'] . "\n";
        }
    } else {
        echo "No cart items data received";
    }
} else {
    echo "Invalid request method";
}
?>
