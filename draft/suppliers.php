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
          SUPPLIERS LIST (3)
        </h3>
        <p class="text-gray-600 italic font-normal">View and manage supplier information</p>
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
            <tr class="hover:bg-gray-50">
              <td class="py-2 text-black font-medium" data-label="Supplier ID">SUP-001</td>
              <td class="py-2 text-gray-600" data-label="Name">Tech Distributors Ltd</td>
              <td class="py-2 text-gray-600" data-label="Contact">+254 712 345 678</td>
              <td class="py-2 text-gray-600" data-label="Email">info@techdistro.com</td>
              <td class="py-2 font-bold text-black" data-label="Total Orders">24</td>
              <td class="py-2" data-label="Status">
                <span class="badge bg-green-600 text-white">active</span>
              </td>
              <td class="py-2" data-label="Actions">
                <button id="viewSupplierDetails" class="border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                  <i data-lucide="eye" class="w-4 h-4"></i>
                </button>
              </td>
            </tr>
            <tr class="hover:bg-gray-50">
              <td class="py-2 text-black font-medium" data-label="Supplier ID">SUP-002</td>
              <td class="py-2 text-gray-600" data-label="Name">Lighting World Co</td>
              <td class="py-2 text-gray-600" data-label="Contact">+254 733 567 890</td>
              <td class="py-2 text-gray-600" data-label="Email">sales@lightingworld.co.ke</td>
              <td class="py-2 font-bold text-black" data-label="Total Orders">12</td>
              <td class="py-2" data-label="Status">
                <span class="badge bg-green-600 text-white">active</span>
              </td>
              <td class="py-2" data-label="Actions">
                <button class="border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                  <i data-lucide="eye" class="w-4 h-4"></i>
                </button>
              </td>
            </tr>
            <tr class="hover:bg-gray-50">
              <td class="py-2 text-black font-medium" data-label="Supplier ID">SUP-003</td>
              <td class="py-2 text-gray-600" data-label="Name">Hardware Hub</td>
              <td class="py-2 text-gray-600" data-label="Contact">+254 701 234 567</td>
              <td class="py-2 text-gray-600" data-label="Email">hardwarehub@gmail.com</td>
              <td class="py-2 font-bold text-black" data-label="Total Orders">8</td>
              <td class="py-2" data-label="Status">
                <span class="badge bg-gray-200 text-gray-800">inactive</span>
              </td>
              <td class="py-2" data-label="Actions">
                <button class="border border-gray-200 text-black px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                  <i data-lucide="eye" class="w-4 h-4"></i>
                </button>
              </td>
            </tr>
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

      <form class="space-y-4">
        <div>
          <label class="text-gray-600 font-normal">Supplier Name</label>
          <input type="text" placeholder="Enter supplier name" class="mt-1 w-full border border-gray-300 rounded-md p-2" required />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-gray-600 font-normal">Contact</label>
            <input type="text" placeholder="+254 7XX XXX XXX" class="mt-1 w-full border border-gray-300 rounded-md p-2" required />
          </div>
          <div>
            <label class="text-gray-600 font-normal">Email</label>
            <input type="email" placeholder="supplier@example.com" class="mt-1 w-full border border-gray-300 rounded-md p-2" required />
          </div>
        </div>
        <div>
          <label class="text-gray-600 font-normal">Address</label>
          <input type="text" placeholder="Enter supplier address" class="mt-1 w-full border border-gray-300 rounded-md p-2" required />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-gray-600 font-normal">Payment Terms</label>
            <select class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
              <option value="">Select payment terms</option>
              <option value="net-30">Net 30 Days</option>
              <option value="net-60">Net 60 Days</option>
              <option value="cash-on-delivery">Cash on Delivery</option>
            </select>
          </div>
          <div>
            <label class="text-gray-600 font-normal">Delivery Lead Time</label>
            <input type="number" placeholder="Days" min="1" class="mt-1 w-full border border-gray-300 rounded-md p-2" required />
          </div>
        </div>
        <div>
          <label class="text-gray-600 font-normal">Website (Optional)</label>
          <input type="url" placeholder="https://example.com" class="mt-1 w-full border border-gray-300 rounded-md p-2" />
        </div>
        <div>
          <label class="text-gray-600 font-normal">Notes (Optional)</label>
          <textarea placeholder="Additional notes..." class="mt-1 w-full border border-gray-300 rounded-md p-2" rows="3"></textarea>
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
            SUPPLIER DETAILS — <span class="text-blue-700">Tech Distributors Ltd</span>
          </h2>
          <p class="text-gray-500 italic font-normal">Full supplier information and order history</p>
        </div>
        <button id="closeSupplierDetailsDialog" class="text-gray-500 hover:text-gray-800 transition">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-gray-50 rounded-xl p-4 mb-6">
        <div>
          <label class="text-gray-500 text-sm">Supplier ID</label>
          <p class="text-gray-900 font-medium">SUP-001</p>
        </div>
        <div>
          <label class="text-gray-500 text-sm">Contact</label>
          <p class="text-gray-900 font-medium flex items-center gap-2">
            <i data-lucide="phone" class="w-4 h-4 text-gray-600"></i>
            +254 712 345 678
          </p>
        </div>
        <div>
          <label class="text-gray-500 text-sm">Email</label>
          <p class="text-gray-900 font-medium flex items-center gap-2">
            <i data-lucide="mail" class="w-4 h-4 text-gray-600"></i>
            info@techdistro.com
          </p>
        </div>
        <div>
          <label class="text-gray-500 text-sm">Status</label>
          <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-sm font-medium px-3 py-1 rounded-full">
            <i data-lucide="check-circle" class="w-4 h-4"></i> Active
          </span>
        </div>
      </div>

      <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
        <i data-lucide="list" class="w-5 h-5 text-blue-600"></i> Order History
      </h3>
      <div class="border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="text-left text-gray-600 font-semibold py-3 px-4">Order ID</th>
              <th class="text-left text-gray-600 font-semibold py-3 px-4">Date</th>
              <th class="text-left text-gray-600 font-semibold py-3 px-4">Total</th>
              <th class="text-left text-gray-600 font-semibold py-3 px-4">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr class="hover:bg-gray-50 transition">
              <td class="py-3 px-4 text-gray-800 font-medium">PO-001</td>
              <td class="py-3 px-4 text-gray-600">10/12/2025</td>
              <td class="py-3 px-4 text-gray-900 font-semibold">$2,345.00</td>
              <td class="py-3 px-4">
                <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">
                  <i data-lucide="truck" class="w-3 h-3"></i> Delivered
                </span>
              </td>
            </tr>
            <tr class="hover:bg-gray-50 transition">
              <td class="py-3 px-4 text-gray-800 font-medium">PO-002</td>
              <td class="py-3 px-4 text-gray-600">09/29/2025</td>
              <td class="py-3 px-4 text-gray-900 font-semibold">$1,180.00</td>
              <td class="py-3 px-4">
                <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium">
                  <i data-lucide="clock" class="w-3 h-3"></i> Pending
                </span>
              </td>
            </tr>
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
    const viewSupplierDetails = document.getElementById("viewSupplierDetails");
    const closeSupplierDetailsDialog = document.querySelectorAll("#closeSupplierDetailsDialog, #closeSupplierButton");

    openSupplierDialog.addEventListener("click", () => {
      supplierDialog.classList.remove("hidden");
    });

    closeSupplierDialog.forEach(button => {
      button.addEventListener("click", () => {
        supplierDialog.classList.add("hidden");
      });
    });

    viewSupplierDetails.addEventListener("click", () => {
      supplierDetailsDialog.classList.remove("hidden");
    });

    closeSupplierDetailsDialog.forEach(button => {
      button.addEventListener("click", () => {
        supplierDetailsDialog.classList.add("hidden");
      });
    });
  </script>
</body>
</html>