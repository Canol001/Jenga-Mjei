<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory Management</title>
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
  <?php include 'config.php'; ?>

  <?php
  // Handle form submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
      $name = $_POST['name'];
      $sku = $_POST['sku'];
      $description = $_POST['description'];
      $category = $_POST['category'];
      $supplier = $_POST['supplier'];
      $cost_price = $_POST['cost_price'];
      $selling_price = $_POST['selling_price'];
      $current_stock = $_POST['current_stock'];
      $min_stock = $_POST['min_stock'];
      $unit = $_POST['unit'];
      $reorder_level = $_POST['reorder_level'];
      $image_url = $_POST['image_url'] ?: null;
      $status = $_POST['status'];

      try {
          $stmt = $pdo->prepare("INSERT INTO products (name, sku, description, category, supplier, cost_price, selling_price, stock, min_stock, unit, reorder_level, image_url, status, created_at) VALUES (:name, :sku, :description, :category, :supplier, :cost_price, :selling_price, :stock, :min_stock, :unit, :reorder_level, :image_url, :status, NOW())");
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
          echo "<script>alert('Product added successfully!'); document.getElementById('addProductDialog').classList.add('hidden');</script>";
      } catch (PDOException $e) {
          echo "<script>alert('Error adding product: " . addslashes($e->getMessage()) . "');</script>";
      }
  }

  // Fetch products
  $stmt = $pdo->query("SELECT * FROM products LIMIT 10"); // Adjust limit as needed
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <header class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-8 space-y-4 sm:space-y-0">
      <div>
        <h1 class="text-3xl sm:text-4xl font-bold text-black mb-2">INVENTORY MANAGEMENT</h1>
        <p class="text-base sm:text-lg text-gray-600 italic">
          Manage your product inventory, track stock levels, and monitor alerts.
        </p>
      </div>
      <div class="flex flex-wrap gap-2 sm:space-x-3">
        <button class="border border-blue-600 text-blue-600 px-3 py-2 rounded-md flex items-center hover:bg-blue-50 transition-colors w-full sm:w-auto justify-center">
          <i data-lucide="download" class="w-4 h-4 mr-2"></i>
          Export
        </button>
        <button class="border border-blue-600 text-blue-600 px-3 py-2 rounded-md flex items-center hover:bg-blue-50 transition-colors w-full sm:w-auto justify-center">
          <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
          Import
        </button>
        <button id="openDialogBtn" class="bg-blue-600 text-white px-3 py-2 rounded-md flex items-center hover:bg-blue-700 transition-colors w-full sm:w-auto justify-center">
          <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
          Add Product
        </button>
      </div>
    </header>

    <!-- Add Product Modal -->
    <div id="addProductDialog" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
      <!-- Inner Scrollable Container -->
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6">
        <div class="flex justify-between items-start mb-4 sticky top-0 bg-white z-10 pb-2 border-b">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
              <i data-lucide="package" class="w-6 h-6 text-blue-600"></i>
              ADD NEW PRODUCT
            </h2>
            <p class="text-gray-500 italic font-normal">Enter product details to add to inventory.</p>
          </div>
          <button id="closeDialogBtn" class="text-gray-500 hover:text-gray-800 transition">
            <i data-lucide="x" class="w-6 h-6"></i>
          </button>
        </div>

        <form method="POST" class="space-y-6 pb-4">
          <input type="hidden" name="add_product" value="1">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-gray-600 font-medium">Product Name</label>
              <input type="text" name="name" placeholder="Enter product name" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div>
              <label class="text-gray-600 font-medium">SKU</label>
              <input type="text" name="sku" placeholder="Enter SKU" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
            </div>
          </div>

          <div>
            <label class="text-gray-600 font-medium">Description</label>
            <textarea name="description" placeholder="Enter product description" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-gray-600 font-medium">Category</label>
              <select name="category" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required>
                <option value="">Select category</option>
                <option value="Tools">Tools</option>
                <option value="Hardware">Hardware</option>
                <option value="Electrical">Electrical</option>
              </select>
            </div>
            <div>
              <label class="text-gray-600 font-medium">Supplier</label>
              <select name="supplier" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required>
                <option value="">Select supplier</option>
                <option value="ABC Supply Co">ABC Supply Co</option>
                <option value="Tool World">Tool World</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-gray-600 font-medium">Cost Price</label>
              <input type="number" name="cost_price" step="0.01" placeholder="Enter cost price" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div>
              <label class="text-gray-600 font-medium">Selling Price</label>
              <input type="number" name="selling_price" step="0.01" placeholder="Enter selling price" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-gray-600 font-medium">Current Stock</label>
              <input type="number" name="current_stock" placeholder="Enter current stock" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div>
              <label class="text-gray-600 font-medium">Minimum Stock</label>
              <input type="number" name="min_stock" placeholder="Enter minimum stock" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-gray-600 font-medium">Unit of Measure</label>
              <select name="unit" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required>
                <option value="">Select unit</option>
                <option value="pieces">Pieces</option>
                <option value="kg">Kilograms</option>
                <option value="liters">Liters</option>
              </select>
            </div>
            <div>
              <label class="text-gray-600 font-medium">Reorder Level</label>
              <input type="number" name="reorder_level" placeholder="Enter reorder level" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-gray-600 font-medium">Product Image URL</label>
              <input type="text" name="image_url" placeholder="https://example.com/image.jpg" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label class="text-gray-600 font-medium">Status</label>
              <select name="status" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
            <button id="closeDialogBtn" type="button" class="border border-gray-200 text-gray-900 px-4 py-2 rounded-md hover:bg-gray-100 transition-colors">
              Cancel
            </button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center gap-2 transition-colors">
              <i data-lucide="save" class="w-5 h-5"></i>
              Add Product
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Filters -->
    <section class="bg-white border border-gray-200 rounded-lg mb-6 p-4 sm:p-6 shadow-sm">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div>
          <label class="text-gray-600 font-normal">Search Products</label>
          <div class="relative mt-1">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input placeholder="Search by name, SKU..." class="w-full pl-10 border border-gray-300 rounded-md p-2" />
          </div>
        </div>
        <div>
          <label class="text-gray-600 font-normal">Category</label>
          <select class="mt-1 w-full border border-gray-300 rounded-md p-2">
            <option>All</option>
            <option>Tools</option>
            <option>Hardware</option>
          </select>
        </div>
        <div>
          <label class="text-gray-600 font-normal">Status</label>
          <select class="mt-1 w-full border border-gray-300 rounded-md p-2">
            <option>All</option>
            <option>Active</option>
          </select>
        </div>
        <div class="flex items-end">
          <button class="w-full border border-blue-600 text-blue-600 px-4 py-2 rounded-md flex items-center justify-center hover:bg-blue-50 transition-colors">
            <i data-lucide="filter" class="w-4 h-4 mr-2"></i> Clear Filters
          </button>
        </div>
      </div>
    </section>

    <!-- Products Table -->
    <section class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="p-4 border-b border-gray-200">
        <h3 class="text-lg sm:text-xl font-bold text-black flex items-center">
          <i data-lucide="package" class="w-5 h-5 mr-2"></i>
          PRODUCTS (<?php echo count($products); ?>)
        </h3>
        <p class="text-gray-600 italic text-sm sm:text-base">Manage your product inventory and stock levels.</p>
      </div>

      <div class="p-4 overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left font-bold text-gray-600 py-2">Image</th>
              <th class="text-left font-bold text-gray-600 py-2">Product</th>
              <th class="text-left font-bold text-gray-600 py-2">SKU</th>
              <th class="text-left font-bold text-gray-600 py-2">Category</th>
              <th class="text-left font-bold text-gray-600 py-2">Stock</th>
              <th class="text-left font-bold text-gray-600 py-2">Price</th>
              <th class="text-left font-bold text-gray-600 py-2">Status</th>
              <th class="text-left font-bold text-gray-600 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product) { ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="py-2" data-label="Image">
                  <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                    <?php if ($product['image_url']) { ?>
                      <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover rounded">
                    <?php } else { ?>
                      <i data-lucide="package" class="w-5 h-5 text-gray-400"></i>
                    <?php } ?>
                  </div>
                </td>
                <td class="py-2" data-label="Product">
                  <p class="font-medium text-black"><?php echo htmlspecialchars($product['name']); ?></p>
                  <p class="text-sm text-gray-600 italic"><?php echo htmlspecialchars($product['supplier']); ?></p>
                </td>
                <td class="py-2 text-gray-600" data-label="SKU"><?php echo htmlspecialchars($product['sku']); ?></td>
                <td class="py-2 text-gray-600" data-label="Category"><?php echo htmlspecialchars($product['category']); ?></td>
                <td class="py-2" data-label="Stock">
                  <div class="flex items-center space-x-2">
                    <span class="text-black"><?php echo $product['stock']; ?></span>
                    <?php if ($product['stock'] < $product['min_stock']) { ?>
                      <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-500"></i>
                      <span class="badge bg-orange-500 text-white">Low Stock</span>
                    <?php } ?>
                  </div>
                </td>
                <td class="py-2 text-black" data-label="Price">Ksh.<?php echo number_format($product['selling_price'], 2); ?></td>
                <td class="py-2" data-label="Status">
                  <span class="badge bg-<?php echo $product['status'] === 'Active' ? 'blue-600' : 'gray-500'; ?> text-white"><?php echo htmlspecialchars($product['status']); ?></span>
                </td>
                <td class="py-2" data-label="Actions">
                  <div class="flex space-x-2">
                    <button class="border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                      <i data-lucide="edit" class="w-4 h-4"></i>
                    </button>
                    <button class="border border-gray-200 text-red-600 px-2 py-1 rounded hover:bg-red-50 transition-colors">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <?php include 'footer.php'; ?>

  <script>
    lucide.createIcons();
    const dialog = document.getElementById('addProductDialog');
    const openDialogBtn = document.getElementById('openDialogBtn');
    const closeDialogBtn = document.getElementById('closeDialogBtn');

    openDialogBtn.addEventListener('click', () => {
      dialog.classList.remove('hidden');
    });

    closeDialogBtn.addEventListener('click', () => {
      dialog.classList.add('hidden');
    });
  </script>
</body>
</html>