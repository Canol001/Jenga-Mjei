<?php
ob_start(); // Start output buffering to prevent header errors
@session_start();
@include 'config.php';

// Check if user is logged in
$loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jenga-Mjei Management</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">

  <!-- Header -->
  <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-[100rem] mx-auto px-2 sm:px-4 lg:px-8 py-2 sm:py-3">
      <div class="flex justify-between items-center">
        <!-- Logo -->
        <a href="homepage.php" class="flex items-center space-x-2 sm:space-x-3">
          <i data-lucide="package" class="w-6 sm:w-8 h-6 sm:h-8 text-gray-600"></i>
          <span class="text-lg sm:text-xl font-semibold text-gray-600">Jenga-Mjei</span>
        </a>

        <!-- Navigation (Shown only if logged in) -->
        <?php if ($loggedin): ?>
          <nav class="hidden md:flex space-x-2 sm:space-x-4">
            <a href="dashboard.php" class="flex items-center space-x-1 sm:space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-600">
              <i data-lucide="bar-chart-3" class="w-3 sm:w-4 h-3 sm:h-4"></i><span>Dashboard</span>
            </a>
            <a href="inventory.php" class="flex items-center space-x-1 sm:space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-600">
              <i data-lucide="package" class="w-3 sm:w-4 h-3 sm:h-4"></i><span>Inventory</span>
            </a>
            <a href="sales.php" class="flex items-center space-x-1 sm:space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-600">
              <i data-lucide="shopping-cart" class="w-3 sm:w-4 h-3 sm:h-4"></i><span>Sales</span>
            </a>
            <a href="customers.php" class="flex items-center space-x-1 sm:space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-600">
              <i data-lucide="users" class="w-3 sm:w-4 h-3 sm:h-4"></i><span>Customers</span>
            </a>
            <a href="suppliers.php" class="flex items-center space-x-1 sm:space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-600">
              <i data-lucide="truck" class="w-3 sm:w-4 h-3 sm:h-4"></i><span>Suppliers</span>
            </a>
            <a href="users.php" class="flex items-center space-x-1 sm:space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-600">
              <i data-lucide="settings" class="w-3 sm:w-4 h-3 sm:h-4"></i><span>Users</span>
            </a>
          </nav>

          <!-- Mobile Navigation (Shown only if logged in) -->
          <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-white">
            <div class="px-2 sm:px-4 pt-2 sm:pt-3 pb-2 sm:pb-4 space-y-1 sm:space-y-2">
              <a href="dashboard.php" class="flex items-center space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-sm sm:text-base text-gray-600 hover:bg-gray-100 hover:text-gray-600">
                <i data-lucide="bar-chart-3" class="w-4 sm:w-5 h-4 sm:h-5"></i><span>Dashboard</span>
              </a>
              <a href="inventory.php" class="flex items-center space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-sm sm:text-base text-gray-600 hover:bg-gray-100 hover:text-gray-600">
                <i data-lucide="package" class="w-4 sm:w-5 h-4 sm:h-5"></i><span>Inventory</span>
              </a>
              <a href="sales.php" class="flex items-center space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-sm sm:text-base text-gray-600 hover:bg-gray-100 hover:text-gray-600">
                <i data-lucide="shopping-cart" class="w-4 sm:w-5 h-4 sm:h-5"></i><span>Sales</span>
              </a>
              <a href="customers.php" class="flex items-center space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-sm sm:text-base text-gray-600 hover:bg-gray-100 hover:text-gray-600">
                <i data-lucide="users" class="w-4 sm:w-5 h-4 sm:h-5"></i><span>Customers</span>
              </a>
              <a href="suppliers.php" class="flex items-center space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-sm sm:text-base text-gray-600 hover:bg-gray-100 hover:text-gray-600">
                <i data-lucide="truck" class="w-4 sm:w-5 h-4 sm:h-5"></i><span>Suppliers</span>
              </a>
              <a href="users.php" class="flex items-center space-x-2 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-sm sm:text-base text-gray-600 hover:bg-gray-100 hover:text-gray-600">
                <i data-lucide="settings" class="w-4 sm:w-5 h-4 sm:h-5"></i><span>Users</span>
              </a>
            </div>
          </div>

          <!-- User Menu (Shown only if logged in) -->
          <div class="flex items-center space-x-2 sm:space-x-4">
            <a href="#" class="flex items-center space-x-1 sm:space-x-2 text-gray-600 hover:text-gray-600">
              <i data-lucide="user" class="w-3 sm:w-4 h-3 sm:h-4"></i>
              <span class="hidden sm:block italic text-xs sm:text-sm">Admin</span>
            </a>
            <a href="logout.php" class="border border-gray-300 px-2 sm:px-3 py-1 sm:py-1.5 rounded-md text-xs sm:text-sm hover:bg-gray-100">Sign Out</a>
            <button id="mobile-menu-toggle" class="md:hidden p-1 sm:p-2 rounded-md text-gray-600 hover:text-gray-600 hover:bg-gray-100">
              <i data-lucide="menu" class="w-5 sm:w-6 h-5 sm:h-6"></i>
            </button>
          </div>
        <?php else: ?>
          <!-- Login Link (Shown only if not logged in) -->
          <div class="flex items-center space-x-2 sm:space-x-4">
            <a href="login.php" class="border border-gray-300 px-2 sm:px-3 py-1 sm:py-1.5 rounded-md text-xs sm:text-sm hover:bg-gray-100">Login</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header>

<!-- Main Content (Placeholder) -->
<main class="flex-1">
  <div id="main-content" class="max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">
    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
      <button id="close-content" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 transition-colors p-1 rounded-full hover:bg-gray-100">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>

      <h2 class="text-2xl font-bold text-gray-700">Welcome to Jenga-Mjei Management</h2>
      <p class="mt-2 text-gray-600">Please log in to access the dashboard and manage your hardware shop.</p>

      <div id="footer-content" class="max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-sm text-gray-600 relative">
        <p>&copy; <?php echo date('Y'); ?> Jenga-Mjei. All rights reserved.</p>
      </div>
    <?php endif; ?>
  </div>
</main>




<!-- Script -->
<script>
  lucide.createIcons();

  // Close main content
  const closeContentBtn = document.getElementById('close-content');
  const mainContent = document.getElementById('main-content');
  if (closeContentBtn && mainContent) {
    closeContentBtn.addEventListener('click', () => {
      mainContent.style.display = 'none';
    });
  }

</script>

  <!-- Script -->
  <script>
    lucide.createIcons();

    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');
    if (toggle && menu) {
      toggle.addEventListener('click', () => {
        menu.classList.toggle('hidden');
        toggle.innerHTML = menu.classList.contains('hidden')
          ? '<i data-lucide="menu" class="w-6 h-6"></i>'
          : '<i data-lucide="x" class="w-6 h-6"></i>';
        lucide.createIcons();
      });
    }

    // Sign Out functionality
    document.querySelector('button:contains("Sign Out")').addEventListener('click', () => {
      if (confirm('Are you sure you want to sign out?')) {
        fetch('logout.php', { method: 'POST' })
          .then(() => window.location.href = 'homepage.php')
          .catch(error => console.error('Error signing out:', error));
      }
    });

    // Custom :contains() polyfill for older browsers
    if (!Element.prototype.matches(':contains')) {
      Element.prototype.matches = Element.prototype.matchesSelector || 
                                Element.prototype.mozMatchesSelector ||
                                Element.prototype.msMatchesSelector ||
                                Element.prototype.oMatchesSelector ||
                                Element.prototype.webkitMatchesSelector;

      Element.prototype.matches(':contains', function(text) {
        return this.textContent.includes(text);
      });
    }
  </script>
</body>
</html>
<?php ob_end_flush(); // Flush the output buffer ?>