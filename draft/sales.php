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
          SALES HISTORY (3)
        </h3>
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
            <tr class="border-b border-gray-100 hover:bg-gray-50">
              <td class="py-2 font-medium text-black" data-label="Invoice">INV-001</td>
              <td class="py-2 text-gray-600" data-label="Customer">John Doe</td>
              <td class="py-2 text-gray-600" data-label="Items">2 items</td>
              <td class="py-2 font-bold text-black" data-label="Total">Ksh.197.97</td>
              <td class="py-2 flex items-center space-x-1 text-gray-600" data-label="Payment">
                <i data-lucide="banknote" class="w-4 h-4"></i><span>cash</span>
              </td>
              <td class="py-2 text-gray-600" data-label="Date">10/15/2025</td>
              <td class="py-2" data-label="Status"><span class="badge bg-blue-600 text-white">completed</span></td>
              <td class="py-2" data-label="Actions">
                <button id="openSaleDetails" class="border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                  <i data-lucide="eye" class="w-4 h-4"></i>
                </button>
              </td>
            </tr>
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Left -->
          <div class="space-y-6">
            <div>
              <label class="text-gray-600 font-medium">Customer</label>
              <select class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required>
                <option value="">Select customer</option>
                <option value="walk-in">Walk-in Customer</option>
                <option value="cust1">John Doe - 555-1234</option>
              </select>
            </div>
            <div>
              <label class="text-gray-600 font-medium">Add Products</label>
              <div class="relative mt-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input id="productSearch" placeholder="Search products..." class="w-full pl-10 border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" />
              </div>
              <div id="productList" class="mt-2 border border-gray-200 rounded-lg max-h-48 overflow-y-auto">
                <div class="p-3 hover:bg-gray-100 border-b border-gray-200 flex justify-between items-center cursor-pointer" data-product="Cordless Drill" data-sku="DRILL-001" data-price="89.99">
                  <div>
                    <p class="font-medium text-black">Cordless Drill</p>
                    <p class="text-sm text-gray-600 italic">DRILL-001 - Stock: 8</p>
                  </div>
                  <p class="font-bold text-black">Ksh.89.99</p>
                </div>
                <!-- Add more products as needed -->
              </div>
            </div>
            <div>
              <label class="text-gray-600 font-medium">Discount (Optional)</label>
              <input type="number" step="0.01" placeholder="Enter discount amount or %" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label class="text-gray-600 font-medium">Date</label>
              <input type="date" value="2025-10-19" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required />
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
              <select class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" required>
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
              <button id="cancelNewSale" class="flex-1 border border-gray-200 text-gray-900 px-4 py-2 rounded-md hover:bg-gray-100 transition-colors">Cancel</button>
              <button id="processSale" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center gap-2 transition-colors">
                <i data-lucide="receipt" class="w-5 h-5"></i>
                Process Sale
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sale Details Modal -->
    <div id="saleDetailsDialog" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
              <i data-lucide="shopping-cart" class="w-6 h-6 text-blue-600"></i>
              SALE DETAILS - INV-001
            </h2>
            <p class="text-gray-500 italic font-normal">Complete transaction information</p>
          </div>
          <button id="closeSaleDetails" class="text-gray-500 hover:text-gray-800 transition">
            <i data-lucide="x" class="w-6 h-6"></i>
          </button>
        </div>

        <div class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="text-gray-600 font-medium">Customer</label><p class="text-black">John Doe</p></div>
            <div><label class="text-gray-600 font-medium">Date</label><p class="text-black">10/15/2025</p></div>
            <div><label class="text-gray-600 font-medium">Payment</label><p class="text-black capitalize">cash</p></div>
            <div><label class="text-gray-600 font-medium">Cashier</label><p class="text-black">Current User</p></div>
          </div>
          <div>
            <label class="text-gray-600 font-medium">Items Purchased</label>
            <div class="mt-2 border border-gray-200 rounded-lg">
              <div class="flex justify-between p-3 border-b border-gray-200">
                <div><p class="font-medium text-black">Cordless Drill</p><p class="text-sm text-gray-600 italic">DRILL-001</p></div>
                <p class="text-black">2 × Ksh.89.99 = Ksh.179.98</p>
              </div>
              <div class="flex justify-between p-3">
                <div><p class="font-medium text-black">Screws Pack</p><p class="text-sm text-gray-600 italic">SCREW-005</p></div>
                <p class="text-black">1 × Ksh.5.99 = Ksh.5.99</p>
              </div>
            </div>
          </div>
          <div class="border-t border-gray-200 pt-4 space-y-2">
            <div class="flex justify-between"><span class="text-gray-600">Subtotal:</span><span class="font-bold text-black">Ksh.185.97</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Tax:</span><span class="font-bold text-black">Ksh.18.60</span></div>
            <div class="flex justify-between text-lg"><span class="font-bold text-black">Total:</span><span class="font-bold text-black">Ksh.204.57</span></div>
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

      document.getElementById("openNewSale").addEventListener("click", () => {
        newSaleDialog.classList.remove("hidden");
      });
      document.getElementById("cancelNewSale").addEventListener("click", () => newSaleDialog.classList.add("hidden"));
      document.getElementById("closeNewSale").addEventListener("click", () => newSaleDialog.classList.add("hidden"));

      document.getElementById("openSaleDetails").addEventListener("click", () => {
        saleDetailsDialog.classList.remove("hidden");
      });
      document.getElementById("closeSaleDetails").addEventListener("click", () => saleDetailsDialog.classList.add("hidden"));

      // Handle product selection
      productList.addEventListener("click", (e) => {
        if (e.target.closest(".flex")) {
          const productDiv = e.target.closest(".flex");
          const productName = productDiv.getAttribute("data-product");
          const sku = productDiv.getAttribute("data-sku");
          const price = parseFloat(productDiv.getAttribute("data-price"));
          const quantityInput = document.createElement("input");
          quantityInput.type = "number";
          quantityInput.min = "1";
          quantityInput.value = "1";
          quantityInput.className = "w-16 border border-gray-300 rounded-md p-1 ml-2 focus:ring-2 focus:ring-blue-500";

          const itemDiv = document.createElement("div");
          itemDiv.className = "flex justify-between items-center p-2 border-b border-gray-200";
          itemDiv.innerHTML = `
            <div>
              <p class="font-medium text-black">${productName}</p>
              <p class="text-sm text-gray-600 italic">${sku}</p>
            </div>
            <div class="flex items-center">
              <span class="font-bold text-black">Ksh.${price.toFixed(2)}</span>
              ${quantityInput.outerHTML}
            </div>
          `;

          saleItems.innerHTML = ""; // Clear placeholder
          saleItems.appendChild(itemDiv);

          // Update totals (simple calculation for demo)
          const quantity = parseInt(quantityInput.value);
          const subtotal = price * quantity;
          const tax = subtotal * 0.1;
          const total = subtotal + tax;

          subtotalElement.textContent = `Ksh.${subtotal.toFixed(2)}`;
          taxElement.textContent = `Ksh.${tax.toFixed(2)}`;
          totalElement.textContent = `Ksh.${total.toFixed(2)}`;

          quantityInput.addEventListener("change", () => {
            const newQuantity = parseInt(quantityInput.value);
            const newSubtotal = price * newQuantity;
            const newTax = newSubtotal * 0.1;
            const newTotal = newSubtotal + newTax;

            subtotalElement.textContent = `Ksh.${newSubtotal.toFixed(2)}`;
            taxElement.textContent = `Ksh.${newTax.toFixed(2)}`;
            totalElement.textContent = `Ksh.${newTotal.toFixed(2)}`;
          });
        }
      });
    });
  </script>
</body>
</html>