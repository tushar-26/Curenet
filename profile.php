<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php'; // Include your database connection file
include 'includes/functions.php'; // Include your functions file

$user_id = $_SESSION['user_id'];
$user = get_user_details($user_id); // Function to get user details from the database
$orders = get_user_orders($user_id); // Function to get user orders from the database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_username'])) {
        // Handle username update
        $username = $_POST['username'];
        update_user_profile($user_id, $username, $user['email'], $user['password']);
        echo "<script>showMessage('Username updated successfully');</script>";
    } elseif (isset($_POST['update_email'])) {
        // Handle email update
        $email = $_POST['email'];
        update_user_profile($user_id, $user['user_fname'], $email, $user['password']);
        echo "<script>showMessage('Email updated successfully');</script>";
    } elseif (isset($_POST['request_otp'])) {
        // Handle OTP request
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + 300; // OTP expires in 5 minutes
        send_otp_email($user['email'], $otp); // Function to send OTP email
        echo "<script>showMessage('OTP sent to your email');</script>";
    } elseif (isset($_POST['verify_otp'])) {
        // Handle OTP verification
        $entered_otp = $_POST['otp'];
        if ($entered_otp == $_SESSION['otp'] && time() <= $_SESSION['otp_expiry']) {
            $_SESSION['otp_verified'] = true;
            echo "<script>showMessage('OTP verified successfully');</script>";
        } else {
            echo "<script>showMessage('Invalid or expired OTP');</script>";
        }
    } elseif (isset($_POST['update_password'])) {
        // Handle password update
        if (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']) {
            $password = $_POST['password'];
            update_user_profile($user_id, $user['user_fname'], $user['email'], $password);
            echo "<script>showMessage('Password updated successfully');</script>";
            unset($_SESSION['otp']);
            unset($_SESSION['otp_expiry']);
            unset($_SESSION['otp_verified']);
        } else {
            echo "<script>showMessage('Please verify OTP first');</script>";
        }
    } elseif (isset($_POST['delete_order'])) {
        // Handle order deletion
        $order_id = $_POST['order_id'];
        delete_user_order($order_id);
        echo "<script>showMessage('Order deleted successfully');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        h1, h2 {
            color: navy;
            text-align: center;
        }
        .profile-section, .orders-section {
            width: 48%;
        }
        .profile-section form, .orders-section ul {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background-color: #fff;
            margin-bottom: 10px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .order-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .order-details span {
            flex: 1;
        }
        .order-details form {
            margin: 0;
        }
        .order-status {
            font-weight: bold;
            margin-left: 10px;
        }
        .order-status.pending {
            color: red;
        }
        .order-status.shipped {
            color: green;
        }
        .tracking {
            margin-top: 20px;
        }
        .tracking ul {
            display: flex;
            justify-content: space-between;
            padding: 0;
        }
        .tracking li {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .tracking li:before {
            content: '';
            width: 100%;
            height: 2px;
            background-color: #ccc;
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: -1;
        }
        .tracking li:first-child:before {
            left: 0;
        }
        .tracking li:last-child:before {
            width: 50%;
        }
        .tracking li.active:before {
            background-color: #007bff;
        }
        .tracking li span {
            display: block;
            width: 20px;
            height: 20px;
            background-color: #ccc;
            border-radius: 50%;
            margin: 0 auto 10px;
            position: relative;
            z-index: 1;
        }
        .tracking li.active span {
            background-color: #007bff;
        }
        .message {
            display: none;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
            text-align: center;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
    <script>
        function confirmDelete(orderStatus) {
            if (orderStatus == 1) {
                var messageDiv = document.getElementById('error-message');
                messageDiv.style.display = 'block';
                setTimeout(function() {
                    messageDiv.style.display = 'none';
                }, 3000);
                return false;
            }
            return true;
        }

        function showMessage(message) {
            var messageDiv = document.getElementById('message');
            messageDiv.innerText = message;
            messageDiv.style.display = 'block';
            setTimeout(function() {
                messageDiv.style.display = 'none';
            }, 4000);
        }
    </script>
</head>
<body>
    <h2>User Profile</h2>
    <div class="container">
        <div id="message" class="message"></div>
        <div id="error-message" class="message error-message">Cannot delete order after it has been shipped.</div>
        
        <div class="profile-section">
            <h2>Manage Profile</h2>
            <form method="POST" action="profile.php">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $user['user_fname']; ?>" required>
                <button type="submit" name="update_username">Update Username</button>
            </form>
            <form method="POST" action="profile.php">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                <button type="submit" name="update_email">Update Email</button>
            </form>
            <form method="POST" action="profile.php">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="request_otp">Request OTP</button>
            </form>
            <?php if (isset($_SESSION['otp'])) { ?>
            <form method="POST" action="profile.php">
                <label for="otp">Enter OTP:</label>
                <input type="number" id="otp" name="otp" required>
                <button type="submit" name="verify_otp">Verify OTP</button>
            </form>
            <?php } ?>
            <?php if (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']) { ?>
            <form method="POST" action="profile.php">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="password" required>
                <button type="submit" name="update_password">Update Password</button>
            </form>
            <?php } ?>
        </div>

        <div class="orders-section">
            <h2>Manage Orders</h2>
            <ul>
                <?php foreach ($orders as $order) { ?>
                    <li>
                        <div class="order-details">
                            <span>Order ID: <?php echo $order['order_id']; ?> - Item ID: <?php echo $order['item_id']; ?> - Quantity: <?php echo $order['order_quantity']; ?> - Date: <?php echo $order['order_date']; ?></span>
                            <span class="order-status <?php echo $order['order_status'] == 1 ? 'shipped' : 'pending'; ?>">
                                <?php echo $order['order_status'] == 1 ? 'Shipped' : 'Pending'; ?>
                            </span>
                            <form method="POST" action="profile.php" onsubmit="return confirmDelete(<?php echo $order['order_status']; ?>);">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <button type="submit" name="delete_order">Delete Order</button>
                            </form>
                        </div>
                        <div class="tracking">
                            <ul>
                                <li class="<?php echo $order['order_status'] == 0 ? 'active' : ''; ?>"><span></span>Order Placed</li>
                                <li class="<?php echo $order['order_status'] == 1 ? 'active' : ''; ?>"><span></span>Shipped</li>
                                <li class="<?php echo $order['order_status'] == 2 ? 'active' : ''; ?>"><span></span>Out for Delivery</li>
                                <li class="<?php echo $order['order_status'] == 3 ? 'active' : ''; ?>"><span></span>Delivered</li>
                            </ul>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</body>
</html>