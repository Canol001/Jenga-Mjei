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

// Handle new customer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_customer'])) {
    //$customerId = trim($_POST['customerId'] ?? '');
    $customerId = 'CUST' . rand(10000, 99999);
    $name = trim($_POST['name']);
    $company = trim($_POST['company'] ?? '');
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address'] ?? '');
    $status = trim($_POST['status']);
    $loyaltyPoints = intval($_POST['loyaltyPoints'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    try {
        $stmt = $pdo->prepare("INSERT INTO customers (customer_id, name, company, email, phone, address, status, loyalty_points, notes) VALUES (:customer_id, :name, :company, :email, :phone, :address, :status, :loyalty_points, :notes)");
        $stmt->execute([
            ':customer_id' => $customerId,
            ':name' => $name,
            ':company' => $company,
            ':email' => $email,
            ':phone' => $phone,
            ':address' => $address,
            ':status' => $status,
            ':loyalty_points' => $loyaltyPoints,
            ':notes' => $notes
        ]);
        $_SESSION['message'] = "Customer added successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding customer: " . $e->getMessage();
    }
    header("Location: customers.php");
    exit();
}

// Fetch customers (for future dynamic table population)
$stmt = $pdo->query("SELECT * FROM customers LIMIT 3"); // Limit to 3 for demo
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Management</title>
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
        <h1 class="text-3xl sm:text-4xl font-bold text-black mb-4">CUSTOMER MANAGEMENT</h1>
        <p class="text-base sm:text-lg text-gray-600 italic font-normal">
          Manage customer database, track purchase history, and maintain relationships
        </p>
      </div>
      <button id="addCustomerBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md flex items-center hover:bg-blue-700 transition-colors">
        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
        Add Customer
      </button>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white border border-gray-200 rounded-lg mb-6 shadow-sm">
      <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="lg:col-span-2">
            <label for="search" class="text-gray-600 font-normal">Search Customers</label>
            <div class="relative mt-1">
              <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
              <input id="search" placeholder="Search by name, email, phone, or company..." class="w-full pl-10 border border-gray-300 rounded-md p-2" />
            </div>
          </div>
          <div>
            <label class="text-gray-600 font-normal">Status Filter</label>
            <select class="mt-1 w-full border border-gray-300 rounded-md p-2">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="vip">VIP</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="p-4">
        <h3 class="text-xl font-bold text-black flex items-center">
          <i data-lucide="users" class="w-5 h-5 mr-2"></i>
          CUSTOMERS (<?php echo count($customers); ?>)
        </h3>
        <p class="text-gray-600 italic font-normal">Manage customer database and track purchase history</p>
      </div>

      <div class="hidden p-4 md:block overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr>
              <th class="text-left font-bold text-gray-600 py-2">Customer</th>
              <th class="text-left font-bold text-gray-600 py-2">Contact</th>
              <th class="text-left font-bold text-gray-600 py-2">Company</th>
              <th class="text-left font-bold text-gray-600 py-2">Total Purchases</th>
              <th class="text-left font-bold text-gray-600 py-2">Last Purchase</th>
              <th class="text-left font-bold text-gray-600 py-2">Status</th>
              <th class="text-left font-bold text-gray-600 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($customers as $customer) { ?>
              <tr>
                <td class="py-2" data-label="Customer">
                  <div>
                    <p class="font-medium text-black"><?php echo htmlspecialchars($customer['name']); ?></p>
                    <p class="text-sm text-gray-600 italic">ID: <?php echo htmlspecialchars($customer['customer_id'] ?? 'N/A'); ?></p>
                  </div>
                </td>
                <td class="py-2" data-label="Contact">
                  <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                      <i data-lucide="mail" class="w-3 h-3 text-gray-400"></i>
                      <span class="text-sm text-gray-600"><?php echo htmlspecialchars($customer['email']); ?></span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <i data-lucide="phone" class="w-3 h-3 text-gray-400"></i>
                      <span class="text-sm text-gray-600"><?php echo htmlspecialchars($customer['phone']); ?></span>
                    </div>
                  </div>
                </td>
                <td class="py-2 flex items-center space-x-2" data-label="Company">
                  <i data-lucide="building" class="w-4 h-4 text-gray-400"></i>
                  <span class="text-gray-600"><?php echo htmlspecialchars($customer['company'] ?? 'N/A'); ?></span>
                </td>
                <td class="py-2" data-label="Total Purchases">
                  <p class="font-bold text-black">$0.00</p> <!-- Placeholder, requires join with sales -->
                  <p class="text-sm text-gray-600 italic">0 orders</p> <!-- Placeholder -->
                </td>
                <td class="py-2 flex items-center space-x-2" data-label="Last Purchase">
                  <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                  <span class="text-gray-600">N/A</span> <!-- Placeholder -->
                </td>
                <td class="py-2" data-label="Status">
                  <span class="badge bg-blue-600 text-white"><?php echo strtoupper(htmlspecialchars($customer['status'])); ?></span>
                </td>
                <td class="py-2" data-label="Actions">
                  <div class="flex space-x-2">
                    <button class="viewBtn border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors" data-email="<?php echo htmlspecialchars($customer['email']); ?>">
                      <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                    <button class="editBtn border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors" data-email="<?php echo htmlspecialchars($customer['email']); ?>">
                      <i data-lucide="edit" class="w-4 h-4"></i>
                    </button>
                    <button class="deleteBtn border border-gray-200 text-red-600 px-2 py-1 rounded hover:bg-red-50 transition-colors" data-email="<?php echo htmlspecialchars($customer['email']); ?>">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
      <?php foreach ($customers as $customer) { ?>
        <div class="border border-gray-200 rounded-lg p-3 shadow-sm">
          <div class="flex justify-between items-center mb-2">
            <h4 class="font-semibold text-black"><?php echo htmlspecialchars($customer['name']); ?></h4>
            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
              <?php echo strtoupper(htmlspecialchars($customer['status'])); ?>
            </span>
          </div>
          <p class="text-sm text-gray-700"><span class="font-medium">ID:</span> <?php echo htmlspecialchars($customer['customer_id'] ?? 'N/A'); ?></p>
          <p class="text-sm text-gray-700"><span class="font-medium">Email:</span> <?php echo htmlspecialchars($customer['email']); ?></p>
          <p class="text-sm text-gray-700"><span class="font-medium">Phone:</span> <?php echo htmlspecialchars($customer['phone']); ?></p>
          <p class="text-sm text-gray-700"><span class="font-medium">Company:</span> <?php echo htmlspecialchars($customer['company'] ?? 'N/A'); ?></p>
          <p class="text-sm text-gray-700"><span class="font-medium">Total Purchases:</span> $0.00 (0 orders)</p>
          <p class="text-sm text-gray-700"><span class="font-medium">Last Purchase:</span> N/A</p>

          <div class="mt-3 flex justify-end space-x-2">
            <button class="viewBtn border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors" data-email="<?php echo htmlspecialchars($customer['email']); ?>">
              <i data-lucide="eye" class="w-4 h-4"></i>
            </button>
            <button class="editBtn border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors" data-email="<?php echo htmlspecialchars($customer['email']); ?>">
              <i data-lucide="edit" class="w-4 h-4"></i>
            </button>
            <button class="deleteBtn border border-gray-200 text-red-600 px-2 py-1 rounded hover:bg-red-50 transition-colors" data-email="<?php echo htmlspecialchars($customer['email']); ?>">
              <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
          </div>
        </div>
      <?php } ?>
    </div>

    
    </div>
  </div>

  <!-- Add Customer Modal -->
  <div id="addCustomerDialog" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6">
      <div class="flex justify-between items-start mb-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i data-lucide="user-plus" class="w-6 h-6 text-blue-600"></i>
            ADD NEW CUSTOMER
          </h2>
          <p class="text-gray-500 italic font-normal">Enter customer details to add to database</p>
        </div>
        <button id="cancelAdd" class="text-gray-500 hover:text-gray-800 transition">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
      </div>

      <form class="space-y-6" id="addCustomerForm" method="POST" action="customers.php">
        <input type="hidden" name="add_customer" value="1">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="name" class="text-gray-600 font-medium">Full Name</label>
            <input id="name" name="name" type="text" placeholder="Enter full name" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
          </div>
          <div>
            <label for="company" class="text-gray-600 font-medium">Company</label>
            <input id="company" name="company" type="text" placeholder="Enter company name" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" />
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="email" class="text-gray-600 font-medium">Email Address</label>
            <input id="email" name="email" type="email" placeholder="Enter email address" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
          </div>
          <div>
            <label for="phone" class="text-gray-600 font-medium">Phone Number</label>
            <input id="phone" name="phone" type="text" placeholder="Enter phone number" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div>
            <label for="address" class="text-gray-600 font-medium">Address</label>
            <textarea id="address" name="address" placeholder="Enter address" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" rows="1"></textarea>
          </div>
          <div>
            <label for="status" class="text-gray-600 font-medium">Status</label>
            <select id="status" name="status" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="vip">VIP</option>
            </select>
          </div>

        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div>
            <label for="loyaltyPoints" class="text-gray-600 font-medium">Loyalty Points</label>
            <input id="loyaltyPoints" name="loyaltyPoints" type="number" value="0" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" readonly />
            </div>

           <div>
            <label for="dateAdded" class="text-gray-600 font-medium">Date Added</label>
            <input id="dateAdded" name="dateAdded" type="text" value="<?php echo date('m/d/Y h:i A T'); ?>" class="mt-1 w-full border border-gray-300 rounded-md p-2 bg-gray-100 cursor-not-allowed" readonly />
          </div>

        </div>

        <div>
          <label for="notes" class="text-gray-600 font-medium">Notes</label>
          <textarea id="notes" name="notes" placeholder="Additional notes..." class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" rows="2"></textarea>
        </div>
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button type="button" id="cancelAdd" class="border border-gray-200 text-gray-900 px-4 py-2 rounded-md hover:bg-gray-100 transition-colors">Cancel</button>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center gap-2 transition-colors">
            <i data-lucide="save" class="w-5 h-5"></i>
            Add Customer
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- View Customer Modal -->
  <div id="viewCustomerDialog" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl p-6">
      <div class="flex justify-between items-start mb-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i data-lucide="user" class="w-6 h-6 text-blue-600"></i>
            CUSTOMER DETAILS - <span id="viewName">John Doe</span>
          </h2>
          <p class="text-gray-500 italic font-normal">Complete customer information and purchase history</p>
        </div>
        <button id="closeView" class="text-gray-500 hover:text-gray-800 transition">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
      </div>

      <div class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
          <h3 class="text-lg font-bold text-black mb-2">CONTACT INFORMATION</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="viewContact">
            <!-- Populated dynamically -->
          </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
          <h3 class="text-lg font-bold text-black mb-2">PURCHASE HISTORY</h3>
          <table class="w-full text-sm" id="purchaseHistory">
            <thead>
              <tr class="border-b">
                <th class="py-2 text-gray-600 font-semibold">Invoice</th>
                <th class="py-2 text-gray-600 font-semibold">Date</th>
                <th class="py-2 text-gray-600 font-semibold">Total</th>
              </tr>
            </thead>
            <tbody>
              <!-- Populated dynamically -->
            </tbody>
          </table>
        </div>
        <div class="flex justify-end">
          <button id="closeView" class="border border-gray-200 text-gray-900 px-4 py-2 rounded-md hover:bg-gray-100 transition-colors">Close</button>
        </div>
      </div>
    </div>
  </div>

<?php include 'footer.php'; ?>

  <script>
    lucide.createIcons();

    const addDialog = document.getElementById('addCustomerDialog');
    const addBtn = document.getElementById('addCustomerBtn');
    const cancelAdd = document.querySelectorAll('#cancelAdd');

    addBtn.addEventListener('click', () => addDialog.classList.remove('hidden'));
    cancelAdd.forEach(btn => btn.addEventListener('click', () => addDialog.classList.add('hidden')));

    const viewDialog = document.getElementById('viewCustomerDialog');
    const viewBtns = document.querySelectorAll('.viewBtn');
    const closeView = document.querySelectorAll('#closeView');
    const editBtns = document.querySelectorAll('.editBtn');
    const deleteBtns = document.querySelectorAll('.deleteBtn');

    viewBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const email = btn.getAttribute('data-email');
        fetch(`customers.php?view=${email}`)
          .then(response => response.json())
          .then(data => {
            document.getElementById('viewName').textContent = data.name;
            const contactDiv = document.getElementById('viewContact');
            contactDiv.innerHTML = `
              <p><strong>Email:</strong> <span class="text-black">${data.email}</span></p>
              <p><strong>Phone:</strong> <span class="text-black">${data.phone}</span></p>
              <p><strong>Company:</strong> <span class="text-black">${data.company || 'N/A'}</span></p>
              <p><strong>Customer ID:</strong> <span class="text-black">${data.customer_id || 'N/A'}</span></p>
            `;
            const historyTable = document.getElementById('purchaseHistory').getElementsByTagName('tbody')[0];
            historyTable.innerHTML = ''; // Clear existing rows
            data.purchases.forEach(purchase => {
              const row = document.createElement('tr');
              row.className = 'border-b hover:bg-gray-50';
              row.innerHTML = `
                <td class="py-2 text-gray-800 font-medium">${purchase.invoice_number}</td>
                <td class="py-2 text-gray-600">${purchase.sale_date}</td>
                <td class="py-2 text-gray-900 font-semibold">$${purchase.total_amount.toFixed(2)}</td>
              `;
              historyTable.appendChild(row);
            });
            viewDialog.classList.remove('hidden');
          })
          .catch(error => console.error('Error fetching customer details:', error));
      });
    });

    closeView.forEach(btn => btn.addEventListener('click', () => viewDialog.classList.add('hidden')));

    editBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        console.log('Edit clicked for:', btn.getAttribute('data-email'));
        // Implement edit functionality here
      });
    });

    deleteBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        if (confirm('Are you sure you want to delete this customer?')) {
          const email = btn.getAttribute('data-email');
          fetch(`customers.php?delete=${email}`, { method: 'POST' })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert('Customer deleted successfully!');
                location.reload();
              } else {
                alert('Error deleting customer: ' + data.error);
              }
            })
            .catch(error => console.error('Error deleting customer:', error));
        }
      });
    });

    // Handle view request
    if (window.location.search.includes('view')) {
      const email = new URLSearchParams(window.location.search).get('view');
      document.querySelector(`.viewBtn[data-email="${email}"]`).click();
    }

    // Handle delete request
    if (window.location.search.includes('delete')) {
      const email = new URLSearchParams(window.location.search).get('delete');
      document.querySelector(`.deleteBtn[data-email="${email}"]`).click();
    }
  </script>
</body>
</html>
<?php ob_end_flush(); // Flush the output buffer ?>

<?php
// Handle AJAX requests for view and delete
if (isset($_GET['view']) || isset($_GET['delete'])) {
    header('Content-Type: application/json');

    try {
        if (isset($_GET['view'])) {
            $email = $_GET['view'];
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                // Fetch purchase history (assuming a sales table with customer reference)
                $purchaseStmt = $pdo->prepare("SELECT invoice_number, sale_date, total_amount FROM sales WHERE customer_name = :name LIMIT 5");
                $purchaseStmt->execute([':name' => $customer['name']]);
                $purchases = $purchaseStmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode(['success' => true, 'customer' => $customer, 'purchases' => $purchases]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Customer not found']);
            }
        } elseif (isset($_GET['delete'])) {
            $email = $_GET['delete'];
            $stmt = $pdo->prepare("DELETE FROM customers WHERE email = :email");
            $stmt->execute([':email' => $email]);
            echo json_encode(['success' => true]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}
?>