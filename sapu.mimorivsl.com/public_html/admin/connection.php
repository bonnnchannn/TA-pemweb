<?php
// Database connection configuration
$db_host = "localhost";
$db_user = "root";
$db_pass = "ameng";
$db_name = "datacenter_db";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Function to log access attempts
function log_access($username, $action, $status, $details = "") {
    global $conn;
    
    $username = sanitize_input($username);
    $action = sanitize_input($action);
    $status = sanitize_input($status);
    $details = sanitize_input($details);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Get user_id if available
    $user_id = "NULL";
    if ($status == "success") {
        $query = "SELECT user_id FROM users WHERE username = '$username'";
        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];
        }
    }
    
    $query = "INSERT INTO access_logs (user_id, username, ip_address, action, status, details) 
              VALUES ($user_id, '$username', '$ip_address', '$action', '$status', '$details')";
    
    $conn->query($query);
}

// Function to log user activities
function log_activity($user_id, $activity_type, $description = "") {
    global $conn;
    
    $user_id = (int)$user_id;
    $activity_type = sanitize_input($activity_type);
    $description = sanitize_input($description);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $query = "INSERT INTO user_activities (user_id, activity_type, description, ip_address) 
              VALUES ($user_id, '$activity_type', '$description', '$ip_address')";
    
    $conn->query($query);
}

// Function to get system settings
function get_setting($setting_name) {
    global $conn;
    
    $setting_name = sanitize_input($setting_name);
    $query = "SELECT setting_value FROM system_settings WHERE setting_name = '$setting_name'";
    
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['setting_value'];
    }
    
    return null;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>