<?php
include "includes/head.php"
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    
    <!-- Add Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Enhanced CAPTCHA Styles */
        .captcha-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .captcha-container {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 400px;
            transform: translateY(-50px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .captcha-container.show {
            transform: translateY(0);
            opacity: 1;
        }

        .captcha-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .captcha-header h4 {
            color: #2c3e50;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .captcha-body {
            position: relative;
        }

        .captcha-code {
            background: linear-gradient(45deg, #f8f9fa 25%, #e9ecef 25%, #e9ecef 50%, #f8f9fa 50%, #f8f9fa 75%, #e9ecef 75%);
            background-size: 20px 20px;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            letter-spacing: 5px;
            text-align: center;
            margin-bottom: 1rem;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .reload-button {
            background: none;
            border: none;
            color: #3498db;
            cursor: pointer;
            transition: transform 0.3s ease;
            margin-left: 10px;
        }

        .reload-button:hover {
            transform: rotate(180deg);
            color: #2980b9;
        }

        #captchaInput {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: border-color 0.3s ease;
        }

        #captchaInput:focus {
            border-color: #3498db;
            outline: none;
        }

        .captcha-submit {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            width: 100%;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .captcha-submit:hover {
            background: #2980b9;
        }

        /* Payment Method Styling */
        .payment-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            transition: border-color 0.3s ease;
        }

        .payment-option:hover {
            border-color: #3498db;
        }

        .payment-header {
            background: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .payment-body {
            padding: 1.5rem;
        }

        .upi-logos img {
            height: 40px;
            margin-right: 15px;
        }
    </style>
</head>

<body>
    <div class="site-wrap">
        <?php
        include "includes/header.php";
        $data = get_user($_SESSION['user_id']);
        ?>

        <div class="bg-light py-3">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 mb-0">
                        <a href="index.php">Home</a> <span class="mx-2 mb-0">/</span>
                        <strong class="text-black">Checkout</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="site-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <h2 class="h3 mb-3 text-black">Delivery Details</h2>
                                <div class="p-3 p-lg-5 border">
                                    <table class="table site-block-order-table mb-5">
                                        <thead>
                                            <th>Customer Details</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>First Name </td>
                                                <td><?php echo $data[0]['user_fname'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Last Name </td>
                                                <td><?php echo $data[0]['user_lname'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Email </td>
                                                <td><?php echo $data[0]['email'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Address </td>
                                                <td><?php echo $data[0]['user_address'] ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <h2 class="h3 mb-3 text-black">Your Order</h2>
                                <div class="p-3 p-lg-5 border">
                                    <table class="table site-block-order-table mb-5">
                                        <thead>
                                            <th>Product</th>
                                            <th>Total</th>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($_SESSION['cart'])) {
                                                $data = get_cart();
                                                $num = sizeof($data);
                                                for ($i = 0; $i < $num; $i++) {
                                                    if (isset($data[$i])) {
                                            ?>
                                                        <tr>
                                                            <td><?php echo $data[$i][0]['item_title'] ?><strong class="mx-2">x</strong><?php echo $_SESSION['cart'][$i]['quantity'] ?></td>
                                                            <td>₹<?php echo ($data[$i][0]['item_price'] * $_SESSION['cart'][$i]['quantity'])  ?></td>
                                                        </tr>
                                            <?php
                                                    }
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td class="text-black font-weight-bold"><strong>Cart Subtotal</strong></td>
                                                <td class="text-black">₹<?php echo total_price($data) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-black font-weight-bold"><strong>Delivery Fees</strong></td>
                                                <td class="text-black">₹<?php echo delivery_fees($data) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-black font-weight-bold"><strong>Order Total</strong></td>
                                                <td class="text-black font-weight-bold"><strong>₹<?php echo delivery_fees($data) + total_price($data) ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="form-group">
                                        <h3 class="h4 mb-3 text-black">Payment Options</h3>
                                        
                                        <!-- Debit Card Payment -->
                                        <div class="payment-option">
                                            <div class="payment-header">
                                                <h4 class="h5 mb-0">Debit Card</h4>
                                            </div>
                                            <div class="payment-body">
                                                <form id="debitCardForm" action="thankyou.php?order=done" method="post">
                                                    <div class="form-group">
                                                        <label for="card_name">Name on Card</label>
                                                        <input type="text" class="form-control" id="card_name" name="card_name" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="card_number">Card Number</label>
                                                        <input type="text" class="form-control" id="card_number" name="card_number" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="card_expiry">Expiry Date</label>
                                                                <input type="month" class="form-control" id="card_expiry" name="card_expiry" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="card_cvv">CVV</label>
                                                                <input type="text" class="form-control" id="card_cvv" name="card_cvv" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="validateForm('debitCardForm')">
                                                        Pay with Debit Card
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- QR Code Payment -->
                                        <div class="payment-option">
                                            <div class="payment-header">
                                                <h4 class="h5 mb-0">QR Code</h4>
                                            </div>
                                            <div class="payment-body text-center">
                                                <p class="mb-3">Scan the QR code to pay</p>
                                                <img src="images/qr.jpg" alt="QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                                                <form id="qrCodeForm" action="thankyou.php?order=done" method="post">
                                                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="validateForm('qrCodeForm')">
                                                        Confirm QR Payment
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- UPI Payment -->
                                        <div class="payment-option">
                                            <div class="payment-header">
                                                <h4 class="h5 mb-0">UPI Payment</h4>
                                            </div>
                                            <div class="payment-body">
                                                <div class="upi-logos mb-3">
                                                    <img src="images/googlepay.png" alt="Google Pay">
                                                    <img src="images/phonepe.png" alt="PhonePe">
                                                </div>
                                                <form id="upiForm" action="thankyou.php?order=done" method="post">
                                                    <div class="form-group">
                                                        <label for="upi_id">UPI ID</label>
                                                        <input type="text" class="form-control" id="upi_id" name="upi_id" placeholder="example@upi" required>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="validateForm('upiForm')">
                                                        Pay via UPI
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Cash on Delivery -->
                                        <div class="payment-option">
                                            <div class="payment-header">
                                                <h4 class="h5 mb-0">Cash on Delivery</h4>
                                            </div>
                                            <div class="payment-body">
                                                <form id="codForm" action="thankyou.php?order=done" method="post">
                                                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="validateForm('codForm')">
                                                        Confirm Cash Payment
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "includes/footer.php" ?>
    </div>

    <!-- CAPTCHA Popup -->
    <div id="captchaPopup" class="captcha-overlay">
        <div class="captcha-container">
            <div class="captcha-header">
                <h4>Security Verification</h4>
                <p>Please enter the code below to confirm your order</p>
            </div>
            <div class="captcha-body">
                <div style="display: flex; align-items: center; justify-content: center;">
                    <div id="captchaCode" class="captcha-code"></div>
                    <button type="button" class="reload-button" onclick="reloadCaptcha()">
                        <i class="fas fa-redo-alt fa-lg"></i>
                    </button>
                </div>
                <input type="text" id="captchaInput" class="form-control" placeholder="Enter CAPTCHA code" required>
                <button type="button" class="captcha-submit" onclick="submitCaptcha()">Verify & Continue</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentFormId = '';

        function showCaptchaPopup(formId) {
            currentFormId = formId;
            reloadCaptcha();
            const overlay = document.getElementById('captchaPopup');
            const container = document.querySelector('.captcha-container');
            overlay.style.display = 'flex';
            setTimeout(() => container.classList.add('show'), 10);
        }

        function hideCaptchaPopup() {
            const overlay = document.getElementById('captchaPopup');
            const container = document.querySelector('.captcha-container');
            container.classList.remove('show');
            setTimeout(() => overlay.style.display = 'none', 300);
        }

        function reloadCaptcha() {
            $.ajax({
                url: 'generate_captcha.php',
                success: function(data) {
                    document.getElementById('captchaCode').innerText = data;
                    document.getElementById('captchaInput').value = '';
                }
            });
        }

        function submitCaptcha() {
            const captchaInput = document.getElementById('captchaInput').value;
            $.ajax({
                type: 'POST',
                url: 'verify_captcha.php',
                data: { captcha: captchaInput },
                success: function(response) {
                    if (response === 'success') {
                        hideCaptchaPopup();
                        document.getElementById(currentFormId).submit();
                    } else {
                        alert('Incorrect CAPTCHA. Please try again.');
                        reloadCaptcha();
                    }
                }
            });
        }

        function validateForm(formId) {
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll('input[required]');
            let valid = true;

            inputs.forEach(input => {
                if (!input.value) {
                    valid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (valid) {
                showCaptchaPopup(formId);
            } else {
                alert('Please fill out all required fields.');
            }
        }
    </script>
</body>

</html>