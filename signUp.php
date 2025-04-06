<?php
session_start();
include "includes/functions.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['request_otp'])) {
        // Handle OTP request
        $email = trim(strtolower($_POST['email']));
        $fname = trim($_POST['Fname']);
        $lname = trim($_POST['Lname']);
        $address = trim($_POST['address']);
        $passwd = trim($_POST['passwd']);

        // Basic validation
        if (empty($email) || empty($passwd) || empty($address) || empty($fname) || empty($lname)) {
            $_SESSION['message'] = "empty_err";
            header("Location: signUp.php");
            exit();
        } elseif (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) {
            $_SESSION['message'] = "signup_err_email";
            header("Location: signUp.php");
            exit();
        } elseif (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,30}$/', $passwd)) {
            $_SESSION['message'] = "signup_err_password";
            header("Location: signUp.php");
            exit();
        }

        // Check if email already exists
        $query = "SELECT email FROM user WHERE email='$email'";
        $data = query($query);
        if (!empty($data)) {
            $_SESSION['message'] = "usedEmail";
            header("Location: signUp.php");
            exit();
        }

        // Generate and store OTP
        $otp = rand(100000, 999999);
        $_SESSION['signup_otp'] = $otp;
        $_SESSION['signup_otp_expiry'] = time() + 300; // 5 minutes
        $_SESSION['signup_data'] = [
            'email' => $email,
            'fname' => $fname,
            'lname' => $lname,
            'address' => $address,
            'passwd' => $passwd
        ];

        // Send OTP
        if (send_otp_register($email, $otp)) {
            $_SESSION['show_otp_modal'] = true;
            $_SESSION['otp_message'] = "OTP sent to your email.";
        } else {
            $_SESSION['otp_message'] = "Failed to send OTP. Please try again.";
        }
        header("Location: signUp.php");
        exit();
    } elseif (isset($_POST['verify_otp'])) {
        // Handle OTP verification
        $entered_otp = $_POST['otp'];
        if ($entered_otp == $_SESSION['signup_otp'] && time() <= $_SESSION['signup_otp_expiry']) {
            // OTP verified, complete registration
            $signup_data = $_SESSION['signup_data'];
            $email = $signup_data['email'];
            $fname = $signup_data['fname'];
            $lname = $signup_data['lname'];
            $address = $signup_data['address'];
            $passwd = $signup_data['passwd'];

            $query = "INSERT INTO user (email, user_fname, user_lname, user_address, user_password) 
                     VALUES('$email', '$fname', '$lname', '$address', '$passwd')";
            $queryStatus = single_query($query);

            if ($queryStatus == "done") {
                $query = "SELECT user_id FROM user WHERE email='$email'";
                $data = query($query);
                $_SESSION['user_id'] = $data[0]['user_id'];
                
                // Clear OTP data
                unset($_SESSION['signup_otp']);
                unset($_SESSION['signup_otp_expiry']);
                unset($_SESSION['signup_data']);
                unset($_SESSION['show_otp_modal']);
                
                echo "<script>alert('OTP verified successfully');</script>";
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['message'] = "wentWrong";
                header("Location: signUp.php");
                exit();
            }
        } else {
            $_SESSION['otp_message'] = "Invalid or expired OTP.";
            $_SESSION['show_otp_modal'] = true;
            header("Location: signUp.php");
            exit();
        }
    } elseif (isset($_POST['resend_otp'])) {
        // Handle OTP resend
        if (isset($_SESSION['signup_data'])) {
            $email = $_SESSION['signup_data']['email'];
            $otp = rand(100000, 999999);
            $_SESSION['signup_otp'] = $otp;
            $_SESSION['signup_otp_expiry'] = time() + 300; // 5 minutes

            if (send_otp_register($email, $otp)) {
                $_SESSION['otp_message'] = "New OTP sent to your email.";
                $_SESSION['show_otp_modal'] = true;
            } else {
                $_SESSION['otp_message'] = "Failed to send OTP. Please try again.";
            }
        }
        header("Location: signUp.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="images/favicon.png">
    <link rel="stylesheet" href="css/register.css">
    <title>Sign Up</title>
    <style>
        .otp-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .otp-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            animation: modalopen 0.3s;
        }
        
        @keyframes modalopen {
            from {opacity: 0; transform: translateY(-50px);}
            to {opacity: 1; transform: translateY(0);}
        }
        
        .otp-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .otp-header h2 {
            margin: 0;
            color: rgb(25, 194, 213);
        }
        
        .close-otp {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close-otp:hover {
            color: #333;
        }
        
        .otp-input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .otp-btn {
            width: 100%;
            padding: 12px;
            background-color:rgb(25, 194, 213);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        
        .otp-btn:hover {
            background-color:rgb(16, 145, 159);
        }
        
        .otp-resend {
            text-align: center;
            margin-top: 15px;
        }
        
        .otp-resend a {
            color: rgb(226, 70, 70);
            cursor: pointer;
            text-decoration: none;
        }
        
        .otp-resend a:hover {
            text-decoration: underline;
        }
        
        .otp-message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        
        .otp-success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        
        .otp-error {
            background-color: #f2dede;
            color: #a94442;
        }
        
        .otp-info {
            background-color: #d9edf7;
            color: #31708f;
        }
        
        .otp-timer {
            text-align: center;
            margin: 10px 0;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <nav class="nav">
            <div class="nav-logo">
                <img style="width: 120px;" src="images/favicon.png" alt="logo">
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="#" class="link active">Home</a></li>
                    <li><a href="#" class="link">About</a></li>
                    <li><a href="members.html" class="link">Members</a></li>
                    <li><a href="#" class="link">Customers</a></li>
                </ul>
            </div>
            <div class="nav-btns">
                <a href="login.php"><button class="l-btn" id="loginBtn">Sign In</button></a>
                <button class="s-btn" id="registerBtn">Sign Up</button>
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>

        <div class="form-box">
            <?php message(); ?>

            <form class="register-container" id="register" action="signUp.php" method="post">
                <div class="top">
                    <span>Already have an account? <a href="login.php">Login</a></span>
                    <header>Sign Up to Curenet</header>
                </div>
                <div class="two-forms">
                    <div class="input-box">
                        <input type="text" class="input-field" placeholder="Firstname" name="Fname" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" placeholder="Lastname" name="Lname" required>
                        <i class="bx bx-user"></i>
                    </div>
                </div>
                <div class="input-box">
                    <input type="text" class="input-field" placeholder="Email" name="email" required>
                    <i class="bx bx-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" placeholder="Password" name="passwd" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="text" class="input-field" placeholder="Address..." name="address" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="submit" class="submit" value="Register" name="request_otp">
                </div>
            </form>
        </div>
    </div>

    <!-- OTP Verification Modal -->
    <div id="otpModal" class="otp-modal" style="<?php echo isset($_SESSION['show_otp_modal']) ? 'display: block;' : 'display: none;' ?>">
        <div class="otp-modal-content">
            <div class="otp-header">
                <h2>Verify Your Email</h2>
                <span class="close-otp" onclick="closeOtpModal()">&times;</span>
            </div>
            
            <?php if (isset($_SESSION['otp_message'])): ?>
                <div class="otp-message <?php echo strpos($_SESSION['otp_message'], 'Failed') !== false ? 'otp-error' : 'otp-info'; ?>">
                    <?php echo $_SESSION['otp_message']; unset($_SESSION['otp_message']); ?>
                </div>
            <?php endif; ?>
            
            <div class="otp-timer" id="otpTimer">
                OTP valid for: <span id="time">05:00</span>
            </div>
            
            <form method="post" action="signUp.php">
                <input type="number" class="otp-input" name="otp" placeholder="Enter 6-digit OTP" required min="100000" max="999999">
                <button type="submit" class="otp-btn" name="verify_otp">Verify OTP</button>
            </form>
            
            <div class="otp-resend">
                Didn't receive OTP? <a onclick="resendOtp()">Resend OTP</a>
            </div>
            
            <form id="resendForm" method="post" action="signUp.php">
                <input type="hidden" name="resend_otp" value="1">
            </form>
        </div>
    </div>

    <script>
        function myMenuFunction() {
            var i = document.getElementById("navMenu");

            if (i.className === "nav-menu") {
                i.className += " responsive";
            } else {
                i.className = "nav-menu";
            }
        }

        // OTP Timer
        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            var interval = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(interval);
                    display.textContent = "Expired";
                    document.querySelector('.otp-resend a').style.color = "red";
                }
            }, 1000);
        }

        // Initialize timer if OTP modal is visible
        window.onload = function() {
            var otpModal = document.getElementById('otpModal');
            if (otpModal.style.display === 'block') {
                var fiveMinutes = 60 * 5,
                    display = document.querySelector('#time');
                startTimer(fiveMinutes, display);
            }
        };

        function closeOtpModal() {
            document.getElementById('otpModal').style.display = 'none';
            // Redirect to clear the session if user closes the modal
            window.location.href = 'signUp.php?clear_otp=1';
        }

        function resendOtp() {
            document.getElementById('resendForm').submit();
        }

        // Show OTP modal if there's an OTP in session
        <?php if (isset($_GET['clear_otp'])): ?>
            // Clear OTP session if user closes the modal
            <?php 
                unset($_SESSION['signup_otp']);
                unset($_SESSION['signup_otp_expiry']);
                unset($_SESSION['signup_data']);
                unset($_SESSION['show_otp_modal']);
            ?>
        <?php endif; ?>
    </script>
</body>
</html>