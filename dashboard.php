<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page
    header("Location: homepage.php"); 
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hardware Shop Dashboard</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    /* Custom styles for charts */
    .bar-chart {
      display: flex;
      align-items: flex-end;
      height: 300px;
      gap: 10px;
      padding: 20px;
      background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg"><line x1="0" y1="0" x2="100%" y2="0" stroke="#D1CDC7" stroke-dasharray="3,3"/><line x1="0" y1="50" x2="100%" y2="50" stroke="#D1CDC7" stroke-dasharray="3,3"/><line x1="0" y1="100" x2="100%" y2="100" stroke="#D1CDC7" stroke-dasharray="3,3"/><line x1="0" y1="150" x2="100%" y2="150" stroke="#D1CDC7" stroke-dasharray="3,3"/><line x1="0" y1="200" x2="100%" y2="200" stroke="#D1CDC7" stroke-dasharray="3,3"/><line x1="0" y1="250" x2="100%" y2="250" stroke="#D1CDC7" stroke-dasharray="3,3"/></svg>') repeat;
    }
    .bar {
      flex: 1;
      background-color: #000000;
      border-radius: 4px 4px 0 0;
      transition: height 0.3s ease;
    }
    .pie-chart {
      width: 200px;
      height: 200px;
      background: conic-gradient(
        #000000 0% 25%,
        #4A4A4A 25% 50%,
        #B0B0B0 50% 75%,
        #D1CDC7 75% 100%
      );
      border-radius: 50%;
      margin: auto;
    }
    /* Responsive adjustments */
    @media (max-width: 640px) {
      .bar-chart {
        height: 200px;
        gap: 5px;
        padding: 10px;
      }
      .pie-chart {
        width: 150px;
        height: 150px;
      }
      .key-metrics {
        grid-template-columns: 1fr;
      }
      .charts-section, .alerts-activity {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">
  <?php include 'header.php'; ?>
  

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl sm:text-4xl font-bold text-black mb-4">DASHBOARD</h1>
      <p class="text-base sm:text-lg text-gray-600 italic">
        Overview of your hardware shop performance and key metrics
      </p>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 key-metrics">
      <!-- Total Products -->
      <?php
      $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN stock < min_stock THEN 1 ELSE 0 END) as low_stock FROM products");
      $productData = $stmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <div class="flex flex-row items-center justify-between p-4 space-y-0">
          <h3 class="text-sm font-bold text-gray-600">TOTAL PRODUCTS</h3>
          <i data-lucide="package" class="h-4 w-4 text-gray-400"></i>
        </div>
        <div class="p-4">
          <div class="text-2xl font-bold text-black"><?php echo $productData['total']; ?></div>
          <p class="text-xs text-gray-400 italic"><?php echo $productData['low_stock']; ?> low stock items</p>
        </div>
      </div>

      <!-- Total Sales -->
      <?php
      $stmt = $pdo->query("SELECT SUM(total_amount) as total_sales, COUNT(*) as transactions FROM sales");
      $salesData = $stmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <div class="flex flex-row items-center justify-between p-4 space-y-0">
          <h3 class="text-sm font-bold text-gray-600">TOTAL SALES</h3>
          <i data-lucide="dollar-sign" class="h-4 w-4 text-gray-400"></i>
        </div>
        <div class="p-4">
          <div class="text-2xl font-bold text-black">Ksh.<?php echo number_format($salesData['total_sales'], 2); ?></div>
          <p class="text-xs text-gray-400 italic"><?php echo $salesData['transactions']; ?> transactions</p>
        </div>
      </div>

      <!-- Customers -->
      <?php
      $stmt = $pdo->query("SELECT COUNT(*) as total_customers FROM customers WHERE status = 'active'");
      $customerData = $stmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <div class="flex flex-row items-center justify-between p-4 space-y-0">
          <h3 class="text-sm font-bold text-gray-600">CUSTOMERS</h3>
          <i data-lucide="users" class="h-4 w-4 text-gray-400"></i>
        </div>
        <div class="p-4">
          <div class="text-2xl font-bold text-black"><?php echo $customerData['total_customers']; ?></div>
          <p class="text-xs text-gray-400 italic">Active customer base</p>
        </div>
      </div>

      <!-- Suppliers -->
      <?php
      $stmt = $pdo->query("SELECT COUNT(*) as total_suppliers FROM suppliers WHERE status = 'active'");
      $supplierData = $stmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <div class="flex flex-row items-center justify-between p-4 space-y-0">
          <h3 class="text-sm font-bold text-gray-600">SUPPLIERS</h3>
          <i data-lucide="truck" class="h-4 w-4 text-gray-400"></i>
        </div>
        <div class="p-4">
          <div class="text-2xl font-bold text-black"><?php echo $supplierData['total_suppliers']; ?></div>
          <p class="text-xs text-gray-400 italic">Active partnerships</p>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 charts-section">
      <!-- Sales Chart -->
      <?php
try {
    $stmt = $pdo->query("SELECT DATE_FORMAT(sale_date, '%b') as month, SUM(total_amount) as total FROM sales GROUP BY DATE_FORMAT(sale_date, '%m-%Y') ORDER BY sale_date DESC LIMIT 6");
    $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $months = [];
    for ($i = 5; $i >= 0; $i--) {
        $months[] = date('M', strtotime("-$i months", strtotime('2025-10-19')));
    }
    $heights = [];
    foreach ($salesData as $data) {
        $monthIndex = array_search(substr($data['month'], 0, 3), $months);
        if ($monthIndex !== false) {
            $heights[$monthIndex] = min(200, ($data['total'] / 100) * 20); // Adjusted scaling
        }
    }
} catch (PDOException $e) {
    echo "<p class='text-red-500'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $salesData = [];
    $months = ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'];
    $heights = array_fill(0, 6, 0); // Default to zero heights on error
}
?>
<div class="bg-white border border-gray-200 rounded-lg shadow-sm">
  <div class="p-4">
    <h3 class="text-xl font-bold text-black">SALES OVERVIEW</h3>
    <p class="italic text-gray-600">Monthly sales performance</p>
  </div>
  <div class="p-4">
    <div class="bar-chart">
      <?php for ($i = 0; $i < 6; $i++) {
          $height = isset($heights[$i]) ? $heights[$i] : 0;
          echo "<div class='bar' style='height: {$height}px;' data-height='{$height}px'></div>";
      } ?>
    </div>
    <div class="flex justify-between mt-2 text-gray-600 text-sm">
      <?php foreach ($months as $month) echo "<span>$month</span>"; ?>
    </div>
  </div>
</div>

      <!-- Category Distribution -->
      <?php
      $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM products GROUP BY category");
      $categoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $totalProducts = array_sum(array_column($categoryData, 'count'));
      $colors = ['#000000', '#4A4A4A', '#B0B0B0', '#D1CDC7'];
      $gradient = '';
      $start = 0;
      foreach ($categoryData as $index => $data) {
          $percentage = ($data['count'] / $totalProducts) * 100;
          $end = $start + $percentage;
          $gradient .= "{$colors[$index % 4]} {$start}% {$end}%, ";
          $start = $end;
      }
      $gradient = rtrim($gradient, ', ');
      ?>
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4">
          <h3 class="text-xl font-bold text-black">PRODUCT CATEGORIES</h3>
          <p class="italic text-gray-600">Distribution by category</p>
        </div>
        <div class="p-4">
          <div class="pie-chart" style="background: conic-gradient(<?php echo $gradient; ?>);"></div>
          <div class="flex flex-wrap justify-center gap-4 mt-4">
            <?php foreach ($categoryData as $index => $data) {
                $percentage = ($data['count'] / $totalProducts) * 100;
                echo "<div class='flex items-center'><span class='w-4 h-4 bg-{$colors[$index % 4]} mr-2'></span><span class='text-sm text-gray-600'>{$data['category']} (" . number_format($percentage, 2) . "%)</span></div>";
            } ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Alerts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 alerts-activity">
      <!-- Low Stock Alerts -->
      <?php
      $stmt = $pdo->query("SELECT name, stock, min_stock FROM products WHERE stock < min_stock LIMIT 2");
      $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4">
          <h3 class="text-xl font-bold text-black flex items-center">
            <i data-lucide="alert-triangle" class="w-5 h-5 mr-2 text-orange-500"></i>
            LOW STOCK ALERTS
          </h3>
          <p class="italic text-gray-600">Products requiring attention</p>
        </div>
        <div class="p-4 space-y-3">
          <?php foreach ($alerts as $alert) { ?>
            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200 hover:bg-orange-100 transition-colors">
              <div>
                <p class="font-medium text-black"><?php echo htmlspecialchars($alert['name']); ?></p>
                <p class="text-sm text-gray-600 italic">Stock: <?php echo $alert['stock']; ?> / Min: <?php echo $alert['min_stock']; ?></p>
              </div>
              <span class="bg-orange-500 text-white text-xs font-medium px-2.5 py-0.5 rounded">Low Stock</span>
            </div>
          <?php } ?>
        </div>
      </div>

      <!-- Recent Sales -->
      <?php
      $stmt = $pdo->query("SELECT sale_id, sale_date, total_amount, item_count FROM sales ORDER BY sale_date DESC LIMIT 2");
      $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4">
          <h3 class="text-xl font-bold text-black flex items-center">
            <i data-lucide="shopping-cart" class="w-5 h-5 mr-2"></i>
            RECENT SALES
          </h3>
          <p class="italic text-gray-600">Latest transactions</p>
        </div>
        <div class="p-4 space-y-3">
          <?php foreach ($sales as $sale) { ?>
            <div class="flex items-center justify-between p-3 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 transition-colors">
              <div>
                <p class="font-medium text-black">Sale #<?php echo htmlspecialchars($sale['sale_id']); ?></p>
                <p class="text-sm text-gray-600 italic"><?php echo date('m/d/Y', strtotime($sale['sale_date'])); ?></p>
              </div>
              <div class="text-right">
                <p class="text-lg font-bold text-black">Ksh.<?php echo number_format($sale['total_amount'], 2); ?></p>
                <p class="text-sm text-gray-400 italic"><?php echo $sale['item_count']; ?> items</p>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();
  </script>
</body>
</html>