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

        // Calculate total amount
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount +=  2;//$item['price'];

        }

         // file name process_payment.php
        // Collect card payment with Flutterwave
        $url = 'https://api.flutterwave.com/v3/charges?type=card';

        // Prepare request body
        $body = [
            'amount' => $totalAmount, // Total amount to be charged
            'email' => 'user@example.com', // Customer's email
            'tx_ref' => '20220430-123456789', // Unique reference for the transaction
            // Add other required parameters like card number, CVV, expiry, etc.
            // Example:
            'card_number' => '5531886652142950',
            'cvv' => '564',
            'expiry_month' => '09',
            'expiry_year' => '32',
            'currency' => 'ZMW', // Currency code (e.g., NGN, USD)
            'redirect_url' => 'https://www.flutterwave.ng', // Redirect URL after payment completion
            // Add more parameters as needed
        ];

        var_dump($body['amount']);

        // Convert the body data to JSON format
        $jsonData = json_encode($body);

        // Encrypt the payload using OpenSSL
        $encryptedPayload = encryptPayload($jsonData);

        // Make a POST request to Flutterwave API using cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encryptedPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            // Add your Flutterwave API key here
            'Authorization: Bearer FLWSECK_TEST-66db95d8d928b5ddd1c2c922c4260332-X',
        ]);

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            $responseData = ['error' => curl_error($ch)];
        } else {
            $responseData = json_decode($response, true);
        }

        // Close curl resource
        curl_close($ch);

        // Check if the payment was successful and contains a redirect URL
        if (isset($responseData['data']['redirect_url'])) {
            // Redirect the user to the Flutterwave payment page
            header('Location: ' . $responseData['data']['redirect_url']);
            exit;
        } else {
            // Payment failed or no redirect URL provided
            echo json_encode(['error' => 'Payment failed or no redirect URL provided']);
        }

    } else {
        echo json_encode(['error' => 'No cart items data received']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

// Function to encrypt the payload using OpenSSL
function encryptPayload($data) {
    // Replace with your actual encryption key (32 bytes for AES-256-CBC)
    $encryptionKey = "YOUR_ENCRYPTION_KEY_HERE";

    // Generate a random initialization vector (IV)
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Encrypt the data using AES-256-CBC cipher
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $encryptionKey, OPENSSL_RAW_DATA, $iv);

    // Combine the IV and encrypted data for transmission
    $encryptedPayload = base64_encode($iv . $encryptedData);

    return $encryptedPayload;
}
