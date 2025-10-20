<?php
@session_start();
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hardware Shop Management System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&family=Nunito:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Nunito', sans-serif; }
    h1, h2, h3, .font-heading { font-family: 'Poppins', sans-serif; }
  </style>
</head>

<body class="bg-gray-50 text-gray-800">
<?php include 'header.php'; ?>
  <!-- Hero Section -->
  <section class="max-w-[120rem] mx-auto grid lg:grid-cols-2 min-h-[80vh] items-center">


<div class="px-8 lg:px-16 py-16 lg:py-24">
  <h1 class="font-heading text-5xl lg:text-7xl text-gray-700 mb-8 leading-tight">
    HARDWARE SHOP<br>MANAGEMENT<br>SYSTEM
  </h1>

  <p class="text-lg text-gray-600 italic mb-12 max-w-md leading-relaxed">
    Streamline your hardware business operations with our comprehensive management solution. 
    Track inventory, manage sales, and grow your business efficiently.
  </p>

  <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">
      Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User'); ?> 👋
    </h2>
    <a href="homepage.php" class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-8 py-4 rounded-lg text-lg shadow">
      Go to Dashboard
    </a>
  <?php else: ?>
    <a href="login.php" class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-8 py-4 rounded-lg text-lg shadow">
      Get Started
    </a>
  <?php endif; ?>
</div>


    <!-- Right image -->
    <div class="relative bg-gray-100 flex items-center justify-center h-full min-h-[500px] lg:min-h-[80vh]">
      <img src="https://static.wixstatic.com/media/2a9c2e_8f2facaee16343c88639d01c9a9e1fb6~mv2.png?originWidth=384&originHeight=384"
           alt="Hardware tools" class="object-contain w-80 h-80 relative z-10">
      <div class="absolute inset-0 bg-gradient-to-t from-gray-200/10 to-transparent rounded-lg"></div>
    </div>
  </section>

  <!-- Features Section -->
<section class="max-w-[100rem] mx-auto px-8 py-24">
  <div class="text-center mb-16">
    <h2 class="font-heading text-4xl text-gray-700 mb-6">COMPLETE BUSINESS SOLUTION</h2>
    <p class="text-lg text-gray-600 italic max-w-2xl mx-auto">
      Everything you need to manage your hardware shop efficiently, from inventory tracking to customer relationships.
    </p>
  </div>

  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
    <!-- Inventory Management -->
    <div class="border rounded-xl p-8 text-center hover:shadow-lg transition">
      <div class="w-16 h-16 bg-gray-50 text-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
        <i data-lucide="package" class="w-8 h-8"></i>
      </div>
      <h3 class="font-heading text-xl mb-2 text-gray-700">Inventory Management</h3>
      <p class="italic text-gray-600 mb-6">Track stock levels, manage products, and receive low stock alerts.</p>
      <a href="<?= $isLoggedIn ? 'inventory.php' : 'login.php'; ?>" 
         class="block w-full border border-gray-700 text-gray-700 hover:bg-gray-700 hover:text-white py-2 rounded-lg transition">
        <?= $isLoggedIn ? 'Open Module' : 'Sign In to Access'; ?>
      </a>
    </div>

    <!-- Sales Management -->
    <div class="border rounded-xl p-8 text-center hover:shadow-lg transition">
      <div class="w-16 h-16 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
        <i data-lucide="shopping-cart" class="w-8 h-8"></i>
      </div>
      <h3 class="font-heading text-xl mb-2 text-green-700">Sales Management</h3>
      <p class="italic text-gray-600 mb-6">Create invoices, process payments, and track sales performance.</p>
      <a href="<?= $isLoggedIn ? 'sales.php' : 'login.php'; ?>" 
         class="block w-full border border-green-700 text-green-700 hover:bg-green-700 hover:text-white py-2 rounded-lg transition">
        <?= $isLoggedIn ? 'Open Module' : 'Sign In to Access'; ?>
      </a>
    </div>

    <!-- Customer Management -->
    <div class="border rounded-xl p-8 text-center hover:shadow-lg transition">
      <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
        <i data-lucide="users" class="w-8 h-8"></i>
      </div>
      <h3 class="font-heading text-xl mb-2 text-purple-700">Customer Management</h3>
      <p class="italic text-gray-600 mb-6">Maintain customer database and purchase history.</p>
      <a href="<?= $isLoggedIn ? 'customers.php' : 'login.php'; ?>" 
         class="block w-full border border-purple-700 text-purple-700 hover:bg-purple-700 hover:text-white py-2 rounded-lg transition">
        <?= $isLoggedIn ? 'Open Module' : 'Sign In to Access'; ?>
      </a>
    </div>

    <!-- Supplier Management -->
    <div class="border rounded-xl p-8 text-center hover:shadow-lg transition">
      <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
        <i data-lucide="truck" class="w-8 h-8"></i>
      </div>
      <h3 class="font-heading text-xl mb-2 text-orange-700">Supplier Management</h3>
      <p class="italic text-gray-600 mb-6">Manage suppliers, track orders and deliveries.</p>
      <a href="<?= $isLoggedIn ? 'suppliers.php' : 'login.php'; ?>" 
         class="block w-full border border-orange-700 text-orange-700 hover:bg-orange-700 hover:text-white py-2 rounded-lg transition">
        <?= $isLoggedIn ? 'Open Module' : 'Sign In to Access'; ?>
      </a>
    </div>

    <!-- Dashboard & Reports -->
    <div class="border rounded-xl p-8 text-center hover:shadow-lg transition">
      <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
        <i data-lucide="bar-chart-3" class="w-8 h-8"></i>
      </div>
      <h3 class="font-heading text-xl mb-2 text-indigo-700">Dashboard & Reports</h3>
      <p class="italic text-gray-600 mb-6">View analytics, charts, and business insights.</p>
      <a href="<?= $isLoggedIn ? 'reports.php' : 'login.php'; ?>" 
         class="block w-full border border-indigo-700 text-indigo-700 hover:bg-indigo-700 hover:text-white py-2 rounded-lg transition">
        <?= $isLoggedIn ? 'Open Module' : 'Sign In to Access'; ?>
      </a>
    </div>

    <!-- User Management -->
    <div class="border rounded-xl p-8 text-center hover:shadow-lg transition">
      <div class="w-16 h-16 bg-gray-50 text-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
        <i data-lucide="settings" class="w-8 h-8"></i>
      </div>
      <h3 class="font-heading text-xl mb-2 text-gray-700">User Management</h3>
      <p class="italic text-gray-600 mb-6">Manage user roles and system settings.</p>
      <a href="<?= $isLoggedIn ? 'users.php' : 'login.php'; ?>" 
         class="block w-full border border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white py-2 rounded-lg transition">
        <?= $isLoggedIn ? 'Open Module' : 'Sign In to Access'; ?>
      </a>
    </div>
  </div>
</section>

  <!-- Stats Section -->
  <section class="bg-gray-700 text-white py-16">
    <div class="max-w-[100rem] mx-auto px-8 grid md:grid-cols-4 gap-8 text-center">
      <div>
        <div class="font-heading text-4xl mb-2">500+</div>
        <div class="italic">Products Managed</div>
      </div>
      <div>
        <div class="font-heading text-4xl mb-2">1,200+</div>
        <div class="italic">Sales Processed</div>
      </div>
      <div>
        <div class="font-heading text-4xl mb-2">150+</div>
        <div class="italic">Active Customers</div>
      </div>
      <div>
        <div class="font-heading text-4xl mb-2">25+</div>
        <div class="italic">Trusted Suppliers</div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>

  <script>
    lucide.createIcons();
  </script>

</body>
</html>
