<?php
// Configuration for Database Connection
$servername = "localhost";
$username = "root";       // XAMPP default MySQL username
$password = "";           // XAMPP default MySQL password (usually blank)
$dbname = "lms";          // The schema you created

// 1. Get user input from the form
$input_username = $_POST['username'] ?? '';
$input_password = $_POST['password'] ?? '';

// Basic sanitization (security: use prepared statements to prevent SQL injection)
$sanitized_username = trim($input_username);

// 2. Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. Prepare and execute the SQL query
// NOTE: For prototyping, we are checking the plain-text input_password against 
// the password_hash column, as requested. In a real system, you'd use password_verify().
$sql = "SELECT user_id, username, password_hash, role_id FROM Login_Info WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $sanitized_username);
$stmt->execute();
$result = $stmt->get_result();

$message = '';

if ($result->num_rows === 1) {
    // User found
    $row = $result->fetch_assoc();
    $db_password = $row['password_hash'];
    $user_role_id = $row['role_id'];

    // 4. Compare Passwords (Plain Text Check)
    if ($input_password === $db_password) {
        // Successful Login
        
        // Retrieve the role name for the success message (optional but helpful)
        $role_sql = "SELECT role_name FROM Role_Type WHERE role_id = ?";
        $role_stmt = $conn->prepare($role_sql);
        $role_stmt->bind_param("i", $user_role_id);
        $role_stmt->execute();
        $role_result = $role_stmt->get_result();
        $role_name = $role_result->fetch_assoc()['role_name'] ?? 'Unknown Role';
        
        $message = "<div class='message success'>Login Successful! Welcome, " . htmlspecialchars($row['username']) . " (" . $role_name . ").</div>";
        // In a real application, you would start a session here:
        // session_start();
        // $_SESSION['user_id'] = $row['user_id'];
        // header("Location: dashboard.php"); // Redirect to dashboard
    } else {
        // Password Mismatch
        $message = "<div class='message error'>Invalid password.</div>";
    }
} else {
    // User not found
    $message = "<div class='message error'>Invalid username or password.</div>";
}

$conn->close();

// Display a page with the message and a link back to login (since we are not redirecting)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Authentication Result</h2>
        <?php echo $message; ?>
        <p><a href="login.html">Go back to login</a></p>
    </div>
</body>
</html>