<?php
//require 'vendor/autoload.php';
include 'includes/functions.php';

use Dompdf\Dompdf;

if (!isset($_GET['order_id'])) {
    die("Order ID is required.");
}

$order_id = $_GET['order_id'];
$order = get_order_details($order_id);
$user = get_user_details($order['user_id']);
$product = get_product_details($order['item_id']); // Fetch product details

if (!$order || !$user || !$product) {
    die("Invalid order, user, or product.");
}

$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #007bff;
        }
        .header p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .section p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        .section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .section table, .section th, .section td {
            border: 1px solid #ddd;
        }
        .section th, .section td {
            padding: 12px;
            text-align: left;
        }
        .section th {
            background-color: #007bff;
            color: white;
        }
        .section tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .section tr:hover {
            background-color: #f1f1f1;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Curenet Invoice</h1>
            <p>Thank you for shopping with Curenet!</p>
        </div>
        <div class="section">
            <h2>Customer Details</h2>
            <p><strong>Name:</strong> ' . $user['user_fname'] . ' ' . $user['user_lname'] . '</p>
            <p><strong>Email:</strong> ' . $user['email'] . '</p>
            <p><strong>Address:</strong> ' . $user['user_address'] . '</p>
        </div>
        <div class="section">
            <h2>Order Details</h2>
            <table>
                <tr>
                    <th>Order ID</th>
                    <td>' . $order['order_id'] . '</td>
                </tr>
                <tr>
                    <th>Item ID</th>
                    <td>' . $order['item_id'] . '</td>
                </tr>
                <tr>
                    <th>Product Name</th>
                    <td>' . $product['item_title'] . '</td>
                </tr>
                <tr>
                    <th>Quantity</th>
                    <td>' . $order['order_quantity'] . '</td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>' . $order['order_date'] . '</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>' . ($order['order_status'] == 1 ? 'Shipped' : ($order['order_status'] == 2 ? 'Out for Delivery' : 'Pending')) . '</td>
                </tr>
            </table>
        </div>
        <div class="footer">
            <p>&copy; ' . date("Y") . ' Curenet. All rights reserved.</p>
            <p>For any inquiries, please contact us:</p>
            <p><strong>Help Email:</strong> <a href="mailto:curenet57@gmail.com">curenet57@gmail.com</a></p>
            <p><strong>Help Mobile:</strong> +91 6353747334</p>
            <p>Please note that this is an automatically generated invoice and does not require a signature.</p>
            <p>If you have any questions regarding your order, feel free to reach out to our customer support team.</p>
            <p>Thank you for choosing Curenet. We hope to see you again soon!</p>
        </div>
    </div>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("invoice_$order_id.pdf", array("Attachment" => 1));
?>