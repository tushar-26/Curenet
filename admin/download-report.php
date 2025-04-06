<?php
require 'vendor/autoload.php';
include "includes/functions.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Dompdf\Dompdf;

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
        h1, h2 {
            color: #333;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            margin: 10px 0;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            background: #e0e0e0;
            margin: 5px 0;
            padding: 10px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1>Payment Management Report</h1>
    <h2>Total Revenue: ' . $total_revenue . '</h2>
    <h2>Total Expenses: ' . $total_expenses . '</h2>
    <h2>Net Profit/Loss: ' . $net_profit_loss . '</h2>
    <h2>Total Orders/Transactions: ' . $total_orders . '</h2>
    <h2>Top-Selling Products: ' . implode(', ', $top_selling_products) . '</h2>
    <h2>Customer Growth Rate: ' . $customer_growth_rate . '%</h2>
    <h2>Pending Payments/Outstanding Dues: ' . $pending_payments . '</h2>
    <h2>Stock/Inventory Levels:</h2>
    <ul>';
foreach ($inventory_levels as $level) {
    $html .= '<li>' . $level . '</li>';
}
$html .= '</ul>
    <h2>Monthly Performance Trends: ' . implode(', ', $monthly_performance) . '</h2>
    <h2>Yearly Performance Trends: ' . implode(', ', $yearly_performance) . '</h2>
    <table>
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
        <tbody>';
foreach ($data as $order) {
    $html .= '<tr>';
    $html .= '<td>' . $order['order_id'] . '</td>';
    $html .= '<td>' . $order['user_id'] . '</td>';
    $html .= '<td>' . $order['item_id'] . '</td>';
    $html .= '<td>' . $order['order_quantity'] . '</td>';
    $html .= '<td>' . ($order['order_status'] ? 'Completed' : 'Pending') . '</td>';
    $html .= '<td>' . $order['order_date'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody>
    </table>
</body>
</html>';

// Output HTML for debugging
// echo $html;

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("analysis_report.pdf", array("Attachment" => 1));
?>