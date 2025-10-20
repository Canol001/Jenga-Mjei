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

// Handle new supplier submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_supplier'])) {
    $name = trim($_POST['name']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $paymentTerms = trim($_POST['payment_terms']);
    $deliveryLeadTime = intval($_POST['delivery_lead_time']);
    $website = trim($_POST['website'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $supplierId = 'SUP-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT); // Auto-generate supplier ID

    try {
        $stmt = $pdo->prepare("INSERT INTO suppliers (supplier_id, name, contact, email, address, payment_terms, delivery_lead_time, website, notes, status) VALUES (:supplier_id, :name, :contact, :email, :address, :payment_terms, :delivery_lead_time, :website, :notes, :status)");
        $stmt->execute([
            ':supplier_id' => $supplierId,
            ':name' => $name,
            ':contact' => $contact,
            ':email' => $email,
            ':address' => $address,
            ':payment_terms' => $paymentTerms,
            ':delivery_lead_time' => $deliveryLeadTime,
            ':website' => $website,
            ':notes' => $notes,
            ':status' => 'active'
        ]);
        $_SESSION['message'] = "Supplier added successfully! ID: $supplierId";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding supplier: " . $e->getMessage();
    }
    header("Location: suppliers.php");
    exit();
}

// Fetch suppliers
$stmt = $pdo->query("SELECT * FROM suppliers ORDER BY created_at DESC LIMIT 3"); // Limit to 3 for demo
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Supplier Management</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons CDN -->
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
        <h1 class="text-3xl sm:text-4xl font-bold text-black mb-4">SUPPLIER MANAGEMENT</h1>
        <p class="text-base sm:text-lg text-gray-600 italic font-normal">
          Manage suppliers, track deliveries, and monitor order performance
        </p>
      </div>
      <button id="openSupplierDialog" class="bg-blue-600 text-white px-4 py-2 rounded-md flex items-center hover:bg-blue-700 transition-colors">
        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
        Add Supplier
      </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white border border-gray-200 rounded-lg mb-6 shadow-sm">
      <div class="p-6">
        <div class="relative">
          <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
          <input
            placeholder="Search suppliers by name or contact..."
            class="w-full pl-10 border border-gray-300 rounded-md p-2"
          />
        </div>
      </div>
    </div>

    <!-- Supplier Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="p-4">
        <h3 class="text-xl font-bold text-black flex items-center">
          <i data-lucide="truck" class="w-5 h-5 mr-2"></i>
          SUPPLIERS LIST (<?php echo count($suppliers); ?>)
        </h3>
        <p class="text-gray-600 italic font-normal">View and manage supplier information</p>
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
      </div>
      <div class="p-4 overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr>
              <th class="text-left font-bold text-gray-600 py-2">Supplier ID</th>
              <th class="text-left font-bold text-gray-600 py-2">Name</th>
              <th class="text-left font-bold text-gray-600 py-2">Contact</th>
              <th class="text-left font-bold text-gray-600 py-2">Email</th>
              <th class="text-left font-bold text-gray-600 py-2">Total Orders</th>
              <th class="text-left font-bold text-gray-600 py-2">Status</th>
              <th class="text-left font-bold text-gray-600 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($suppliers as $supplier) { ?>
              <tr class="hover:bg-gray-50">
                <td class="py-2 text-black font-medium" data-label="Supplier ID"><?php echo htmlspecialchars($supplier['supplier_id']); ?></td>
                <td class="py-2 text-gray-600" data-label="Name"><?php echo htmlspecialchars($supplier['name']); ?></td>
                <td class="py-2 text-gray-600" data-label="Contact"><?php echo htmlspecialchars($supplier['contact']); ?></td>
                <td class="py-2 text-gray-600" data-label="Email"><?php echo htmlspecialchars($supplier['email']); ?></td>
                <td class="py-2 font-bold text-black" data-label="Total Orders">0</td> <!-- Placeholder, requires order tracking -->
                <td class="py-2" data-label="Status">
                  <span class="badge <?php echo $supplier['status'] === 'active' ? 'bg-green-600' : 'bg-gray-200'; ?> text-<?php echo $supplier['status'] === 'active' ? 'white' : 'gray-800'; ?>">
                    <?php echo strtoupper($supplier['status']); ?>
                  </span>
                </td>
                <td class="py-2" data-label="Actions">
                  <button class="viewSupplierBtn border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors" data-supplier-id="<?php echo htmlspecialchars($supplier['supplier_id']); ?>">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                  </button>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add Supplier Modal -->
  <div id="supplierDialog" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl p-6">
      <div class="flex justify-between items-start mb-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i data-lucide="truck" class="w-6 h-6 text-blue-600"></i>
            ADD NEW SUPPLIER
          </h2>
          <p class="text-gray-500 italic font-normal">Register a new supplier</p>
        </div>
        <button id="closeSupplierDialog" class="text-gray-500 hover:text-gray-800 transition">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
      </div>

      <form class="space-y-4" method="POST" action="suppliers.php">
        <input type="hidden" name="add_supplier" value="1">
        <div>
          <label class="text-gray-600 font-normal">Supplier Name</label>
          <input type="text" name="name" placeholder="Enter supplier name" class="mt-1 w-full border border-gray-300 rounded-md p-2" required />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-gray-600 font-normal">Contact</label>
            <input type="text" name="contact" placeholder="+254 7XX XXX XXX" class="mt-1 w-full border border-gray-300 rounded-md p-2" required />
          </div>
          <div>
            <label class="text-gray-600 font-normal">Email</label>
            <input type="email" name="email" placeholder="supplier@example.com" class="mt-1 w-full border border-gray-300 rounded-md p-2" required />
          </div>
        </div>
        <div>
          <label class="text-gray-600 font-normal">Address</label>
          <input type="text" name="address" placeholder="Enter supplier address" class="mt-1 w-full border border-gray-300 rounded-md p-2" required />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-gray-600 font-normal">Payment Terms</label>
            <select name="payment_terms" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
              <option value="">Select payment terms</option>
              <option value="net-30">Net 30 Days</option>
              <option value="net-60">Net 60 Days</option>
              <option value="cash-on-delivery">Cash on Delivery</option>
            </select>
          </div>
          <div>
            <label class="text-gray-600 font-normal">Delivery Lead Time</label>
            <input type="number" name="delivery_lead_time" placeholder="Days" min="1" class="mt-1 w-full border border-gray-300 rounded-md p-2" required />
          </div>
        </div>
        <div>
          <label class="text-gray-600 font-normal">Website (Optional)</label>
          <input type="url" name="website" placeholder="https://example.com" class="mt-1 w-full border border-gray-300 rounded-md p-2" />
        </div>
        <div>
          <label class="text-gray-600 font-normal">Notes (Optional)</label>
          <textarea name="notes" placeholder="Additional notes..." class="mt-1 w-full border border-gray-300 rounded-md p-2" rows="3"></textarea>
        </div>
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button id="closeSupplierDialog" type="button" class="border border-gray-200 text-gray-900 px-4 py-2 rounded-md hover:bg-gray-100 transition-colors">
            Cancel
          </button>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center gap-2 transition-colors">
            <i data-lucide="save" class="w-5 h-5"></i>
            Save Supplier
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Supplier Details Modal -->
  <div id="supplierDetailsDialog" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl p-6">
      <div class="flex justify-between items-start mb-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i data-lucide="truck" class="w-6 h-6 text-blue-600"></i>
            SUPPLIER DETAILS — <span id="detailName" class="text-blue-700">Tech Distributors Ltd</span>
          </h2>
          <p class="text-gray-500 italic font-normal">Full supplier information and order history</p>
        </div>
        <button id="closeSupplierDetailsDialog" class="text-gray-500 hover:text-gray-800 transition">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-gray-50 rounded-xl p-4 mb-6" id="supplierInfo">
        <!-- Populated dynamically -->
      </div>

      <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
        <i data-lucide="list" class="w-5 h-5 text-blue-600"></i> Order History
      </h3>
      <div class="border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm" id="orderHistory">
          <thead class="bg-gray-100">
            <tr>
              <th class="text-left text-gray-600 font-semibold py-3 px-4">Order ID</th>
              <th class="text-left text-gray-600 font-semibold py-3 px-4">Date</th>
              <th class="text-left text-gray-600 font-semibold py-3 px-4">Total</th>
              <th class="text-left text-gray-600 font-semibold py-3 px-4">Status</th>
            </tr>
          </thead>
          <tbody>
            <!-- Populated dynamically -->
          </tbody>
        </table>
      </div>

      <div class="flex justify-end mt-6">
        <button id="closeSupplierButton" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium flex items-center gap-2 transition">
          <i data-lucide="x-circle" class="w-5 h-5"></i> Close
        </button>
      </div>
    </div>
  </div>

<?php include 'footer.php'; ?>

  <script>
    lucide.createIcons();

    const supplierDialog = document.getElementById("supplierDialog");
    const supplierDetailsDialog = document.getElementById("supplierDetailsDialog");
    const openSupplierDialog = document.getElementById("openSupplierDialog");
    const closeSupplierDialog = document.querySelectorAll("#closeSupplierDialog");
    const viewSupplierBtns = document.querySelectorAll(".viewSupplierBtn");
    const closeSupplierDetailsDialog = document.querySelectorAll("#closeSupplierDetailsDialog, #closeSupplierButton");

    openSupplierDialog.addEventListener("click", () => {
      supplierDialog.classList.remove("hidden");
    });

    closeSupplierDialog.forEach(button => {
      button.addEventListener("click", () => {
        supplierDialog.classList.add("hidden");
      });
    });

    viewSupplierBtns.forEach(btn => {
      btn.addEventListener("click", () => {
        const supplierId = btn.getAttribute("data-supplier-id");
        fetch(`suppliers.php?view=${supplierId}`)
          .then(response => response.json())
          .then(data => {
            document.getElementById('detailName').textContent = data.name;
            const infoDiv = document.getElementById('supplierInfo');
            infoDiv.innerHTML = `
              <div><label class="text-gray-500 text-sm">Supplier ID</label><p class="text-gray-900 font-medium">${data.supplier_id}</p></div>
              <div><label class="text-gray-500 text-sm">Contact</label><p class="text-gray-900 font-medium flex items-center gap-2"><i data-lucide="phone" class="w-4 h-4 text-gray-600"></i>${data.contact}</p></div>
              <div><label class="text-gray-500 text-sm">Email</label><p class="text-gray-900 font-medium flex items-center gap-2"><i data-lucide="mail" class="w-4 h-4 text-gray-600"></i>${data.email}</p></div>
              <div><label class="text-gray-500 text-sm">Status</label><span class="inline-flex items-center gap-1 ${data.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-800'} text-sm font-medium px-3 py-1 rounded-full"><i data-lucide="check-circle" class="w-4 h-4"></i> ${data.status}</span></div>
            `;
            const historyTable = document.getElementById('orderHistory').getElementsByTagName('tbody')[0];
            historyTable.innerHTML = ''; // Clear existing rows
            data.orders.forEach(order => {
              const row = document.createElement('tr');
              row.className = 'hover:bg-gray-50 transition';
              row.innerHTML = `
                <td class="py-3 px-4 text-gray-800 font-medium">${order.order_id}</td>
                <td class="py-3 px-4 text-gray-600">${order.order_date}</td>
                <td class="py-3 px-4 text-gray-900 font-semibold">$${order.total.toFixed(2)}</td>
                <td class="py-3 px-4">
                  <span class="inline-flex items-center gap-1 ${order.status === 'Delivered' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700'} px-3 py-1 rounded-full text-xs font-medium">
                    <i data-lucide="${order.status === 'Delivered' ? 'truck' : 'clock'}" class="w-3 h-3"></i> ${order.status}
                  </span>
                </td>
              `;
              historyTable.appendChild(row);
            });
            supplierDetailsDialog.classList.remove("hidden");
          })
          .catch(error => console.error('Error fetching supplier details:', error));
      });
    });

    closeSupplierDetailsDialog.forEach(button => {
      button.addEventListener("click", () => {
        supplierDetailsDialog.classList.add("hidden");
      });
    });

    // Handle view request
    if (window.location.search.includes('view')) {
      const supplierId = new URLSearchParams(window.location.search).get('view');
      document.querySelector(`.viewSupplierBtn[data-supplier-id="${supplierId}"]`).click();
    }
  </script>
</body>
</html>
<?php ob_end_flush(); // Flush the output buffer ?>

<?php
// Handle AJAX requests for view
if (isset($_GET['view'])) {
    header('Content-Type: application/json');

    try {
        $supplierId = $_GET['view'];
        $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE supplier_id = :supplier_id");
        $stmt->execute([':supplier_id' => $supplierId]);
        $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($supplier) {
            // Fetch order history (assuming an orders table with supplier_id reference)
            $orderStmt = $pdo->prepare("SELECT order_id, order_date, total_amount AS total, status FROM orders WHERE supplier_id = :supplier_id LIMIT 5");
            $orderStmt->execute([':supplier_id' => $supplierId]);
            $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'supplier' => $supplier, 'orders' => $orders]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Supplier not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}
?>