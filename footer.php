

  <!-- Footer -->
  <footer class="bg-gray-700 text-white mt-auto">
    <div class="max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid md:grid-cols-4 gap-8">
        <div class="col-span-2">
          <div class="flex items-center space-x-3 mb-4">
            <i data-lucide="package" class="w-8 h-8"></i>
            <span class="text-xl font-semibold">Jenga-Mjei</span>
          </div>
          <p class="italic text-white/80 max-w-md">
            Complete management solution for hardware businesses. Streamline operations, track inventory, and grow your business.
          </p>
        </div>

        <div>
          <h3 class="text-lg font-semibold mb-4">FEATURES</h3>
          <ul class="space-y-2 italic text-white/80">
            <li>Inventory Management</li>
            <li>Sales Processing</li>
            <li>Customer Database</li>
            <li>Supplier Tracking</li>
          </ul>
        </div>

        <div>
          <h3 class="text-lg font-semibold mb-4">SUPPORT</h3>
          <ul class="space-y-2 italic text-white/80">
            <li>Documentation</li>
            <li>Help Center</li>
            <li>Contact Support</li>
            <li>System Status</li>
          </ul>
        </div>
      </div>

      <div class="border-t border-white/20 mt-8 pt-8 text-center">
        <p class="italic text-white/60">
          © 2024 Jenga-Mjei. All rights reserved.
        </p>
      </div>
    </div>
  </footer>

  <!-- Script -->
  <script>
    lucide.createIcons();

    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');
    toggle.addEventListener('click', () => {
      menu.classList.toggle('hidden');
      toggle.innerHTML = menu.classList.contains('hidden')
        ? '<i data-lucide="menu" class="w-6 h-6"></i>'
        : '<i data-lucide="x" class="w-6 h-6"></i>';
      lucide.createIcons();
    });
  </script>
</body>
</html>
