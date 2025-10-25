<?php
// Configuration for Database Connection
$servername = "localhost";
$username = "root";       // XAMPP default MySQL username
$password = "";           // XAMPP default MySQL password (usually blank)
$dbname = "lms";          // The schema you created

// Constants from LMS inserts.sql
const BORROWER_ROLE_ID = 1; // 'borrower' role_id is 1
const ACTIVE_STATUS_ID = 1; // 'active' status_id is 1

$message = '';
$message_type = '';
$input_username = '';
$input_first_name = '';
$input_last_name = '';
$input_contact_no = '';
$input_email = '';
$input_address = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Get and sanitize user input
    $input_username = trim($_POST['username'] ?? '');
    $input_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $input_first_name = trim($_POST['first_name'] ?? '');
    $input_last_name = trim($_POST['last_name'] ?? '');
    $input_contact_no = trim($_POST['contact_no'] ?? '');
    $input_email = trim($_POST['email'] ?? '');
    $input_address = trim($_POST['address'] ?? '');

    // Basic Validation
    if (empty($input_username) || empty($input_password) || empty($confirm_password) || 
        empty($input_first_name) || empty($input_last_name) || empty($input_email)) {
        
        $message = "Please fill in all required fields (Username, Password, Name, and Email).";
        $message_type = 'error';
    } 
    // Password Match Check
    else if ($input_password !== $confirm_password) {
        $message = "Passwords do not match. Please try again.";
        $message_type = 'error';
    } 
    else {
        // 2. Establish database connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // START TRANSACTION: Ensure both inserts succeed or both fail
        $conn->begin_transaction();
        
        try {
            // 3. Check if username already exists
            $check_sql = "SELECT user_id FROM Login_Info WHERE username = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $input_username);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                throw new Exception("The username '{$input_username}' is already taken. Please choose another.");
            }

            // --- STEP A: Insert into Login_Info ---
            $login_sql = "INSERT INTO Login_Info (username, password_hash, role_id, status_id) VALUES (?, ?, ?, ?)";
            $login_stmt = $conn->prepare($login_sql);
            
            // Assign 'borrower' role (1) and 'active' status (1)
            $login_stmt->bind_param("ssii", 
                                     $input_username, 
                                     $input_password, 
                                     BORROWER_ROLE_ID, 
                                     ACTIVE_STATUS_ID);

            if (!$login_stmt->execute()) {
                throw new Exception("Failed to create login account: " . $login_stmt->error);
            }
            
            // Get the ID of the new user to use in the Borrower table
            $new_user_id = $conn->insert_id;

            // --- STEP B: Insert into Borrower Table ---
            $borrower_sql = "INSERT INTO Borrower (user_id, first_name, last_name, contact_no, email, address) VALUES (?, ?, ?, ?, ?, ?)";
            $borrower_stmt = $conn->prepare($borrower_sql);
            
            $borrower_stmt->bind_param("isssss", 
                                        $new_user_id, 
                                        $input_first_name, 
                                        $input_last_name, 
                                        $input_contact_no, 
                                        $input_email, 
                                        $input_address);

            if (!$borrower_stmt->execute()) {
                throw new Exception("Failed to create borrower profile: " . $borrower_stmt->error);
            }

            // If both statements succeed, commit the transaction
            $conn->commit();
            $message = "Registration successful! Your borrower account is now active. Please <a href='login.html'>Login</a>.";
            $message_type = 'success';
            
            // Clear fields on success
            $input_username = $input_first_name = $input_last_name = $input_contact_no = $input_email = $input_address = '';

        } catch (Exception $e) {
            // If any error occurs, rollback the transaction
            $conn->rollback();
            $message = "Registration failed: " . $e->getMessage();
            $message_type = 'error';
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Registration</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* This will override conflicting styles from style.css
           if they have the same or lower specificity, because 
           internal styles load later. */
        .login-container {
            
        }

    </style>
</head>
<body>
    <div class="login-container">
        <h2>Borrower Registration</h2>
        
        <?php 
        // Display the status message if set
        if (!empty($message)) {
            echo "<div class='message {$message_type}'>{$message}</div>";
        }
        
        // Only show the form if registration wasn't successful
        if ($message_type !== 'success') :
        ?>
            <form action="registration.php" method="POST">
                
                <h3>Personal Details</h3>
                <div class="form-group">
                    <label for="first_name">First Name (Required):</label>
                    <input type="text" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($input_first_name); ?>">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name (Required):</label>
                    <input type="text" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($input_last_name); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email (Required):</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($input_email); ?>">
                </div>
                <div class="form-group">
                    <label for="contact_no">Contact No.:</label>
                    <input type="text" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($input_contact_no); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($input_address); ?>">
                </div>    


                <h3>Login Details</h3>
                <div class="form-group">
                    <label for="username">Username (Required):</label>
                    <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($input_username); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password (Required):</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password (Required):</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn-login">Complete Registration</button>
            </form>
        <?php endif; ?>
        
        <p style="margin-top: 15px;"><a href="login.html">Already registered? Login here.</a></p>
    </div>
</body>
</html>