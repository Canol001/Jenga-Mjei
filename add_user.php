<?php
$host = "localhost";
$user = "radius";
$pass = "Somestrongpassword_321";
$dbname = "radius";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Auto-delete expired users (based on radreply Expiration)
$conn->query("DELETE rc FROM radcheck rc 
              JOIN radreply rr ON rc.username = rr.username 
              WHERE rr.attribute = 'Expiration' 
              AND NOW() > rr.value");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $validity = trim($_POST['validity']); // e.g., "1h", "2d", "30m"

    // Default password = username
    if (empty($password)) $password = $username;

    // Calculate expiry time from validity input
    $expireTime = date("Y-m-d H:i:s", strtotime("+$validity"));

    // Insert user password into radcheck
    $stmt = $conn->prepare("INSERT INTO radcheck (username, attribute, op, value, valid_for) VALUES (?, 'Cleartext-Password', ':=', ?, ?)");
    $stmt->bind_param("sss", $username, $password, $validity);
    $stmt->execute();

    // Insert expiration time into radreply
    $stmt2 = $conn->prepare("INSERT INTO radreply (username, attribute, op, value) VALUES (?, 'Session-Timeout', ':=', ?)");
    $stmt2->bind_param("ss", $username, $validity);
    $stmt2->execute();

    echo "<p style='color:green;'>✅ User <b>$username</b> added successfully!<br>Expires on: <b>$expireTime</b></p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add RADIUS User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
<div class="bg-white p-8 rounded-2xl shadow-xl w-96">
    <h1 class="text-2xl font-bold mb-4 text-center">Add RADIUS User</h1>
    <form method="POST" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">Username</label>
            <input type="text" name="username" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block mb-1 font-medium">Password (optional)</label>
            <input type="text" name="password" class="w-full border rounded-lg px-3 py-2" placeholder="Leave blank = username">
        </div>
        <div>
            <label class="block mb-1 font-medium">Validity Period</label>
            <input type="text" name="validity" placeholder="e.g. 1h, 2d, 30m" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Add User</button>
    </form>
</div>
</body>
</html>
