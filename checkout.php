<?php
include "includes/head.php"
?>

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
                    <div class="border p-3 mb-3">
                      <h4 class="h5 mb-3">Debit Card</h4>
                      <form action="thankyou.php?order=done" method="post">
                        <div class="form-group">
                          <label for="card_name">Name on Card</label>
                          <input type="text" class="form-control" id="card_name" name="card_name" required>
                        </div>
                        <div class="form-group">
                          <label for="card_number">Card Number</label>
                          <input type="text" class="form-control" id="card_number" name="card_number" required>
                        </div>
                        <div class="form-group">
                          <label for="card_expiry">Expiry Date</label>
                          <input type="date" class="form-control" id="card_expiry" name="card_expiry" required>
                        </div>
                        <div class="form-group">
                          <label for="card_cvv">CVV</label>
                          <input type="text" class="form-control" id="card_cvv" name="card_cvv" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Pay with Debit Card</button>
                      </form>
                    </div>

                    <div class="border p-3 mb-3">
                      <h4 class="h5 mb-3">QR Code</h4>
                      <p>Scan the QR code below to pay:</p>
                      <img src="images/qr.jpg" alt="QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                      <form action="thankyou.php?order=done" method="post">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Pay with QR Code</button>
                      </form>
                    </div>

                    <div class="border p-3 mb-3">
                      <h4 class="h5 mb-3" style="display: inline-block;">UPI Payment</h4>
                      
                        <img src="images/googlepay.png" alt="Google Pay" class="img-fluid" style="max-width: 55px; margin-right: 10px;">
                        <img src="images/phonepe.png" alt="PhonePe" class="img-fluid" style="max-width: 42px;">
                      
                      <p>Enter your UPI ID to pay:</p>
                      <form action="thankyou.php?order=done" method="post">
                        <div class="form-group">
                          <label for="upi_id">UPI ID</label>
                          <input type="text" class="form-control" id="upi_id" name="upi_id" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Pay with UPI</button>
                      </form>
                     
                    </div>

                    <div class="border p-3 mb-3">
                      <h4 class="h5 mb-3">Cash on Delivery</h4>
                      <form action="thankyou.php?order=done" method="post">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Pay with Cash on Delivery</button>
                      </form>
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </div>
        <!-- </form> -->
      </div>
    </div>
    <?php
    include "includes/footer.php"
    ?>
  </div>

</body>

</html>