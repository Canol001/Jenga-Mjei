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

// Handle new user submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $userId = trim($_POST['userId'] ?? '');
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $status = trim($_POST['status']);
    $role = trim($_POST['role']);
    $permissions = [
        'inventory' => isset($_POST['inventory']),
        'suppliers' => isset($_POST['suppliers']),
        'sales' => isset($_POST['sales']),
        'reports' => isset($_POST['reports']),
        'customers' => isset($_POST['customers']),
        'users' => isset($_POST['users'])
    ];
    $permissionsJson = json_encode($permissions);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (user_id, name, email, role, status, permissions) VALUES (:user_id, :name, :email, :role, :status, :permissions)");
        $stmt->execute([
            ':user_id' => $userId,
            ':name' => $name,
            ':email' => $email,
            ':role' => $role,
            ':status' => $status,
            ':permissions' => $permissionsJson
        ]);
        $_SESSION['message'] = "User added successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding user: " . $e->getMessage();
    }
    header("Location: users.php");
    exit();
}

// Fetch users
$stmt = $pdo->query("SELECT * FROM users ORDER BY date_created DESC LIMIT 4"); // Limit to 4 for demo
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count users by role for overview cards
$roleCounts = [
    'admin' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND status = 'active'")->fetchColumn(),
    'manager' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'manager' AND status = 'active'")->fetchColumn(),
    'cashier' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'cashier' AND status = 'active'")->fetchColumn(),
    'viewer' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'viewer' AND status = 'active'")->fetchColumn()
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    /* Checkbox styling to mimic shadcn/ui Checkbox */
    input[type="checkbox"] {
      appearance: none;
      width: 1rem;
      height: 1rem;
      border: 1px solid #d1d5db;
      border-radius: 0.25rem;
      cursor: pointer;
    }
    input[type="checkbox"]:checked {
      background-color: #2563eb;
      border-color: #2563eb;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12.207 5.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3E%3C/svg%3E");
    }
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
        <h1 class="text-3xl sm:text-4xl font-bold text-black mb-4">USER MANAGEMENT</h1>
        <p class="text-base sm:text-lg text-gray-600 italic font-normal">
          Manage system users, roles, and permissions for your hardware shop
        </p>
      </div>
      <button id="openAddUser" class="bg-blue-600 text-white px-4 py-2 rounded-md flex items-center hover:bg-blue-700 transition-colors">
        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
        Add User
      </button>
    </div>

    <!-- Add User Modal -->
    <div id="add-user-dialog" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h2 class="text-2xl font-extrabold text-gray-800 border-b pb-2 mb-2 flex items-center gap-2">
              <i data-lucide="user-plus" class="w-6 h-6 text-blue-600"></i>
              ADD NEW SYSTEM USER
            </h2>
            <p class="text-sm text-gray-500 italic font-medium mb-8">Define user credentials, system roles, and granular permissions.</p>
          </div>
          <button id="cancelAddUser" class="text-gray-500 hover:text-gray-800 transition">
            <i data-lucide="x" class="w-6 h-6"></i>
          </button>
        </div>

        <form class="space-y-8" id="addUserForm" method="POST" action="users.php">
          <input type="hidden" name="add_user" value="1">
          <fieldset class="border-t border-gray-200 pt-6">
            <legend class="text-lg font-semibold text-gray-700 mb-4 px-2">Account Details</legend>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
              <div>
                <label for="userId" class="text-sm font-medium text-gray-700 block mb-1">User ID (Optional)</label>
                <input id="userId" name="userId" type="text" placeholder="e.g., USER001" class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition duration-150" />
              </div>
              <div>
                <label for="name" class="text-sm font-medium text-gray-700 block mb-1">Full Name</label>
                <input id="name" name="name" type="text" placeholder="e.g., Jane Doe" class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition duration-150" required />
              </div>
              <div>
                <label for="email" class="text-sm font-medium text-gray-700 block mb-1">Email Address</label>
                <input id="email" name="email" type="email" placeholder="jane.doe@company.com" class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition duration-150" required />
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
              <div>
                <label for="dateCreated" class="text-sm font-medium text-gray-700 block mb-1">Date Created</label>
                <input id="dateCreated" name="dateCreated" type="text" value="<?php echo date('m/d/Y h:i A T'); ?>" class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-100 cursor-not-allowed" readonly />
              </div>
              <div>
                <label for="lastLogin" class="text-sm font-medium text-gray-700 block mb-1">Last Login</label>
                <input id="lastLogin" name="lastLogin" type="text" value="Never" class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-100 cursor-not-allowed" readonly />
              </div>
              <div>
                <label for="status" class="text-sm font-medium text-gray-700 block mb-1">Status</label>
                <select id="status" name="status" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white appearance-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition duration-150">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="pending">Pending</option>
                </select>
              </div>
            </div>
          </fieldset>

          <fieldset class="border-t border-gray-200 pt-6">
            <legend class="text-lg font-semibold text-gray-700 mb-4 px-2">Access Control</legend>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
              <div>
                <label for="role" class="text-sm font-medium text-gray-700 block mb-2">User Role</label>
                <select id="role" name="role" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white appearance-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition duration-150">
                  <option value="admin">Administrator (Full Access)</option>
                  <option value="manager">Manager (Operations Oversight)</option>
                  <option value="cashier" selected>Cashier (Sales & Customers)</option>
                  <option value="viewer">Viewer (Read-Only Reports)</option>
                </select>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700 block mb-2">Granular Permissions</label>
                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                  <div class="flex items-center">
                    <input type="checkbox" id="inventory" name="inventory" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                    <label for="inventory" class="ml-2 text-sm text-gray-600">Inventory Management</label>
                  </div>
                  <div class="flex items-center">
                    <input type="checkbox" id="suppliers" name="suppliers" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                    <label for="suppliers" class="ml-2 text-sm text-gray-600">Supplier Management</label>
                  </div>
                  <div class="flex items-center">
                    <input type="checkbox" id="sales" name="sales" checked class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                    <label for="sales" class="ml-2 text-sm text-gray-600">Sales Management</label>
                  </div>
                  <div class="flex items-center">
                    <input type="checkbox" id="reports" name="reports" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                    <label for="reports" class="ml-2 text-sm text-gray-600">Reports & Analytics</label>
                  </div>
                  <div class="flex items-center">
                    <input type="checkbox" id="customers" name="customers" checked class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                    <label for="customers" class="ml-2 text-sm text-gray-600">Customer Management</label>
                  </div>
                  <div class="flex items-center">
                    <input type="checkbox" id="users" name="users" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                    <label for="users" class="ml-2 text-sm text-gray-600">User Management</label>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>

          <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
            <button type="button" id="cancelAddUser" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
              Cancel
            </button>
            <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
              Add User
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Role Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4 pb-3">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-600 uppercase">ADMIN</h3>
            <span class="badge bg-red-500 text-white"><?php echo $roleCounts['admin']; ?></span>
          </div>
        </div>
        <div class="p-4">
          <div class="space-y-2">
            <p class="text-xs text-gray-400 italic font-normal"><?php echo $roleCounts['admin'] ? "$roleCounts[admin] active user(s)" : "0 active users"; ?></p>
            <p class="text-xs text-gray-400 italic font-normal">6 permissions</p>
          </div>
        </div>
      </div>
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4 pb-3">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-600 uppercase">MANAGER</h3>
            <span class="badge bg-blue-500 text-white"><?php echo $roleCounts['manager']; ?></span>
          </div>
        </div>
        <div class="p-4">
          <div class="space-y-2">
            <p class="text-xs text-gray-400 italic font-normal"><?php echo $roleCounts['manager'] ? "$roleCounts[manager] active user(s)" : "0 active users"; ?></p>
            <p class="text-xs text-gray-400 italic font-normal">5 permissions</p>
          </div>
        </div>
      </div>
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4 pb-3">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-600 uppercase">CASHIER</h3>
            <span class="badge bg-green-500 text-white"><?php echo $roleCounts['cashier']; ?></span>
          </div>
        </div>
        <div class="p-4">
          <div class="space-y-2">
            <p class="text-xs text-gray-400 italic font-normal"><?php echo $roleCounts['cashier'] ? "$roleCounts[cashier] active user(s)" : "0 active users"; ?></p>
            <p class="text-xs text-gray-400 italic font-normal">2 permissions</p>
          </div>
        </div>
      </div>
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4 pb-3">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-600 uppercase">VIEWER</h3>
            <span class="badge bg-gray-500 text-white"><?php echo $roleCounts['viewer']; ?></span>
          </div>
        </div>
        <div class="p-4">
          <div class="space-y-2">
            <p class="text-xs text-gray-400 italic font-normal"><?php echo $roleCounts['viewer'] ? "$roleCounts[viewer] active user(s)" : "0 active users"; ?></p>
            <p class="text-xs text-gray-400 italic font-normal">1 permissions</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white border border-gray-200 rounded-lg mb-6 shadow-sm">
      <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="lg:col-span-2">
            <label for="search" class="text-gray-600 font-normal">Search Users</label>
            <div class="relative mt-1">
              <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
              <input id="search" placeholder="Search by name or email..." class="w-full pl-10 border border-gray-300 rounded-md p-2" />
            </div>
          </div>
          <div>
            <label class="text-gray-600 font-normal">Role Filter</label>
            <select class="mt-1 w-full border border-gray-300 rounded-md p-2">
              <option value="all">All Roles</option>
              <option value="admin">Administrator</option>
              <option value="manager">Manager</option>
              <option value="cashier">Cashier</option>
              <option value="viewer">Viewer</option>
            </select>
          </div>
          <div>
            <label class="text-gray-600 font-normal">Status Filter</label>
            <select class="mt-1 w-full border border-gray-300 rounded-md p-2">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="pending">Pending</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="p-4">
        <h3 class="text-xl font-bold text-black flex items-center">
          <i data-lucide="users" class="w-5 h-5 mr-2"></i>
          SYSTEM USERS (<?php echo count($users); ?>)
        </h3>
        <p class="text-gray-600 italic font-normal">Manage user accounts, roles, and system permissions</p>
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
  <!-- Desktop View -->
  <table class="w-full hidden md:table">
    <thead>
      <tr>
        <th class="text-left font-bold text-gray-600 py-2">User</th>
        <th class="text-left font-bold text-gray-600 py-2">Role</th>
        <th class="text-left font-bold text-gray-600 py-2">Permissions</th>
        <th class="text-left font-bold text-gray-600 py-2">Last Login</th>
        <th class="text-left font-bold text-gray-600 py-2">Status</th>
        <th class="text-left font-bold text-gray-600 py-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user) {
          $permissions = json_decode($user['permissions'], true);
          $activePermissions = array_sum(array_map('intval', $permissions));
          $totalPermissions = 6;
      ?>
        <tr class="hover:bg-gray-50">
          <td class="py-2">
            <div>
              <p class="font-medium text-black"><?php echo htmlspecialchars($user['name']); ?></p>
              <div class="flex items-center space-x-2">
                <i data-lucide="mail" class="w-3 h-3 text-gray-400"></i>
                <span class="text-sm text-gray-600 italic"><?php echo htmlspecialchars($user['email']); ?></span>
              </div>
            </div>
          </td>
          <td class="py-2">
            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'red' : ($user['role'] === 'manager' ? 'blue' : ($user['role'] === 'cashier' ? 'green' : 'gray')); ?>-500 text-white">
              <?php echo strtoupper($user['role']); ?>
            </span>
          </td>
          <td class="py-2">
            <div class="flex items-center space-x-2">
              <i data-lucide="shield" class="w-4 h-4 text-gray-400"></i>
              <span class="text-gray-600"><?php echo $activePermissions; ?> of <?php echo $totalPermissions; ?></span>
            </div>
          </td>
          <td class="py-2">
            <div class="flex items-center space-x-2">
              <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
              <span class="text-gray-600"><?php echo $user['last_login'] ? date('m/d/Y', strtotime($user['last_login'])) : 'Never'; ?></span>
            </div>
          </td>
          <td class="py-2">
            <span class="badge bg-<?php echo $user['status'] === 'active' ? 'blue' : ($user['status'] === 'inactive' ? 'red' : 'yellow'); ?>-600 text-white">
              <?php echo strtoupper($user['status']); ?>
            </span>
          </td>
          <td class="py-2">
            <div class="flex space-x-2">
              <button class="toggleStatusBtn border border-gray-200 text-<?php echo $user['status'] === 'active' ? 'orange' : 'green'; ?>-600 px-2 py-1 rounded hover:bg-gray-100 transition-colors" data-email="<?php echo htmlspecialchars($user['email']); ?>" data-status="<?php echo $user['status']; ?>">
                <i data-lucide="user-<?php echo $user['status'] === 'active' ? 'x' : 'check'; ?>" class="w-4 h-4"></i>
              </button>
              <button class="editBtn border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors" data-email="<?php echo htmlspecialchars($user['email']); ?>">
                <i data-lucide="edit" class="w-4 h-4"></i>
              </button>
              <button class="deleteBtn border border-gray-200 text-red-600 px-2 py-1 rounded hover:bg-red-50 transition-colors" data-email="<?php echo htmlspecialchars($user['email']); ?>">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
              </button>
            </div>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <!-- Mobile View -->
  <div class="md:hidden space-y-4">
    <?php foreach ($users as $user) {
        $permissions = json_decode($user['permissions'], true);
        $activePermissions = array_sum(array_map('intval', $permissions));
        $totalPermissions = 6;
    ?>
      <div class="border rounded-lg p-4 shadow-sm bg-white">
        <div class="flex justify-between items-center mb-2">
          <h3 class="font-bold text-black"><?php echo htmlspecialchars($user['name']); ?></h3>
          <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'red' : ($user['role'] === 'manager' ? 'blue' : ($user['role'] === 'cashier' ? 'green' : 'gray')); ?>-500 text-white">
            <?php echo strtoupper($user['role']); ?>
          </span>
        </div>
        <p class="text-gray-600 text-sm"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p class="text-gray-600 text-sm"><strong>Permissions:</strong> <?php echo $activePermissions; ?> / <?php echo $totalPermissions; ?></p>
        <p class="text-gray-600 text-sm"><strong>Last Login:</strong> <?php echo $user['last_login'] ? date('m/d/Y', strtotime($user['last_login'])) : 'Never'; ?></p>
        <p class="mt-2">
          <span class="badge bg-<?php echo $user['status'] === 'active' ? 'blue' : ($user['status'] === 'inactive' ? 'red' : 'yellow'); ?>-600 text-white">
            <?php echo strtoupper($user['status']); ?>
          </span>
        </p>
        <div class="mt-3 flex flex-wrap gap-2">
          <button class="toggleStatusBtn border border-gray-200 text-<?php echo $user['status'] === 'active' ? 'orange' : 'green'; ?>-600 px-2 py-1 rounded hover:bg-gray-100 transition-colors flex items-center gap-1 w-full sm:w-auto" data-email="<?php echo htmlspecialchars($user['email']); ?>" data-status="<?php echo $user['status']; ?>">
            <i data-lucide="user-<?php echo $user['status'] === 'active' ? 'x' : 'check'; ?>" class="w-4 h-4"></i>
            Toggle
          </button>
          <button class="editBtn border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors flex items-center gap-1 w-full sm:w-auto" data-email="<?php echo htmlspecialchars($user['email']); ?>">
            <i data-lucide="edit" class="w-4 h-4"></i> Edit
          </button>
          <button class="deleteBtn border border-gray-200 text-red-600 px-2 py-1 rounded hover:bg-red-50 transition-colors flex items-center gap-1 w-full sm:w-auto" data-email="<?php echo htmlspecialchars($user['email']); ?>">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Delete
          </button>
        </div>
      </div>
    <?php } ?>
  </div>
</div>

    </div>

    <!-- Permissions Reference Table -->
    <div class="bg-white border border-gray-200 rounded-lg mt-8 shadow-sm">
      <div class="p-4">
        <h3 class="text-xl font-bold text-black flex items-center">
          <i data-lucide="settings" class="w-5 h-5 mr-2"></i>
          ROLE PERMISSIONS REFERENCE
        </h3>
        <p class="text-gray-600 italic font-normal">Default permissions for each user role</p>
      </div>
      <div class="p-4 overflow-x-auto">
  <!-- Desktop View -->
  <table class="w-full hidden md:table">
    <thead>
      <tr>
        <th class="text-left font-bold text-gray-600 py-2">Role</th>
        <th class="text-left font-bold text-gray-600 py-2">Inventory</th>
        <th class="text-left font-bold text-gray-600 py-2">Sales</th>
        <th class="text-left font-bold text-gray-600 py-2">Customers</th>
        <th class="text-left font-bold text-gray-600 py-2">Suppliers</th>
        <th class="text-left font-bold text-gray-600 py-2">Reports</th>
        <th class="text-left font-bold text-gray-600 py-2">Users</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="py-2"><span class="badge bg-red-500 text-white">ADMIN</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
      </tr>
      <tr>
        <td class="py-2"><span class="badge bg-blue-500 text-white">MANAGER</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-gray-200 text-gray-800">No</span></td>
      </tr>
      <tr>
        <td class="py-2"><span class="badge bg-green-500 text-white">CASHIER</span></td>
        <td class="py-2"><span class="badge bg-gray-200 text-gray-800">No</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-gray-200 text-gray-800">No</span></td>
        <td class="py-2"><span class="badge bg-gray-200 text-gray-800">No</span></td>
        <td class="py-2"><span class="badge bg-gray-200 text-gray-800">No</span></td>
      </tr>
      <tr>
        <td class="py-2"><span class="badge bg-gray-500 text-white">VIEWER</span></td>
        <td class="py-2"><span class="badge bg-gray-200 text-gray-800">No</span></td>
        <td class="py-2"><span class="badge bg-gray-200 text-gray-800">No</span></td>
        <td class="py-2"><span class="badge bg-gray-200 text-gray-800">No</span></td>
        <td class="py-2"><span class="badge bg-gray-200 text-gray-800">No</span></td>
        <td class="py-2"><span class="badge bg-blue-600 text-white">Yes</span></td>
        <td class="py-2"><span class="badge bg-gray-200 text-gray-800">No</span></td>
      </tr>
    </tbody>
  </table>

  <!-- Mobile View -->
  <div class="md:hidden space-y-4">
    <!-- ADMIN -->
    <div class="border rounded-lg p-4 shadow-sm bg-white">
      <h3 class="font-bold text-black mb-2"><span class="badge bg-red-500 text-white">ADMIN</span></h3>
      <ul class="text-sm text-gray-700 space-y-1">
        <li><strong>Inventory:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Sales:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Customers:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Suppliers:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Reports:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Users:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
      </ul>
    </div>

    <!-- MANAGER -->
    <div class="border rounded-lg p-4 shadow-sm bg-white">
      <h3 class="font-bold text-black mb-2"><span class="badge bg-blue-500 text-white">MANAGER</span></h3>
      <ul class="text-sm text-gray-700 space-y-1">
        <li><strong>Inventory:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Sales:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Customers:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Suppliers:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Reports:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Users:</strong> <span class="badge bg-gray-200 text-gray-800">No</span></li>
      </ul>
    </div>

    <!-- CASHIER -->
    <div class="border rounded-lg p-4 shadow-sm bg-white">
      <h3 class="font-bold text-black mb-2"><span class="badge bg-green-500 text-white">CASHIER</span></h3>
      <ul class="text-sm text-gray-700 space-y-1">
        <li><strong>Inventory:</strong> <span class="badge bg-gray-200 text-gray-800">No</span></li>
        <li><strong>Sales:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Customers:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Suppliers:</strong> <span class="badge bg-gray-200 text-gray-800">No</span></li>
        <li><strong>Reports:</strong> <span class="badge bg-gray-200 text-gray-800">No</span></li>
        <li><strong>Users:</strong> <span class="badge bg-gray-200 text-gray-800">No</span></li>
      </ul>
    </div>

    <!-- VIEWER -->
    <div class="border rounded-lg p-4 shadow-sm bg-white">
      <h3 class="font-bold text-black mb-2"><span class="badge bg-gray-500 text-white">VIEWER</span></h3>
      <ul class="text-sm text-gray-700 space-y-1">
        <li><strong>Inventory:</strong> <span class="badge bg-gray-200 text-gray-800">No</span></li>
        <li><strong>Sales:</strong> <span class="badge bg-gray-200 text-gray-800">No</span></li>
        <li><strong>Customers:</strong> <span class="badge bg-gray-200 text-gray-800">No</span></li>
        <li><strong>Suppliers:</strong> <span class="badge bg-gray-200 text-gray-800">No</span></li>
        <li><strong>Reports:</strong> <span class="badge bg-blue-600 text-white">Yes</span></li>
        <li><strong>Users:</strong> <span class="badge bg-gray-200 text-gray-800">No</span></li>
      </ul>
    </div>
  </div>
</div>

    </div>
  </div>

<?php include 'footer.php'; ?>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();

    const addUserDialog = document.getElementById('add-user-dialog');
    const openAddUser = document.getElementById('openAddUser');
    const cancelAddUser = document.querySelectorAll('#cancelAddUser');
    const toggleStatusBtns = document.querySelectorAll('.toggleStatusBtn');
    const editBtns = document.querySelectorAll('.editBtn');
    const deleteBtns = document.querySelectorAll('.deleteBtn');

    openAddUser.addEventListener('click', () => addUserDialog.classList.remove('hidden'));
    cancelAddUser.forEach(btn => btn.addEventListener('click', () => addUserDialog.classList.add('hidden')));

    toggleStatusBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const email = btn.getAttribute('data-email');
        const currentStatus = btn.getAttribute('data-status');
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        fetch(`users.php?toggle=${email}&status=${newStatus}`, { method: 'POST' })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert(`User status updated to ${newStatus}!`);
              location.reload();
            } else {
              alert('Error updating status: ' + data.error);
            }
          })
          .catch(error => console.error('Error toggling status:', error));
      });
    });

    editBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const email = btn.getAttribute('data-email');
        console.log('Edit clicked for:', email);
        // Implement edit functionality here
      });
    });

    deleteBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        if (confirm('Are you sure you want to delete this user?')) {
          const email = btn.getAttribute('data-email');
          fetch(`users.php?delete=${email}`, { method: 'POST' })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert('User deleted successfully!');
                location.reload();
              } else {
                alert('Error deleting user: ' + data.error);
              }
            })
            .catch(error => console.error('Error deleting user:', error));
        }
      });
    });
  </script>
</body>
</html>
<?php ob_end_flush(); // Flush the output buffer ?>

<?php
// Handle AJAX requests for toggle status and delete
if (isset($_GET['toggle']) || isset($_GET['delete'])) {
    header('Content-Type: application/json');

    try {
        if (isset($_GET['toggle'])) {
            $email = $_GET['toggle'];
            $newStatus = $_GET['status'];
            $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE email = :email");
            $stmt->execute([':status' => $newStatus, ':email' => $email]);
            echo json_encode(['success' => true]);
        } elseif (isset($_GET['delete'])) {
            $email = $_GET['delete'];
            $stmt = $pdo->prepare("DELETE FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            echo json_encode(['success' => true]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}
?>