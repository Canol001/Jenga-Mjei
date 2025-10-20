CREATE DATABASE IF NOT EXISTS hardware_shop;
USE hardware_shop;

CREATE TABLE `customers` (
  `customer_id` varchar(10) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `loyalty_points` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `company`, `email`, `phone`, `address`, `status`, `loyalty_points`, `notes`, `date_added`) VALUES
('CUST74115', 'CANOL OWANA', 'Venom Tech', 'canolowana6@gmail.com', '0790502670', 'Maseno', 'active', 0, 'The foreman at maseno new NL building', '2025-10-20 16:38:30'),
('CUST002', 'Mary Atieno', 'Venom Laboratory', 'mary.atieno@example.com', '+254798765432', 'Kisumu, Kenya', 'Inactive', 45, 'Interested in upcoming biometric scanners.', '2025-10-20 16:28:27');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `supplier` varchar(255) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `min_stock` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `reorder_level` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `sku`, `description`, `category`, `supplier`, `cost_price`, `selling_price`, `stock`, `min_stock`, `unit`, `reorder_level`, `image_url`, `status`, `created_at`) VALUES
(1, 'Hammer', 'Steel hammer with rubber grip', NULL, 'Tools', 'ABC Supply Co', 450.00, 700.00, 20, 0, '5', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(2, 'Nails', 'Box of 100 nails', NULL, 'Hardware', 'Tool World', 150.00, 250.00, 50, 0, '10', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(3, 'Screwdriver', '5-piece precision screwdriver set', NULL, 'Tools', 'ABC Supply Co', 600.00, 950.00, 15, 0, '5', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(4, 'Drill', 'Cordless drill with charger', NULL, 'Electrical', 'PowerPro', 3200.00, 4500.00, 8, 0, '3', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(5, 'PaintBrush', '2-inch paint brush', NULL, 'Hardware', 'PaintWorld', 120.00, 250.00, 35, 0, '10', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(6, 'Cement', '50kg bag of cement', NULL, 'Hardware', 'BuildMaster', 800.00, 1100.00, 60, 0, '15', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(7, 'Water pipes', '2-meter PVC water pipe', NULL, 'Hardware', 'PipeWorks', 300.00, 550.00, 25, 0, '5', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(8, 'Copper wires', '30m electrical copper wire roll', NULL, 'Electrical', 'ElectroHub', 1500.00, 2300.00, 12, 0, '4', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(9, 'Spanner', 'Adjustable spanner set of 6', NULL, 'Tools', 'Tool World', 900.00, 1300.00, 18, 0, '5', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(10, 'Helmet', 'Construction safety helmet', NULL, 'Hardware', 'SafetyPro', 400.00, 700.00, 30, 0, '8', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(11, 'SKU011', 'Energy-saving LED bulb', NULL, 'Electrical', 'BrightLight', 80.00, 150.00, 100, 0, '20', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(12, 'SKU012', '10-meter extension cable', NULL, 'Electrical', 'ElectroHub', 900.00, 1400.00, 22, 0, '5', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(13, 'SKU013', 'Pack of 10 sandpapers', NULL, 'Tools', 'ABC Supply Co', 200.00, 350.00, 40, 0, '10', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(14, 'SKU014', 'Universal plier 8-inch', NULL, 'Tools', 'Tool World', 350.00, 600.00, 25, 0, '7', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(15, 'SKU015', '9-inch roller with handle', NULL, 'Hardware', 'PaintWorld', 350.00, 550.00, 20, 0, '5', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(16, 'SKU016', 'Small portable concrete mixer', NULL, 'Hardware', 'BuildMaster', 12000.00, 15000.00, 3, 0, '1', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(17, 'SKU017', 'Set of 10 assorted drill bits', NULL, 'Tools', 'PowerPro', 600.00, 950.00, 18, 0, '5', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(18, 'SKU018', 'Box of 100 assorted screws', NULL, 'Hardware', 'Tool World', 250.00, 400.00, 45, 0, '10', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(19, 'SKU019', 'Electric wire cutting tool', NULL, 'Electrical', 'ElectroHub', 750.00, 1200.00, 10, 0, '3', 0, NULL, 'Active', '2025-10-20 14:31:40'),
(20, 'SKU020', '10-meter measuring tape', NULL, 'Tools', 'ABC Supply Co', 200.00, 350.00, 30, 0, '8', 0, NULL, 'Active', '2025-10-20 14:31:40');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` varchar(10) NOT NULL,
  `sale_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `item_count` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_name` varchar(255) NOT NULL DEFAULT 'Walk-in Customer',
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `status` varchar(50) NOT NULL DEFAULT 'completed',
  `cashier_id` int(11) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `invoice_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sale_id`, `sale_date`, `total_amount`, `item_count`, `created_at`, `customer_name`, `payment_method`, `status`, `cashier_id`, `discount`, `tax_amount`, `invoice_number`) VALUES
('2K7LOAWF', '2025-10-20', 5.00, 0, '2025-10-20 17:04:08', 'CUST74115', 'cash', 'completed', NULL, 0.00, 0.00, 'INV-1760979848'),
('AQ5JPZ7T', '2025-10-20', 1122.00, 0, '2025-10-20 18:02:52', 'CUST74115', 'cash', 'completed', NULL, 0.00, 0.00, 'INV-1760983372'),
('OYHTFMIJ', '2025-10-20', 0.00, 0, '2025-10-20 16:54:19', 'CUST74115', 'cash', 'completed', NULL, 0.00, 0.00, 'INV-1760979259');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` varchar(10) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_name`, `sku`, `price`, `quantity`, `created_at`) VALUES
(1, 'AQ5JPZ7T', 'Hammer', 'Steel hammer with rubber grip', 450.00, 2, '2025-10-20 18:02:52'),
(2, 'AQ5JPZ7T', 'PaintBrush', '2-inch paint brush', 120.00, 1, '2025-10-20 18:02:52');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `payment_terms` varchar(50) NOT NULL,
  `delivery_lead_time` int(11) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `name`, `contact`, `email`, `address`, `payment_terms`, `delivery_lead_time`, `website`, `notes`, `status`, `created_at`) VALUES
('SUP-148', 'CANOL OWANA', '+254790502671', 'canolowana6@gmail.com', 'Maseno', 'cash-on-delivery', 3, 'https://admin-panel.atax.co.ke/', 'notable', 'active', '2025-10-20 17:03:30'),
('SUP-936', 'OKOTH CANOL', '+254790502670', 'canolowana5@gmail.com', 'Maseno', 'net-30', 5, 'https://admin-panel.atax.co.ke/', 'nails', 'active', '2025-10-20 17:00:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(10) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `last_login` datetime DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`) ON DELETE CASCADE;
COMMIT;
