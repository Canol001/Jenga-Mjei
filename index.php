<?php
ob_start(); // Prevent header errors
session_start();
include 'config.php';

// Check if user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: homepage.php");
    exit();
}

// Check if users table is empty and handle first user setup
$firstUserSetup = false;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();

    if ($userCount == 0) {
        $firstUserSetup = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_first_user'])) {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $passwordPlain = trim($_POST['password']);
            $password = password_hash($passwordPlain, PASSWORD_DEFAULT);
            $userId = 'ADM-001';
            $role = 'admin';
            $status = 'active';
            $permissions = [
                'inventory' => true,
                'suppliers' => true,
                'sales' => true,
                'reports' => true,
                'customers' => true,
                'users' => true
            ];
            $permissionsJson = json_encode($permissions, JSON_PRETTY_PRINT);

            // 🧩 Print debug info (commented out)
            // echo "<pre style='background:#111;color:#0f0;padding:15px;border-radius:8px;'>";
            // echo "DEBUG: Data to be inserted into database\n\n";
            // echo "User ID: $userId\n";
            // echo "Name: $name\n";
            // echo "Email: $email\n";
            // echo "Plain Password: $passwordPlain\n";
            // echo "Hashed Password: $password\n";
            // echo "Role: $role\n";
            // echo "Status: $status\n";
            // echo "Permissions:\n$permissionsJson\n";
            // echo "</pre>";

            $stmt = $pdo->prepare("INSERT INTO users (user_id, name, email, password, role, status, permissions)
                                   VALUES (:user_id, :name, :email, :password, :role, :status, :permissions)");
            $stmt->execute([
                ':user_id' => $userId,
                ':name' => $name,
                ':email' => $email,
                ':password' => $password,
                ':role' => $role,
                ':status' => $status,
                ':permissions' => $permissionsJson
            ]);
            $_SESSION['loggedin'] = true;
            header("Location: homepage.php");
            exit();
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Loading - Jenga-Mjei Management</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Animation for progress bar */
        @keyframes progress {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        .progress-bar {
            height: 1rem;
            background-color: #10B981; /* Green for hardware theme */
            animation: progress 5s linear forwards;
            border-radius: 0.25rem;
        }
        /* Popup styles */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 50;
            width: 90%;
            max-width: 400px;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="text-center">
        <div class="w-64 bg-gray-200 rounded-lg overflow-hidden mb-4">
            <div class="progress-bar" style="width: 0%;"></div>
        </div>
        <h2 class="text-xl font-bold text-gray-700">Loading Jenga-Mjei Management</h2>
        <p class="mt-2 text-gray-600">Setting up your hardware management system...</p>
    </div>

    <!-- Popup for first user setup -->
    <div id="setup-popup" class="popup">
        <h3 class="text-lg font-bold text-gray-700 mb-4">Welcome! First-Time Setup</h3>
        <p class="text-gray-600 mb-4">Since you're the first user, please set up your account. You will be granted full admin privileges.</p>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="setup_first_user" value="1">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="name" name="name" placeholder="e.g., Jane Doe" class="mt-1 w-full p-2 border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" placeholder="jane.doe@company.com" class="mt-1 w-full p-2 border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" class="mt-1 w-full p-2 border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">Set Up Account</button>
        </form>
    </div>
    <div id="overlay" class="overlay"></div>

    <!-- Script -->
    <script>
        lucide.createIcons();

        // Redirect to homepage after 5 seconds if not first user, and show popup if first user
        setTimeout(() => {
            if (<?php echo json_encode($firstUserSetup); ?>) {
                document.getElementById('setup-popup').style.display = 'block';
                document.getElementById('overlay').style.display = 'block';
            } else {
                window.location.href = 'homepage.php';
            }
        }, 5000); // 5000 milliseconds = 5 seconds
    </script>
</body>
</html>
<?php ob_end_flush(); // Flush the output buffer ?>