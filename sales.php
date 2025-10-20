<?php
ob_start(); // Start output buffering to prevent header errors
session_start();
include 'config.php';


// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page
    header("Location: homepage.php"); 
    exit();
}

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle new sale submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_sale'])) {
    $customer = trim($_POST['customer']);
    $payment_method = trim($_POST['payment_method']);
    $discount = floatval($_POST['discount'] ?? 0);
    $sale_date = trim($_POST['sale_date']);
    $items = json_decode($_POST['sale_items'], true); // Expecting JSON array of items

    
    try {
      
        $pdo->beginTransaction();

        // Generate unique invoice number (e.g., INV- followed by timestamp)
        $invoice_number = 'INV-' . time();

        // Calculate total
        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $items));
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax - $discount;
        $sale_id = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        // Insert into sales table
        $stmt = $pdo->prepare("INSERT INTO sales (sale_id, invoice_number, customer_name, total_amount, payment_method, sale_date, status, created_at) VALUES (:sale_id, :invoice, :customer, :total, :payment, :date, :status, NOW())");
        $stmt->execute([
            ':sale_id' => $sale_id,
            ':invoice' => $invoice_number,
            ':customer' => $customer,
            ':total' => $total,
            ':payment' => $payment_method,
            ':date' => $sale_date,
            ':status' => 'completed'
        ]);
        //$sale_id = $pdo->lastInsertId();

        // Insert items into sale_items table
        $itemStmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_name, sku, price, quantity) VALUES (:sale_id, :product, :sku, :price, :quantity)");
        foreach ($items as $item) {
            $itemStmt->execute([
                ':sale_id' => $sale_id,
                ':product' => $item['name'],
                ':sku' => $item['sku'],
                ':price' => $item['price'],
                ':quantity' => $item['quantity']
            ]);
        }

        $pdo->commit();
        $_SESSION['message'] = "Sale processed successfully! Invoice: $invoice_number";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error processing sale: " . $e->getMessage();
    }
    header("Location: sales.php"); // Corrected to match filename
    exit();
}

// Fetch sales history
$stmt = $pdo->query("SELECT * FROM sales ORDER BY created_at DESC LIMIT 10");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    /* Responsive table adjustments */
    @media (max-width: 640px) {
      table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }
      thead {
        display: none;
      }
      tbody tr {
        display: block;
        margin-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
      }
      tbody td {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 1rem;
        text-align: left;
      }
      tbody td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #4b5563;
        margin-right: 1rem;
      }
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">
<?php include 'header.php'; ?>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start mb-8 space-y-4 sm:space-y-0">
      <div>
        <h1 class="text-3xl sm:text-4xl font-bold text-black mb-4">SALES MANAGEMENT</h1>
        <p class="text-base sm:text-lg text-gray-600 italic">Process sales, manage invoices, and track transactions</p>
      </div>
      <button id="openNewSale" class="bg-blue-600 text-white px-4 py-2 rounded-md flex items-center hover:bg-blue-700 transition-colors">
        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
        New Sale
      </button>
    </div>

    <!-- Search -->
    <div class="bg-white border border-gray-200 rounded-lg mb-6 p-6 shadow-sm">
      <div class="relative">
        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
        <input placeholder="Search sales by invoice or customer name..." class="w-full pl-10 border border-gray-300 rounded-md p-2" />
      </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="p-4">
        <h3 class="text-xl font-bold text-black flex items-center">
          <i data-lucide="shopping-cart" class="w-5 h-5 mr-2"></i>
          SALES HISTORY (<?php echo count($sales); ?>)
        </h3>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="bg-green-100 text-green-800 p-2 mb-4 text-center">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="bg-red-100 text-red-800 p-2 mb-4 text-center">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <p class="text-gray-600 italic">View and manage all sales transactions</p>
      </div>
      <div class="p-4 overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="py-2 text-gray-600 font-semibold">Invoice</th>
              <th class="py-2 text-gray-600 font-semibold">Customer</th>
              <th class="py-2 text-gray-600 font-semibold">Items</th>
              <th class="py-2 text-gray-600 font-semibold">Total</th>
              <th class="py-2 text-gray-600 font-semibold">Payment</th>
              <th class="py-2 text-gray-600 font-semibold">Date</th>
              <th class="py-2 text-gray-600 font-semibold">Status</th>
              <th class="py-2 text-gray-600 font-semibold">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sales as $sale) {
                // Fetch items for this sale
                $itemStmt = $pdo->prepare("SELECT product_name, sku, price, quantity FROM sale_items WHERE sale_id = :sale_id");
                $itemStmt->execute([':sale_id' => $sale['sale_id']]);
                $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
                $itemCount = count($items);
            ?>
              <tr class="border-b border-gray-100 hover:bg-gray-50">
                <td class="py-2 font-medium text-black" data-label="Invoice"><?php echo htmlspecialchars($sale['invoice_number']); ?></td>
                <td class="py-2 text-gray-600" data-label="Customer"><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                <td class="py-2 text-gray-600" data-label="Items"><?php echo $itemCount . ' item' . ($itemCount !== 1 ? 's' : ''); ?></td>
                <td class="py-2 font-bold text-black" data-label="Total">Ksh.<?php echo number_format($sale['total_amount'], 2); ?></td>
                <td class="py-2 flex items-center space-x-1 text-gray-600" data-label="Payment">
                  <i data-lucide="banknote" class="w-4 h-4"></i><span><?php echo strtolower(htmlspecialchars($sale['payment_method'])); ?></span>
                </td>
                <td class="py-2 text-gray-600" data-label="Date"><?php echo date('m/d/Y', strtotime($sale['sale_date'])); ?></td>
                <td class="py-2" data-label="Status"><span class="badge bg-blue-600 text-white"><?php echo htmlspecialchars($sale['status']); ?></span></td>
                <td class="py-2" data-label="Actions">
                  <button class="openSaleDetails border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors" data-id="<?php echo $sale['sale_id']; ?>">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </button>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- New Sale Modal -->
    <div id="newSaleDialog" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl p-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
              <i data-lucide="shopping-cart" class="w-6 h-6 text-blue-600"></i>
              NEW SALE
            </h2>
            <p class="text-gray-500 italic font-normal">Create a new sales transaction</p>
          </div>
          <button id="closeNewSale" class="text-gray-500 hover:text-gray-800 transition">
            <i data-lucide="x" class="w-6 h-6"></i>
          </button>
        </div>

        <form id="newSaleForm" method="POST" action="sales.php">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left -->
            <div class="space-y-6">
              <div>
  <label class="text-gray-600 font-medium">Customer</label>
  <select name="customer" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required>
    <option value="">Select customer</option>
    <option value="walk-in">Walk-in Customer</option>

    <?php
    // Fetch customers from the database
    $custStmt = $pdo->query("SELECT customer_id, name, phone, status FROM customers ORDER BY name ASC");
    while ($cust = $custStmt->fetch(PDO::FETCH_ASSOC)) {
        $statusBadge = $cust['status'] === 'active' ? '🟢' : '🔴';
        echo '<option value="' . htmlspecialchars($cust['customer_id']) . '">'
            . $statusBadge . ' '
            . htmlspecialchars($cust['name']) . ' - ' . htmlspecialchars($cust['phone'])
            . '</option>';
    }
    ?>
  </select>
</div>

              <div>
                <label class="text-gray-600 font-medium">Add Products</label>
                <div class="relative mt-1">
                  <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                  <input id="productSearch" placeholder="Search products..." class="w-full pl-10 border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" />
                </div>
                <div id="productList" class="mt-2 border border-gray-200 rounded-lg max-h-48 overflow-y-auto">
                  <?php
                  $productStmt = $pdo->query("SELECT name, sku, cost_price FROM products LIMIT 5"); // Adjust query as needed
                  while ($product = $productStmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<div class="p-3 hover:bg-gray-100 border-b border-gray-200 flex justify-between items-center cursor-pointer" data-product="' . htmlspecialchars($product['name']) . '" data-sku="' . htmlspecialchars($product['sku']) . '" data-price="' . htmlspecialchars($product['cost_price']) . '">
                        <div>
                          <p class="font-medium text-black">' . htmlspecialchars($product['name']) . '</p>
                          <p class="text-sm text-gray-600 italic">' . htmlspecialchars($product['sku']) . ' - Stock: 8</p>
                        </div>
                        <p class="font-bold text-black">Ksh.' . number_format($product['cost_price'], 2) . '</p>
                      </div>';
                  }
                  ?>
                </div>
              </div>
              <div>
                <label class="text-gray-600 font-medium">Discount (Optional)</label>
                <input type="number" name="discount" step="0.01" placeholder="Enter discount amount or %" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" />
              </div>
              <div>
                <label class="text-gray-600 font-medium">Date</label>
                <input type="date" name="sale_date" value="<?php echo date('Y-m-d'); ?>" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
              </div>
            </div>

            <!-- Right -->
            <div class="space-y-6">
              <div>
                <label class="text-gray-600 font-medium">Sale Items</label>
                <div id="saleItems" class="mt-1 border border-gray-200 rounded-lg p-4 min-h-[300px]">
                  <p class="text-gray-400 italic text-center">No items added yet</p>
                </div>
              </div>
              <div>
                <label class="text-gray-600 font-medium">Payment Method</label>
                <select name="payment_method" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required>
                  <option value="cash">Cash</option>
                  <option value="card">Card</option>
                  <option value="mobile">Mobile</option>
                </select>
              </div>
              <div class="border-t border-gray-200 pt-4 space-y-2">
                <div class="flex justify-between"><span class="text-gray-600">Subtotal:</span><span id="subtotal" class="font-bold text-black">Ksh.0.00</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Tax (10%):</span><span id="tax" class="font-bold text-black">Ksh.0.00</span></div>
                <div class="flex justify-between text-lg"><span class="font-bold">Total:</span><span id="total" class="font-bold text-black">Ksh.0.00</span></div>
              </div>
              <div class="flex space-x-3">
                <button type="button" id="cancelNewSale" class="flex-1 border border-gray-200 text-gray-900 px-4 py-2 rounded-md hover:bg-gray-100 transition-colors">Cancel</button>
                <button type="submit" name="process_sale" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center gap-2 transition-colors">
                  <i data-lucide="receipt" class="w-5 h-5"></i>
                  Process Sale
                </button>
              </div>
            </div>
          </div>
          <input type="hidden" name="sale_items" id="saleItemsJson">
        </form>
      </div>
    </div>

    <!-- Sale Details Modal -->
    <div id="saleDetailsDialog" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
              <i data-lucide="shopping-cart" class="w-6 h-6 text-blue-600"></i>
              SALE DETAILS - <span id="detailInvoice">INV-001</span>
            </h2>
            <p class="text-gray-500 italic font-normal">Complete transaction information</p>
          </div>
          <button id="closeSaleDetails" class="text-gray-500 hover:text-gray-800 transition">
            <i data-lucide="x" class="w-6 h-6"></i>
          </button>
        </div>

        <div class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="text-gray-600 font-medium">Customer</label><p id="detailCustomer" class="text-black">John Doe</p></div>
            <div><label class="text-gray-600 font-medium">Date</label><p id="detailDate" class="text-black">10/15/2025</p></div>
            <div><label class="text-gray-600 font-medium">Payment</label><p id="detailPayment" class="text-black capitalize">cash</p></div>
            <div><label class="text-gray-600 font-medium">Cashier</label><p class="text-black">Current User</p></div>
          </div>
          <div>
            <label class="text-gray-600 font-medium">Items Purchased</label>
            <div id="detailItems" class="mt-2 border border-gray-200 rounded-lg">
              <!-- Items will be populated dynamically -->
            </div>
          </div>
          <div class="border-t border-gray-200 pt-4 space-y-2">
            <div class="flex justify-between"><span class="text-gray-600">Subtotal:</span><span id="detailSubtotal" class="font-bold text-black">Ksh.185.97</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Tax:</span><span id="detailTax" class="font-bold text-black">Ksh.18.60</span></div>
            <div class="flex justify-between text-lg"><span class="font-bold text-black">Total:</span><span id="detailTotal" class="font-bold text-black">Ksh.204.57</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php include 'footer.php'; ?>

  <script>
document.addEventListener("DOMContentLoaded", () => {
  lucide.createIcons();

  const newSaleDialog = document.getElementById("newSaleDialog");
  const saleDetailsDialog = document.getElementById("saleDetailsDialog");
  const productList = document.getElementById("productList");
  const saleItems = document.getElementById("saleItems");
  const subtotalElement = document.getElementById("subtotal");
  const taxElement = document.getElementById("tax");
  const totalElement = document.getElementById("total");
  const saleItemsJson = document.getElementById("saleItemsJson");
  const newSaleForm = document.getElementById("newSaleForm");

  // Open New Sale
  const openNewSaleBtn = document.getElementById("openNewSale");
  if (openNewSaleBtn) {
    openNewSaleBtn.addEventListener("click", () => {
      if (newSaleDialog) {
        newSaleDialog.classList.remove("hidden");
        saleItems.innerHTML = '<p class="text-gray-400 italic text-center">No items added yet</p>';
        updateTotals();
      }
    });
  }

  document.getElementById("cancelNewSale").addEventListener("click", () => newSaleDialog.classList.add("hidden"));
  document.getElementById("closeNewSale").addEventListener("click", () => newSaleDialog.classList.add("hidden"));

  // Open Sale Details
  document.querySelectorAll(".openSaleDetails").forEach(button => {
    button.addEventListener("click", () => {
      const saleId = button.getAttribute("data-id");
      fetch(`sales.php?details=${saleId}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById("detailInvoice").textContent = data.invoice_number;
          document.getElementById("detailCustomer").textContent = data.customer_name;
          document.getElementById("detailDate").textContent = data.sale_date;
          document.getElementById("detailPayment").textContent = data.payment_method;
          document.getElementById("detailSubtotal").textContent = `Ksh.${data.subtotal.toFixed(2)}`;
          document.getElementById("detailTax").textContent = `Ksh.${data.tax.toFixed(2)}`;
          document.getElementById("detailTotal").textContent = `Ksh.${data.total_amount.toFixed(2)}`;

          const itemsDiv = document.getElementById("detailItems");
          itemsDiv.innerHTML = '';
          data.items.forEach(item => {
            const itemDiv = document.createElement("div");
            itemDiv.className = "flex justify-between p-3 border-b border-gray-200";
            itemDiv.innerHTML = `
              <div>
                <p class="font-medium text-black">${item.product_name}</p>
                <p class="text-sm text-gray-600 italic">${item.sku}</p>
              </div>
              <p class="text-black">${item.quantity} × Ksh.${item.price.toFixed(2)} = Ksh.${(item.quantity * item.price).toFixed(2)}</p>
            `;
            itemsDiv.appendChild(itemDiv);
          });
          saleDetailsDialog.classList.remove("hidden");
        })
        .catch(error => console.error("Error fetching sale details:", error));
    });
  });
  document.getElementById("closeSaleDetails")?.addEventListener("click", () => saleDetailsDialog.classList.add("hidden"));

  // Add product to Sale
  productList.addEventListener("click", (e) => {
    const productDiv = e.target.closest(".flex");
    if (!productDiv) return;

    const productName = productDiv.getAttribute("data-product");
    const sku = productDiv.getAttribute("data-sku");
    const price = parseFloat(productDiv.getAttribute("data-price"));

    // Clear placeholder only
    if (saleItems.querySelector("p.text-gray-400")) saleItems.innerHTML = "";

    // Create quantity input
    const quantityInput = document.createElement("input");
    quantityInput.type = "number";
    quantityInput.min = "1";
    quantityInput.value = "1";
    quantityInput.className = "w-16 border border-gray-300 rounded-md p-1 ml-2 focus:ring-2 focus:ring-blue-500";

    // Create item element
    const itemDiv = document.createElement("div");
    itemDiv.className = "flex justify-between items-center p-2 border-b border-gray-200";
    itemDiv.innerHTML = `
      <div>
        <p class="font-medium text-black">${productName}</p>
        <p class="text-sm text-gray-600 italic">${sku}</p>
      </div>
      <div class="flex items-center">
        <span class="font-bold text-black mr-2">Ksh.${price.toFixed(2)}</span>
        <button class="remove-item ml-2 text-red-500 hover:text-red-700">
          <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>
      </div>
    `;

    // Insert quantity input before remove button
    const actionsDiv = itemDiv.querySelector(".flex.items-center");
    actionsDiv.insertBefore(quantityInput, actionsDiv.querySelector(".remove-item"));

    // Add to list
    saleItems.appendChild(itemDiv);
    lucide.createIcons();

    // Update totals immediately
    updateTotals();

    // Quantity change event
    quantityInput.addEventListener("input", updateTotals);

    // Remove item event
    itemDiv.querySelector(".remove-item").addEventListener("click", () => {
      itemDiv.remove();
      if (saleItems.children.length === 0) {
        saleItems.innerHTML = '<p class="text-gray-400 italic text-center">No items added yet</p>';
      }
      updateTotals();
    });
  });

  // Calculate totals
  function updateTotals() {
    let subtotal = 0;
    const items = [];

    saleItems.querySelectorAll(".flex.justify-between").forEach(item => {
      const name = item.querySelector("p.font-medium")?.textContent.trim() || "";
      const sku = item.querySelector("p.text-sm")?.textContent.trim() || "";
      const price = parseFloat(item.querySelector("span.font-bold").textContent.replace('Ksh.', '').trim());
      const quantityInput = item.querySelector("input");
      const quantity = quantityInput ? parseInt(quantityInput.value) : 1;

      if (!isNaN(price) && !isNaN(quantity)) {
        subtotal += price * quantity;
        items.push({ name, sku, price, quantity });
      }
    });

    const tax = subtotal * 0.1;
    const total = subtotal + tax;

    subtotalElement.textContent = `Ksh.${subtotal.toFixed(2)}`;
    taxElement.textContent = `Ksh.${tax.toFixed(2)}`;
    totalElement.textContent = `Ksh.${total.toFixed(2)}`;
    saleItemsJson.value = JSON.stringify(items);
  }

  // Handle details view via URL param
  if (window.location.search.includes("details")) {
    const saleId = new URLSearchParams(window.location.search).get("details");
    document.querySelector(`.openSaleDetails[data-id='${saleId}']`)?.click();
  }
});
</script>

</body>
</html>
<?php ob_end_flush(); // Flush the output buffer ?>