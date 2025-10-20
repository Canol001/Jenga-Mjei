<?php
ob_start();
session_start();
include 'config.php';

// Redirect if already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: homepage.php");
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['name'] ?? 'User'; // optional name field
            header("Location: homepage.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Jenga-Mjei Management</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="min-h-screen bg-gray-50 flex flex-col items-center justify-center relative">

  <!-- Close Button (Top Right of Page) -->
  <button id="close-login" 
    class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 transition-colors p-2 rounded-full hover:bg-gray-100">
    <i data-lucide="x" class="w-6 h-6"></i>
  </button>

  <!-- Login Form -->
  <div class="w-full max-w-sm p-8 bg-transparent">
    <h2 class="text-3xl font-semibold text-gray-800 text-center mb-6">Sign In</h2>
    
    <?php if (isset($error)): ?>
      <p class="text-red-500 text-center mb-4"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
        <input type="email" id="email" name="email" placeholder="you@example.com"
          class="w-full p-2.5 border border-gray-300 rounded-lg focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition duration-150" 
          required>
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password"
          class="w-full p-2.5 border border-gray-300 rounded-lg focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition duration-150" 
          required>
      </div>

      <button type="submit" name="login"
        class="w-full bg-blue-600 text-white py-2.5 rounded-lg hover:bg-blue-700 transition-colors">
        Login
      </button>
    </form>

    <!-- <p class="mt-6 text-center text-sm text-gray-600">
      <a href="forgot_password.php" class="text-blue-600 hover:text-blue-800" disabled>Forgot Password?</a>
    </p> -->
  </div>

  <script>
    lucide.createIcons();

    const closeLoginBtn = document.getElementById('close-login');
    if (closeLoginBtn) {
      closeLoginBtn.addEventListener('click', () => {
        window.location.href = 'homepage.php';
      });
    }
  </script>
</body>
</html>
<?php ob_end_flush(); ?>
