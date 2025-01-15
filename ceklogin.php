<?php
include "koneksi.php";

$Username = $_POST['Username'];
$Password = md5($_POST['Password']);

// Modified query to fetch role information
$query = mysqli_query($con, "SELECT * FROM user WHERE Username='$Username' AND Password='$Password'");
$hasilquery = mysqli_num_rows($query);

if ($hasilquery == 1) {
    session_start();
    $row = mysqli_fetch_assoc($query);
    
    // Store user data in session
    $_SESSION['Username'] = $row['Username'];
    $_SESSION['UserID'] = $row['UserID'];
    $_SESSION['role'] = $row['role']; // Make sure you have Role column in your user table
    
    // Redirect based on role
    if ($row['role'] == 'admin') {
        header("Location: dashboard.php"); // Admin dashboard
        exit();
    } else {
        header("Location: DashboardUsers.php"); // User dashboard
        exit();
    }
} else {
    header("Location: login.php?error=user_not_found");
    exit();
}
?>