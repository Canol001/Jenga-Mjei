<?php
// ================================
// INVENTORY MANAGEMENT SYSTEM
// ================================
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database config
include 'config.php';

// ----------------------
// ADD PRODUCT
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $sku = trim($_POST['sku']);
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $supplier = $_POST['supplier'] ?? '';
    $cost_price = $_POST['cost_price'] ?? 0;
    $selling_price = $_POST['selling_price'] ?? 0;
    $current_stock = $_POST['current_stock'] ?? 0;
    $min_stock = $_POST['min_stock'] ?? 0;
    $unit = $_POST['unit'] ?? '';
    $reorder_level = $_POST['reorder_level'] ?? 0;
    $image_url = $_POST['image_url'] ?: null;
    $status = $_POST['status'] ?? 'Active';

    try {
        $stmt = $pdo->prepare("INSERT INTO products 
            (name, sku, description, category, supplier, cost_price, selling_price, stock, min_stock, unit, reorder_level, image_url, status, created_at) 
            VALUES 
            (:name, :sku, :description, :category, :supplier, :cost_price, :selling_price, :stock, :min_stock, :unit, :reorder_level, :image_url, :status, NOW())");
        $stmt->execute([
            ':name' => $name,
            ':sku' => $sku,
            ':description' => $description,
            ':category' => $category,
            ':supplier' => $supplier,
            ':cost_price' => $cost_price,
            ':selling_price' => $selling_price,
            ':stock' => $current_stock,
            ':min_stock' => $min_stock,
            ':unit' => $unit,
            ':reorder_level' => $reorder_level,
            ':image_url' => $image_url,
            ':status' => $status
        ]);
        $success_message = "✅ Product added successfully!";
    } catch (PDOException $e) {
        $error_message = "❌ Error adding product: " . $e->getMessage();
    }
}

// ----------------------
// EXPORT PRODUCTS (CSV)
// ----------------------
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="products_export.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'SKU', 'Category', 'Supplier', 'Cost Price', 'Selling Price', 'Stock', 'Unit', 'Status']);
    $stmt = $pdo->query("SELECT * FROM products");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['id'], $row['name'], $row['sku'], $row['category'],
            $row['supplier'], $row['cost_price'], $row['selling_price'],
            $row['stock'], $row['unit'], $row['status']
        ]);
    }
    fclose($output);
    exit;
}

// ----------------------
// IMPORT PRODUCTS (CSV)
// ----------------------
if (isset($_POST['import_csv'])) {
    if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($fileTmp, 'r');
        if ($handle !== false) {
            fgetcsv($handle); // Skip header row
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $stmt = $pdo->prepare("INSERT INTO products 
                    (name, sku, category, supplier, cost_price, selling_price, stock, unit, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    $data[1], $data[2], $data[3], $data[4],
                    $data[5], $data[6], $data[7], $data[8], $data[9]
                ]);
            }
            fclose($handle);
            $success_message = "✅ Products imported successfully!";
        } else {
            $error_message = "❌ Failed to open uploaded file.";
        }
    } else {
        $error_message = "❌ Error uploading file.";
    }
}

// ----------------------
// FETCH PRODUCTS
// ----------------------
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inventory Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 text-gray-800">
<?php include 'header.php'; ?>

<div class="max-w-7xl mx-auto p-6">
  <h1 class="text-3xl font-bold text-black mb-4 flex items-center gap-2">
    <i data-lucide="package"></i> Inventory Management
  </h1>

  <?php if (!empty($success_message)) echo "<p class='bg-green-100 text-green-800 p-2 rounded mb-4'>$success_message</p>"; ?>
  <?php if (!empty($error_message)) echo "<p class='bg-red-100 text-red-800 p-2 rounded mb-4'>$error_message</p>"; ?>

  <!-- Buttons -->
  <div class="flex flex-wrap gap-3 mb-6">
    <button id="openDialogBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center gap-2">
      <i data-lucide="plus"></i> Add Product
    </button>

    <form method="GET" class="inline">
      <button type="submit" name="export" class="border border-blue-600 text-blue-600 px-4 py-2 rounded hover:bg-blue-50 flex items-center gap-2">
        <i data-lucide="download"></i> Export CSV
      </button>
    </form>

    <form method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
      <input type="file" name="csv_file" accept=".csv" required class="border border-gray-300 rounded p-1 text-sm" />
      <button type="submit" name="import_csv" class="border border-green-600 text-green-600 px-4 py-2 rounded hover:bg-green-50 flex items-center gap-2">
        <i data-lucide="upload"></i> Import CSV
      </button>
    </form>
  </div>

  <!-- Products Table -->
  <div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="w-full text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-3 text-left">Image</th>
          <th class="p-3 text-left">Product</th>
          <th class="p-3 text-left">SKU</th>
          <th class="p-3 text-left">Category</th>
          <th class="p-3 text-left">Stock</th>
          <th class="p-3 text-left">Price</th>
          <th class="p-3 text-left">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($products) > 0): ?>
          <?php foreach ($products as $product): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="p-3">
                <?php if ($product['image_url']): ?>
                  <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Product" class="w-12 h-12 object-cover rounded">
                <?php else: ?>
                  <i data-lucide="image-off" class="text-gray-400 w-6 h-6"></i>
                <?php endif; ?>
              </td>
              <td class="p-3 font-medium"><?= htmlspecialchars($product['name']) ?></td>
              <td class="p-3"><?= htmlspecialchars($product['sku']) ?></td>
              <td class="p-3"><?= htmlspecialchars($product['category']) ?></td>
              <td class="p-3"><?= htmlspecialchars($product['stock']) ?></td>
              <td class="p-3">Ksh <?= number_format($product['selling_price'], 2) ?></td>
              <td class="p-3">
                <span class="px-2 py-1 text-xs rounded text-white <?= $product['status'] === 'Active' ? 'bg-blue-600' : 'bg-gray-500' ?>">
                  <?= htmlspecialchars($product['status']) ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="p-4 text-center text-gray-500">No products found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ADD PRODUCT MODAL -->
<div id="addProductDialog" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
  <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Add New Product</h2>
      <button id="closeDialogBtn"><i data-lucide="x"></i></button>
    </div>

    <form method="POST" class="space-y-3">
      <input type="hidden" name="add_product" value="1" />
      <input type="text" name="name" placeholder="Product Name" required class="w-full border p-2 rounded" />
      <input type="text" name="sku" placeholder="SKU" required class="w-full border p-2 rounded" />
      <input type="number" step="0.01" name="cost_price" placeholder="Cost Price" required class="w-full border p-2 rounded" />
      <input type="number" step="0.01" name="selling_price" placeholder="Selling Price" required class="w-full border p-2 rounded" />
      <input type="number" name="current_stock" placeholder="Current Stock" required class="w-full border p-2 rounded" />
      <input type="number" name="min_stock" placeholder="Minimum Stock" required class="w-full border p-2 rounded" />
      <input type="text" name="category" placeholder="Category" required class="w-full border p-2 rounded" />
      <input type="text" name="supplier" placeholder="Supplier" required class="w-full border p-2 rounded" />
      <input type="text" name="unit" placeholder="Unit (e.g., pcs, kg)" required class="w-full border p-2 rounded" />
      <input type="number" name="reorder_level" placeholder="Reorder Level" required class="w-full border p-2 rounded" />
      <input type="text" name="image_url" placeholder="Image URL (optional)" class="w-full border p-2 rounded" />
      <select name="status" class="w-full border p-2 rounded">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>

      <div class="flex justify-end gap-2 mt-3">
        <button type="button" id="cancelBtn" class="border border-gray-400 text-gray-700 px-3 py-1 rounded hover:bg-gray-100">Cancel</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">Add</button>
      </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>
<script>
  lucide.createIcons();

  const dialog = document.getElementById('addProductDialog');
  document.getElementById('openDialogBtn').onclick = () => dialog.classList.remove('hidden');
  document.getElementById('closeDialogBtn').onclick = () => dialog.classList.add('hidden');
  document.getElementById('cancelBtn').onclick = () => dialog.classList.add('hidden');
</script>
</body>
</html>
