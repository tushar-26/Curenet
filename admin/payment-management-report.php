<?php
include "includes/head.php";

?>

<body>
    <?php
    include "includes/header.php";
    include "includes/sidebar.php";
    ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <?php
        $data = get_order_report();
        $total_revenue = calculate_total_revenue($data);
        $total_expenses = calculate_total_expenses($data);
        $net_profit_loss = $total_revenue - $total_expenses;
        $total_orders = count($data);
        $top_selling_products = get_top_selling_products();
        $customer_growth_rate = calculate_customer_growth_rate();
        $pending_payments = count_pending_payments($data);
        $inventory_levels = get_inventory_levels();
        $monthly_performance = get_monthly_performance();
        $yearly_performance = get_yearly_performance();

        // Search functionality
        $search_query = '';
        if (isset($_GET['search'])) {
            $search_query = $_GET['search'];
            $data = array_filter($data, function($order) use ($search_query) {
                return stripos($order['order_id'], $search_query) !== false ||
                       stripos($order['user_id'], $search_query) !== false ||
                       stripos($order['item_id'], $search_query) !== false;
            });
        }
        ?>

        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }

            .navbar {
                background-color: #007bff;
                color: #fff;
                padding: 10px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .navbar a {
                color: #fff;
                text-decoration: none;
                margin: 0 15px;
                font-size: 14px;
            }

            .navbar a:hover {
                color: #ffdd57;
            }

            .navbar .logo {
                font-size: 24px;
                font-weight: bold;
            }

            .navbar .search-bar {
                flex-grow: 1;
                margin: 0 20px;
            }

            .navbar .search-bar input {
                width: 100%;
                padding: 8px;
                border: none;
                border-radius: 4px;
            }

            .navbar .user-actions {
                display: flex;
                align-items: center;
            }

            .navbar .user-actions .icon {
                margin-left: 20px;
                font-size: 18px;
            }

            .container {
                padding: 20px;
            }

            .report-summary {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 20px;
            }

            .report-summary div {
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .report-summary div h2 {
                margin: 0;
                font-size: 18px;
                color: #333;
            }

            .report-summary div p {
                margin: 10px 0 0;
                font-size: 24px;
                color: #007bff;
            }

            .custom-table {
                width: 100%;
                border-collapse: collapse;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .custom-table th, .custom-table td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
            }

            .custom-table th {
                background-color: #007bff;
                color: white;
            }

            .custom-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .custom-table tr:hover {
                background-color: #f1f1f1;
            }

            .btn-primary {
                background-color: #007bff;
                border: none;
                color: #fff;
                padding: 10px 20px;
                text-decoration: none;
                display: inline-block;
                margin-top: 20px;
                border-radius: 4px;
                cursor: pointer;
            }

            .btn-primary:hover {
                background-color: #0056b3;
            }

            .inventory-dropdown {
                position: relative;
            }

            .inventory-dropdown-content {
                display: none;
                position: absolute;
                background-color: #fff;
                min-width: 160px;
                box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                z-index: 1;
                border-radius: 4px;
            }

            .inventory-dropdown-content ul {
                list-style-type: none;
                margin: 0;
                padding: 0;
            }

            .inventory-dropdown-content ul li {
                padding: 12px 16px;
                text-decoration: none;
                display: block;
                color: #333;
            }

            .inventory-dropdown-content ul li:hover {
                background-color: #f1f1f1;
            }

            .inventory-dropdown:hover .inventory-dropdown-content {
                display: block;
            }
        </style>

        <div class="container">
            <div class="report-summary">
                <div>
                    <h2>Total Revenue</h2>
                    <p>₹<?php echo $total_revenue; ?></p>
                </div>
                <div>
                    <h2>Total Expenses</h2>
                    <p>₹<?php echo $total_expenses; ?></p>
                </div>
                <div>
                    <h2>Net Profit/Loss</h2>
                    <p>₹<?php echo $net_profit_loss; ?></p>
                </div>
                <div>
                    <h2>Total Orders/Transactions</h2>
                    <p><?php echo $total_orders; ?></p>
                </div>
                <div>
                    <h2>Top-Selling Products</h2>
                    <p><?php echo implode(', ', $top_selling_products); ?></p>
                </div>
                <div>
                    <h2>Customer Growth Rate</h2>
                    <p><?php echo $customer_growth_rate; ?>%</p>
                </div>
                <div>
                    <h2>Pending Payments</h2>
                    <p><?php echo $pending_payments; ?></p>
                </div>
                <div class="inventory-dropdown">
                    <h2>Stock/Inventory Levels</h2>
                    <p>hover to view</p>
                    <div class="inventory-dropdown-content">
                        <ul>
                            <?php foreach ($inventory_levels as $level) { ?>
                                <li><?php echo $level; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div>
                    <h2>Monthly Performance</h2>
                    <p><?php echo implode(', ', $monthly_performance); ?></p>
                </div>
                <div>
                    <h2>Yearly Performance</h2>
                    <p><?php echo implode(', ', $yearly_performance); ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-black">Payment Management Report</h2>
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User ID</th>
                                <th>Item ID</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)) { ?>
                                <?php foreach ($data as $order) { ?>
                                    <tr>
                                        <td><?php echo $order['order_id']; ?></td>
                                        <td><?php echo $order['user_id']; ?></td>
                                        <td><?php echo $order['item_id']; ?></td>
                                        <td><?php echo $order['order_quantity']; ?></td>
                                        <td><?php echo $order['order_status'] ? 'Completed' : 'Pending'; ?></td>
                                        <td><?php echo $order['order_date']; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="6" class="text-center">No order records found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 text-right">
                    <a href="download-report.php" class="btn btn-primary">Download Report as PDF</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>