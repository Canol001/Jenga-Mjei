<?php
session_start();
require_once "db_connect.php"; // Include the connection

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    // ------------------- GET ALL SALES -------------------
    case 'get_sales':
        $result = $conn->query("
            SELECT s.id, s.invoice_number, s.customer_id, s.customer_name, s.subtotal, s.tax, s.total, 
                   s.payment_method, s.status, s.cashier, s.sale_date, s.created_date,
                   GROUP_CONCAT(si.product_id) as product_ids,
                   GROUP_CONCAT(si.product_name) as product_names,
                   GROUP_CONCAT(si.sku) as skus,
                   GROUP_CONCAT(si.quantity) as quantities,
                   GROUP_CONCAT(si.price) as prices,
                   GROUP_CONCAT(si.total) as totals
            FROM sales s
            LEFT JOIN sale_items si ON s.id = si.sale_id
            GROUP BY s.id
            ORDER BY s.sale_date DESC
        ");
        $sales = [];
        while ($row = $result->fetch_assoc()) {
            $items = [];
            if ($row['product_ids']) {
                $productIds = explode(',', $row['product_ids']);
                $productNames = explode(',', $row['product_names']);
                $skus = explode(',', $row['skus']);
                $quantities = explode(',', $row['quantities']);
                $prices = explode(',', $row['prices']);
                $totals = explode(',', $row['totals']);
                
                for ($i = 0; $i < count($productIds); $i++) {
                    $items[] = [
                        'productId' => $productIds[$i],
                        'productName' => $productNames[$i],
                        'sku' => $skus[$i],
                        'quantity' => (int)$quantities[$i],
                        'price' => (float)$prices[$i],
                        'total' => (float)$totals[$i]
                    ];
                }
            }
            $sales[] = [
                '_id' => (string)$row['id'],
                'invoiceNumber' => $row['invoice_number'],
                'customerId' => (string)$row['customer_id'],
                'customerName' => $row['customer_name'],
                'items' => $items,
                'subtotal' => (float)$row['subtotal'],
                'tax' => (float)$row['tax'],
                'total' => (float)$row['total'],
                'paymentMethod' => $row['payment_method'],
                'status' => $row['status'],
                'cashier' => $row['cashier'],
                'saleDate' => $row['sale_date'],
                'createdDate' => $row['created_date']
            ];
        }
        echo json_encode(['success' => true, 'items' => $sales]);
        break;

    // ------------------- GET ALL PRODUCTS -------------------
    case 'get_products':
        $result = $conn->query("SELECT id, name, price, stock, sku FROM products WHERE status = 'active'");
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = [
                '_id' => (string)$row['id'],
                'name' => $row['name'],
                'price' => (float)$row['price'],
                'stock' => (int)$row['stock'],
                'sku' => $row['sku']
            ];
        }
        echo json_encode(['success' => true, 'items' => $products]);
        break;

    // ------------------- GET ALL CUSTOMERS -------------------
    case 'get_customers':
        $result = $conn->query("SELECT id, name, email, phone FROM customers");
        $customers = [];
        while ($row = $result->fetch_assoc()) {
            $customers[] = [
                '_id' => (string)$row['id'],
                'name' => $row['name'],
                'email' => $row['email'],
                'phone' => $row['phone']
            ];
        }
        echo json_encode(['success' => true, 'items' => $customers]);
        break;

    // ------------------- ADD SALE -------------------
    case 'add_sale':
        $data = json_decode(file_get_contents('php://input'), true);
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("
                INSERT INTO sales (invoice_number, customer_id, customer_name, subtotal, tax, total, payment_method, status, cashier, sale_date, created_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                'sissddsssss',
                $data['invoiceNumber'],
                $data['customerId'],
                $data['customerName'],
                $data['subtotal'],
                $data['tax'],
                $data['total'],
                $data['paymentMethod'],
                $data['status'],
                $data['cashier'],
                $data['saleDate'],
                $data['createdDate']
            );
            $stmt->execute();
            $saleId = $conn->insert_id;

            $stmt = $conn->prepare("
                INSERT INTO sale_items (sale_id, product_id, product_name, sku, quantity, price, total)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            foreach ($data['items'] as $item) {
                $stmt->bind_param(
                    'iissidd',
                    $saleId,
                    $item['productId'],
                    $item['productName'],
                    $item['sku'],
                    $item['quantity'],
                    $item['price'],
                    $item['total']
                );
                $stmt->execute();
            }

            foreach ($data['items'] as $item) {
                $stmt = $conn->prepare("UPDATE products SET stock = stock - ?, updated_date = NOW() WHERE id = ?");
                $stmt->bind_param('ii', $item['quantity'], $item['productId']);
                $stmt->execute();
            }

            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    // Include existing product endpoints from previous response
    case 'get_products': // Already defined above
    case 'add_product':
    case 'update_product':
    case 'delete_product':
        // ... (copy from previous api.php)
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}

$conn->close();
?>