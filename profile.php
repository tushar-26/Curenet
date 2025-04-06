<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/functions.php'; // Include your functions file

$user_id = $_SESSION['user_id'];
$user = get_user_details($user_id); // Fetch user details, including email
$orders = get_user_orders($user_id); // Fetch user orders

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_username'])) {
        // Handle username update
        $username = $_POST['username'];
        if (update_user_profile($user_id, $username, null, null, null)) {
            $_SESSION['message'] = "Username updated successfully";
        } else {
            $_SESSION['message'] = "Username already exists. Please choose a different one.";
        }
        header("Location: profile.php");
        exit();
    } elseif (isset($_POST['update_email'])) {
        // Handle email update
        $email = $_POST['email'];
        if (update_user_profile($user_id, null, $email, null, null)) {
            $_SESSION['message'] = "Email updated successfully";
        } else {
            $_SESSION['message'] = "Email already exists. Please use a different one.";
        }
        header("Location: profile.php");
        exit();
    } elseif (isset($_POST['update_address'])) {
        // Handle address update
        $address = $_POST['address'];
        update_user_profile($user_id, null, null, null, $address);
        $_SESSION['message'] = "Address updated successfully";
        header("Location: profile.php");
        exit();
    } elseif (isset($_POST['request_otp'])) {
        // Handle OTP request
        $otp = rand(100000, 999999); // Generate a 6-digit OTP
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + 300; // OTP expires in 5 minutes

        // Send OTP to the user's email
        if (send_otp_email($user['email'], $otp)) {
            $_SESSION['message'] = "OTP sent to your email. Please check your inbox.";
        } else {
            $_SESSION['message'] = "Failed to send OTP. Please try again.";
        }
    } elseif (isset($_POST['verify_otp'])) {
        // Handle OTP verification
        $entered_otp = $_POST['otp'];
        if ($entered_otp == $_SESSION['otp'] && time() <= $_SESSION['otp_expiry']) {
            $_SESSION['otp_verified'] = true;
             $_SESSION['message'] = "OTP verified successfully.";
        } else {
            $_SESSION['message'] = "Invalid or expired OTP. Please try again.";
        }
    } elseif (isset($_POST['update_password'])) {
        // Handle password update
        if (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']) {
            $password = $_POST['password'];
            update_user_profile($user_id, null, null, $password, null);
            $_SESSION['message'] = "Password updated successfully";
            unset($_SESSION['otp']);
            unset($_SESSION['otp_expiry']);
            unset($_SESSION['otp_verified']);
            header("Location: profile.php");
            exit();
        } else {
            $_SESSION['message'] = "Please Verifiy OTP first.";
        }
    } elseif (isset($_POST['delete_order'])) {
        // Handle order deletion
        $order_id = $_POST['order_id'];
        $order = get_order_details($order_id); // Fetch order details including status

        if ($order['order_status'] == 1) { // 1 indicates shipped
            $_SESSION['order_message'] = "Cannot cancel
 order after it has been shipped.";
            header("Location: profile.php");
            exit();
        } elseif ($order['order_status'] == 2) { // 2 indicates out for delivery
            $_SESSION['order_message'] = "Cannot cancel
 order after it has been out for delivery.";
            header("Location: profile.php");
            exit();
        }

        delete_user_order($order_id);
        $_SESSION['order_message'] = "Order cancelled
 successfully";
        header("Location: profile.php");
        exit();
    }
}

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$order_message = isset($_SESSION['order_message']) ? $_SESSION['order_message'] : '';
unset($_SESSION['message']);
unset($_SESSION['order_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/profile.css">
    <script>
        function showMessage(message, type) {
            var messageDiv = document.getElementById(type + '-message');
            messageDiv.innerText = message;
            messageDiv.style.display = 'block';
            setTimeout(function() {
                messageDiv.style.display = 'none';
            }, 5000);
        }

        // Automatically hide messages after 5 seconds
        window.onload = function() {
            var message = "<?php echo $message; ?>";
            var orderMessage = "<?php echo $order_message; ?>";
            if (message) {
                showMessage(message, 'profile');
            }
            if (orderMessage) {
                showMessage(orderMessage, 'order');
            }

            var messageDivs = document.querySelectorAll('.message');
            messageDivs.forEach(function(messageDiv) {
                if (messageDiv) {
                    setTimeout(function() {
                        messageDiv.style.display = 'none';
                    }, 5000);
                }
            });

            <?php if (isset($_SESSION['order_message'])) { ?>
                showMessage('<?php echo $_SESSION['order_message']; ?>', 'order');
                <?php unset($_SESSION['order_message']); ?>
            <?php } ?>
        };

        function confirmDelete(orderId, orderStatus) {
            if (orderStatus == 1) { // 1 indicates shipped
                showMessage('Cannot delete order after it has been shipped.', 'order');
                return;
            } else if (orderStatus == 2) { // 2 indicates out for delivery
                showMessage('Cannot delete order after it has been out for delivery.', 'order');
                return;
            }
            var modal = document.getElementById('deleteModal');
            var orderIdInput = document.getElementById('modalOrderId');
            orderIdInput.value = orderId;
            modal.style.display = 'block';
        }

        function closeModal() {
            var modal = document.getElementById('deleteModal');
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            var modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h2>User Profile</h2>
    <div class="container">
        <div class="profile-section">
            <h2>Manage Profile</h2>
            <div id="profile-message" class="message" style="display: none;"></div>
            <?php if (isset($_SESSION['message'])) { ?>
                <div id="profile-message" class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php } ?>
            
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
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo $user['user_address']; ?>" required>
                <button type="submit" name="update_address">Update Address</button>
            </form>
            <form method="POST" action="profile.php">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter new password..." required>
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
                <label for="new_password">RE-Enter New Password to Confirm:</label>
                <input type="password" id="new_password" name="password" required>
                <button type="submit" name="update_password">Update Password</button>
            </form>
            <?php } ?>
        </div>

        <div class="orders-section">
            <h2>Manage Orders</h2>
            <div id="order-message" class="message error-message" style="display: none;"></div>
            <ul>
                <?php foreach ($orders as $order) { ?>
                    <li>
                        <div class="order-details">
                            <span>Order ID: <?php echo $order['order_id']; ?> - Item ID: <?php echo $order['item_id']; ?> - Quantity: <?php echo $order['order_quantity']; ?> - Date: <?php echo $order['order_date']; ?></span>
                            <span class="order-status <?php echo $order['order_status'] == 1 || $order['order_status'] == 2 ? 'shipped' : 'pending'; ?>">
                                <?php echo $order['order_status'] == 1 ? 'Shipped' : ($order['order_status'] == 2 ? 'Out for Delivery' : 'Pending'); ?>
                            </span>
                            <button type="button" onclick="confirmDelete(<?php echo $order['order_id']; ?>, <?php echo $order['order_status']; ?>)">Cancel Order</button>
                            <?php if ($order['order_status'] == 1 || $order['order_status'] == 2) { ?>
                                <a href="download-invoice.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-primary" style="background-color: rgb(17, 193, 190);">Download Invoice</a>
                            <?php } ?>
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

    <!-- The Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Cancel Order</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" action="profile.php">
                    <input type="hidden" id="modalOrderId" name="order_id">
                    <label for="cancel_reason">Reason for cancellation:</label>
                    <textarea id="cancel_reason" name="cancel_reason" required></textarea>
                    <div class="modal-footer">
                        <button type="submit" name="delete_order">Submit and Cancel Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>