<?php
$connection = mysqli_connect("localhost", "root", "", "Pharma");

function post_redirect($url)
{
    ob_start();
    header('Location: ' . $url);
    ob_end_flush();
    die();
}

function get_redirect($url)
{
    echo " <script> 
    window.location.href = '" . $url . "'; 
    </script>";
}

function query($query)
{
    global $connection;
    $run = mysqli_query($connection, $query);
    if ($run) {
        while ($row = $run->fetch_assoc()) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data;
        } else {
            return "";
        }
    } else {
        return 0;
    }
}

function single_query($query)
{
    global $connection;
    if (mysqli_query($connection, $query)) {
        return "done";
    } else {
        die("no data" . mysqli_connect_error($connection));
    }
}

function login()
{
    if (isset($_POST['login'])) {
        $userEmail = trim(strtolower($_POST['userEmail']));
        $password = trim($_POST['password']);
        if (empty($userEmail) or empty($password)) {
            $_SESSION['message'] = "empty_err";
            post_redirect("login.php");
        }
        $query = "SELECT email, user_id, user_password FROM user WHERE email= '$userEmail'";
        $data = query($query);
        if (empty($data)) {
            $_SESSION['message'] = "loginErr";
            post_redirect("login.php");
        } elseif ($password == $data[0]['user_password'] and $userEmail == $data[0]['email']) {
            $_SESSION['user_id'] = $data[0]['user_id'];
            post_redirect("index.php");
        } else {
            $_SESSION['message'] = "loginErr";
            post_redirect("login.php");
        }
    }
}

function singUp()
{
    if (isset($_POST['singUp'])) {
        $email = trim(strtolower($_POST['email']));
        $fname = trim($_POST['Fname']);
        $lname = trim($_POST['Lname']);
        $address = trim($_POST['address']);
        $passwd = trim($_POST['passwd']);
        if (empty($email) or empty($passwd) or empty($address) or empty($fname) or empty($lname)) {
            $_SESSION['message'] = "empty_err";
            post_redirect("signUp.php");
        } elseif (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) {
            $_SESSION['message'] = "signup_err_email";
            post_redirect("signUp.php");
        } elseif (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,30}$/', $passwd)) {
            $_SESSION['message'] = "signup_err_password";
            post_redirect("signUp.php");
        }
        $query = "SELECT email FROM user";
        $data = query($query);
        $count = sizeof($data);
        for ($i = 0; $i < $count; $i++) {
            if ($email == $data[$i]['email']) {
                $_SESSION['message'] = "usedEmail";
                post_redirect("signUp.php");
            }
        }
        $query = "INSERT INTO user (email, user_fname, user_lname, user_address, user_password) VALUES('$email', '$fname', '$lname', '$address', '$passwd')";
        $queryStatus = single_query($query);
        $query = "SELECT user_id FROM user WHERE email='$email'";
        $data = query($query);
        $_SESSION['user_id'] = $data[0]['user_id'];
        if ($queryStatus == "done") {
            post_redirect("index.php");
        } else {
            $_SESSION['message'] = "wentWrong";
            post_redirect("signUp.php");
        }
    }
}



function message()
{
    if (isset($_SESSION['message'])) {
        echo "<div class='container mt-3'>"; // Container for proper spacing
        
        $message_type = $_SESSION['message'];
        unset($_SESSION['message']); // Clear message immediately after retrieval

        $messages = [
            "signup_err_password" => "Please enter the password in correct format (8-30 chars, at least 1 letter and 1 number)!",
            "loginErr" => "The email or password is incorrect!",
            "usedEmail" => "This email is already registered!",
            "wentWrong" => "Something went wrong! Please try again.",
            "empty_err" => "Please fill in all required fields!",
            "signup_err_email" => "Please enter a valid email address!"
        ];

        if (array_key_exists($message_type, $messages)) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='color: red; font-weight:bold; font-family: Verdana;background-color: white; padding: 40px; border: solid red 8px;'>
                    {$messages[$message_type]}
                  </div>
                  <script>
                    // Auto-dismiss after 5 seconds
                    setTimeout(() => {
                        document.querySelector('.alert').remove();
                    }, 4000);
                  </script>";
        }

        echo "</div>"; // Close container
    }
}

function search()
{
    if (isset($_GET['search'])) {
        $search_text = $_GET['search'];
        if ($search_text == "") {
            return;
        }
        $query = "SELECT * FROM item WHERE item_tags LIKE '%$search_text%'";
        $data = query($query);
        if (empty($data)) {
            return "no result";
        } else {
            return $data;
        }
    } elseif (isset($_GET['cat'])) {
        $cat = $_GET['cat'];
        $query = "SELECT * FROM item WHERE item_cat='$cat' ORDER BY RAND()";
        $data = query($query);
        return $data;
    } elseif (isset($_GET['store'])) {
        $data = all_products();
        return $data;
    }
}

function all_products()
{
    $query = "SELECT * FROM item ORDER BY RAND()";
    $data = query($query);
    return $data;
}

function total_price($data)
{
    $sum = 0;
    $num = sizeof($data);
    for ($i = 0; $i < $num; $i++) {
        $sum += ($data[$i][0]['item_price'] * $_SESSION['cart'][$i]['quantity']);
    }
    return $sum;
}

function get_item()
{
    if (isset($_GET['product_id'])) {
        $_SESSION['item_id'] = $_GET['product_id'];
        $id = $_GET['product_id'];
        $query = "SELECT * FROM item WHERE item_id='$id'";
        $data = query($query);
        return $data;
    }
}

function get_user($id)
{
    $query = "SELECT user_id, user_fname, user_lname, email, user_address FROM user WHERE user_id=$id";
    $data = query($query);
    return $data;
}

function add_cart($item_id)
{
    $user_id = $_SESSION['user_id'];
    if (isset($_GET['quantity'])) {
        $quantity = $_GET['quantity'];
    }
    if (empty($user_id)) {
        get_redirect("login.php");
    } else {
        if (isset($_GET['cart'])) {
            if (isset($_SESSION['cart'])) {
                $num = sizeof($_SESSION['cart']);
                $_SESSION['cart'][$num]['user_id'] = $user_id;
                $_SESSION['cart'][$num]['item_id'] = $item_id;
                $_SESSION['cart'][$num]['quantity'] = $quantity;
                get_redirect("cart.php");
            } else {
                $_SESSION['cart'][0]['user_id'] = $user_id;
                $_SESSION['cart'][0]['item_id'] = $item_id;
                $_SESSION['cart'][0]['quantity'] = $quantity;
                get_redirect("cart.php");
            }
        }
        if (isset($_SESSION['cart'])) {
            $num = sizeof($_SESSION['cart']);
            for ($i = 0; $i < $num; $i++) {
                for ($j = $i + 1; $j < $num; $j++) {
                    if ($_SESSION['cart'][$i]['item_id'] == $_SESSION['cart'][$j]['item_id']) {
                        $_SESSION['cart'][$i]['quantity'] += $_SESSION['cart'][$j]['quantity'];
                        unset($_SESSION['cart'][$j]);
                        $_SESSION['cart'] = array_values($_SESSION['cart']);
                    }
                }
            }
        }
    }
}

function get_cart()
{
    $num = sizeof($_SESSION['cart']);
    if (isset($num)) {
        for ($i = 0; $i < $num; $i++) {
            $item_id = $_SESSION['cart'][$i]['item_id'];
            $query = "SELECT item_id, item_image, item_title, item_quantity, item_price, item_brand FROM item WHERE item_id='$item_id'";
            $data[$i] = query($query);
        }
        return $data;
    }
}

function delete_from_cart()
{
    if (isset($_GET['delete'])) {
        $item_id = $_GET['delete'];
        $num = sizeof($_SESSION['cart']);
        for ($i = 0; $i < $num; $i++) {
            if ($_SESSION['cart'][$i]['item_id'] == $item_id) {
                unset($_SESSION['cart'][$i]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
        get_redirect("cart.php");
    } elseif (isset($_GET['delete_all'])) {
        unset($_SESSION['cart']);
        get_redirect("cart.php");
    }
}

function add_order()
{
    if (isset($_GET['order'])) {
        $num = sizeof($_SESSION['cart']);
        date_default_timezone_set("Asia/Kolkata");
        $date = date("Y-m-d");
        $order_details = array();
        $total_amount = 0;

        // Get user email
        $user_id = $_SESSION['user_id'];
        $user = get_user_details($user_id);
        $user_email = $user['email'];

        // Process order
        for ($i = 0; $i < $num; $i++) {
            $item_id = $_SESSION['cart'][$i]['item_id'];
            $user_id = $_SESSION['cart'][$i]['user_id'];
            $quantity = $_SESSION['cart'][$i]['quantity'];
            
            if ($quantity == 0) continue;

            // Get item details
            $item = get_item_id($item_id);
            
            // Add to order details for email
            $order_details[] = array(
                'title' => $item[0]['item_title'],
                'quantity' => $quantity,
                'price' => $item[0]['item_price'] * $quantity
            );

            // Create order
            $query = "INSERT INTO orders (user_id, item_id, order_quantity, order_date) 
                    VALUES('$user_id', '$item_id', '$quantity', '$date')";
            single_query($query);
            
            // Update stock
            $new_quantity = $item[0]['item_quantity'] - $quantity;
            $query = "UPDATE item SET item_quantity='$new_quantity' WHERE item_id = '$item_id'";
            single_query($query);
        }

        // Calculate totals
        $subtotal = total_price(get_cart());
        $delivery = delivery_fees(get_cart());
        $total_amount = $subtotal + $delivery;

        // Send confirmation email
        send_order_confirmation_email($user_email, $order_details, $total_amount);

        unset($_SESSION['cart']);
    } else {
        get_redirect("index.php");
    }
}

function check_user($id)
{
    $query = "SELECT user_id FROM user where user_id='$id'";
    $row = query($query);
    if (empty($row)) {
        return 0;
    } else {
        return 1;
    }
}

function get_item_id($id)
{
    $query = "SELECT * FROM item WHERE item_id= '$id'";
    $data = query($query);
    return $data;
}

function all_products_reverse()
{
    $query = "SELECT * FROM item";
    $data = query($query);
    return array_reverse($data);
}

function delivery_fees($data)
{
    if (total_price($data) < 200) {
        $num = sizeof($data);
        return $num * 40;
    } else {
        return 0;
    }
}

function get_user_details($user_id)
{
    global $connection;
    $query = "SELECT user_id, user_fname, user_lname, email, user_address FROM user WHERE user_id = '$user_id'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        return mysqli_fetch_assoc($result);
    } else {
        return null;
    }
}

function get_user_orders($user_id)
{
    global $connection;
    $query = "SELECT order_id, item_id, order_quantity, order_date, order_status FROM orders WHERE user_id = '$user_id'";
    $result = mysqli_query($connection, $query);
    $orders = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    }
    return $orders;
}

function update_user_profile($user_id, $username = null, $email = null, $password = null, $address = null)
{
    global $connection;
     // Check if username already exists for another user
     if ($username !== null) {
        $username = mysqli_real_escape_string($connection, $username);
        $query = "SELECT COUNT(*) AS count FROM user WHERE user_fname = '$username' AND user_id != '$user_id'";
        $result = mysqli_query($connection, $query);
        $row = mysqli_fetch_assoc($result);
        if ($row['count'] > 0) {
            echo "<script>showMessage('Username already exists. Please choose a different one.', 'error');</script>";
            return false;
        }
    }

    // Check if email already exists for another user
    if ($email !== null) {
        $email = mysqli_real_escape_string($connection, $email);
        $query = "SELECT COUNT(*) AS count FROM user WHERE email = '$email' AND user_id != '$user_id'";
        $result = mysqli_query($connection, $query);
        $row = mysqli_fetch_assoc($result);
        if ($row['count'] > 0) {
            echo "<script>showMessage('Email already exists. Please use a different one.', 'error');</script>";
            return false;
        }
    }
    $updates = [];

    if ($username !== null) {
        $username = mysqli_real_escape_string($connection, $username);
        $updates[] = "user_fname = '$username'";
    }

    if ($email !== null) {
        $email = mysqli_real_escape_string($connection, $email);
        $updates[] = "email = '$email'";
    }
    if ($address !== null) {
        $address = mysqli_real_escape_string($connection, $address);
        $updates[] = "user_address = '$address'";
    }

    if ($password !== null) {
        $password = mysqli_real_escape_string($connection, $password);
        $updates[] = "user_password = '$password'";
    }

    if (!empty($updates)) {
        $query = "UPDATE user SET " . implode(', ', $updates) . " WHERE user_id = '$user_id'";
        return mysqli_query($connection, $query);
    }

    return false;
}

function delete_user_order($order_id)
{
    global $connection;
    $query = "DELETE FROM orders WHERE order_id = '$order_id'";
    return mysqli_query($connection, $query);
}

function get_order_details($order_id)
{
    global $connection;
    $query = "SELECT * FROM orders WHERE order_id = '$order_id'";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($result);
}
function get_product_details($item_id) {
    global $connection;
    $query = "SELECT * FROM item WHERE item_id = '$item_id'";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($result);
}

//otps----------------------------------------------------------------------------------------------------------
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function send_otp_email($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'curenet782@gmail.com'; // Replace with your Gmail
        $mail->Password = 'rhhhdokcdswepiqb'; // Replace with your Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('curenet782@gmail.com'); // Replace with your Gmail
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Password Change';
        $mail->Body = "Your OTP for changing your password is: <b>$otp</b>. This OTP is valid for 5 minutes.";

        // Enable debugging if needed
         //$mail->SMTPDebug = SMTP::DEBUG_SERVER;

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
function send_otp_register($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'curenet782@gmail.com';
        $mail->Password = 'rhhhdokcdswepiqb';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('curenet782@gmail.com');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email - Curenet Pharmacy';
        
        // Styled email body
        $mail->Body = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Email Verification</title>
            <style>
                body {
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 0;
                    background-color: #f5f7fa;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background-color: #19c2d5;
                    padding: 30px 20px;
                    text-align: center;
                }
                .header img {
                    height: 50px;
                }
                .content {
                    padding: 30px;
                }
                h1 {
                    color:rgb(3, 117, 75);
                    margin-top: 0;
                    font-size: 24px;
                }
                .otp-container {
                    background-color: #f8f9fa;
                    border-radius: 6px;
                    padding: 15px;
                    text-align: center;
                    margin: 25px 0;
                    border: 1px dashed #19c2d5;
                }
                .otp-code {
                    font-size: 32px;
                    letter-spacing: 3px;
                    color: #19c2d5;
                    font-weight: bold;
                    margin: 10px 0;
                }
                .button {
                    display: inline-block;
                    padding: 12px 24px;
                    background-color: #19c2d5;
                    color: white !important;
                    text-decoration: none;
                    border-radius: 4px;
                    font-weight: bold;
                    margin: 15px 0;
                }
                .footer {
                    background-color: #f5f7fa;
                    padding: 20px;
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                }
                .note {
                    font-size: 14px;
                    color: #666;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="header">
                    <h1>Welcome to Curenet</h1>
                </div>
                
                <div class="content">
                    <p>Hello,</p>
                    <p>Thank you for registering with Curenet Pharmacy. To complete your registration, please verify your email address by entering the following OTP code:</p>
                    
                    <div class="otp-container">
                        <p>Your verification code is:</p>
                        <div class="otp-code">'.$otp.'</div>
                        <p>This code will expire in 5 minutes.</p>
                    </div>
                    
                    <p>If you didn\'t request this code, you can safely ignore this email.</p>
                    
                    <div class="note">
                        <p><strong>Note:</strong> For your security, please do not share this code with anyone.</p>
                    </div>
                </div>
                
                <div class="footer">
                    <p>&copy; '.date('Y').' Curenet Pharmacy. All rights reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>';

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

function send_order_confirmation_email($user_email, $order_details, $total_amount) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'curenet782@gmail.com';
        $mail->Password = 'rhhhdokcdswepiqb';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('curenet782@gmail.com', 'Curenet Pharmacy');
        $mail->addAddress($user_email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmation - Curenet Pharmacy';

        // Styled email body
        $mail->Body = '
        <html>
        <head>
            <style>
                .email-container { max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; }
                .header { background-color: #19c2d5; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background-color: #f8f9fa; }
                .order-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .order-table th { background-color: #19c2d5; color: white; padding: 12px; text-align: left; }
                .order-table td { padding: 12px; border-bottom: 1px solid #ddd; }
                .footer { background-color: #e9ecef; padding: 20px; text-align: center; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="header">
                <img src="favicon.png" style="width:80px;">
                    <h2>Thank you for your order!</h2>
                </div>
                
                <div class="content">
                    <p>Dear valued customer,</p>
                    <p>Your order has been successfully placed. Here are your order details:</p>
                    
                    <table class="order-table">
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>';
        
        foreach ($order_details as $item) {
            $mail->Body .= '
                        <tr>
                            <td>'.$item['title'].'</td>
                            <td>'.$item['quantity'].'</td>
                            <td>₹'.$item['price'].'</td>
                        </tr>';
        }

        $mail->Body .= '
                        <tr>
                            <td colspan="2" style="text-align: right;"><strong>Total Amount:</strong></td>
                            <td>₹'.$total_amount.'</td>
                        </tr>
                    </table>
                     <p>You will receive another email with tracking information once your order has updated by admin.</p>
                    <p>You will be able to download your invoice As PDF</p>
                    <p>Thank you for choosing Curenet</p>
                </div>

                <div class="footer">
                    <p>This is an automated message, please do not reply directly to this email</p>
                    <p>© '.date('Y').' Curenet. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Order confirmation email failed: " . $mail->ErrorInfo);
        return false;
    }
}


function get_order_report()
{
    $query = "SELECT * FROM orders ORDER BY order_date DESC";
    $data = query($query);
    return $data;
}

function calculate_total_revenue($data)
{
    $total = 0;
    foreach ($data as $order) {
        if ($order['order_status'] == 1) { // Assuming 1 means 'Completed'
            $item = get_item_id($order['item_id']);
            $total += $item[0]['item_price'] * $order['order_quantity'];
        }
    }
    return $total;
}

function count_pending_payments($data)
{
    $count = 0;
    foreach ($data as $order) {
        if ($order['order_status'] == 0) { // Assuming 0 means 'Pending'
            $count++;
        }
    }
    return $count;
}

function count_completed_transactions($data)
{
    $count = 0;
    foreach ($data as $order) {
        if ($order['order_status'] == 1) { // Assuming 1 means 'Completed'
            $count++;
        }
    }
    return $count;
}
// Add these functions to your existing functions.php file

function calculate_total_expenses($data)
{
   
    $total_expenses = 0;
    foreach ($data as $order) {
        $item = get_item_id($order['item_id']);
        $cost_price = $item[0]['item_price'] * 0.5; // 50% of selling price
        $total_expenses += $cost_price * $order['order_quantity'];
    }
    return $total_expenses;
}

function get_top_selling_products()
{
    $query = "SELECT item_id, SUM(order_quantity) as total_quantity FROM orders GROUP BY item_id ORDER BY total_quantity DESC LIMIT 5";
    $data = query($query);
    $top_selling_products = [];
    foreach ($data as $item) {
        $item_details = get_item_id($item['item_id']);
        $top_selling_products[] = $item_details[0]['item_title'];
    }
    return $top_selling_products;
}

function calculate_customer_growth_rate()
{
    $query = "SELECT COUNT(user_id) as total_users FROM user";
    $data = query($query);
    $total_users = $data[0]['total_users'];

    // Assuming you have a way to get the number of users from the previous period
    $previous_period_users = 50; // Example value
    $growth_rate = (($total_users - $previous_period_users) / $previous_period_users) * 100;
    return $growth_rate;
}

function get_inventory_levels()
{
    $query = "SELECT item_title, item_quantity FROM item";
    $data = query($query);
    $inventory_levels = [];
    foreach ($data as $item) {
        $inventory_levels[] = $item['item_title'] . ": " . $item['item_quantity'];
    }
    return $inventory_levels;
}

function get_monthly_performance()
{
    $query = "SELECT DATE_FORMAT(order_date, '%Y-%m') as month, SUM(order_quantity) as total_quantity FROM orders GROUP BY month ORDER BY month DESC";
    $data = query($query);
    $monthly_performance = [];
    foreach ($data as $month) {
        $monthly_performance[] = $month['month'] . ": " . $month['total_quantity'];
    }
    return $monthly_performance;
}

function get_yearly_performance()
{
    $query = "SELECT DATE_FORMAT(order_date, '%Y') as year, SUM(order_quantity) as total_quantity FROM orders GROUP BY year ORDER BY year DESC";
    $data = query($query);
    $yearly_performance = [];
    foreach ($data as $year) {
        $yearly_performance[] = $year['year'] . ": " . $year['total_quantity'];
    }
    return $yearly_performance;
}
?>