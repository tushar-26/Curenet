<?php
include "includes/head.php"
?>

<body>
    <div class="site-wrap">
        <?php
        include "includes/header.php";
        add_order();
        ?>

        <div class="bg-light py-3">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 mb-0"><a href="index.php">Home</a> <span class="mx-2 mb-0">/</span> <strong class="text-black">Thank You</strong></div>
                </div>
            </div>
        </div>

        <div class="site-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <span class="icon-check_circle display-3 text-success"></span>
                        <h2 class="display-3 text-black">Thank you!</h2>
                        <p class="lead mb-3">Your order was successfully completed.</p>
                        <div class="alert alert-info" style="max-width: 500px; margin: 20px auto;">
                            <i class="fas fa-envelope-open-text"></i> 
                            We've sent a confirmation email to your inbox. 
                            Please check your email for order details.
                        </div>
                        <p class="text-muted" style="max-width: 600px; margin: 0 auto;">
                            You'll receive another email with tracking information once your order ships. 
                            You can download the invoice directly from your profile after shipping.
                        </p>
                        <p class="mt-5">
                            <a href="store.php" class="btn btn-md height-auto px-4 py-3 btn-primary">
                                <i class="fas fa-store"></i> Back to store
                            </a>
                            <a href="profile.php" class="btn btn-md height-auto px-4 py-3 btn-outline-secondary">
                                <i class="fas fa-user"></i> View Orders
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <?php include "includes/footer.php" ?>
    </div>
</body>
</html>