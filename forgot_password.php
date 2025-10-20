<?php
ob_start(); // Prevent header errors
session_start();
include 'config.php';

// Check if user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: homepage.php");
    exit();
}

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_request'])) {
    $email = trim($_POST['email']);

    try {
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // In a real application, generate a unique token, store it, and send an email
            $message = "A password reset link has been sent to $email. Please check your inbox.";
        } else {
            $message = "No account found with that email address.";
        }
    } catch (PDOException $e) {
        $message = "An error occurred: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password - Jenga-Mjei Management</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div id="forgot-password-container" class="max-w-md w-full bg-white p-6 rounded-lg shadow-md relative">
        <button id="close-forgot" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 transition-colors p-1 rounded-full hover:bg-gray-100">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
        <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Forgot Password</h2>
        <?php if (isset($message)) echo "<p class='text-center mb-4 text-" . (strpos($message, 'error') !== false ? 'red' : 'green') . "-500'>$message</p>"; ?>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="reset_request" value="1">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" placeholder="jane.doe@company.com" class="mt-1 w-full p-2 border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition duration-150" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">Send Reset Link</button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-600">
            <a href="login.php" class="text-indigo-600 hover:text-indigo-800">Back to Login</a>
        </p>
    </div>

    <!-- Script -->
    <script>
        lucide.createIcons();

        const closeForgotBtn = document.getElementById('close-forgot');
        const forgotPasswordContainer = document.getElementById('forgot-password-container');
        if (closeForgotBtn && forgotPasswordContainer) {
            closeForgotBtn.addEventListener('click', () => {
                window.location.href = 'index.php'; // Redirect to index page
            });
        }
    </script>
</body>
</html>
<?php ob_end_flush(); // Flush the output buffer ?>