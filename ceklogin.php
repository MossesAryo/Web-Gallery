<?php
include "koneksi.php";
$Username = $_POST['Username'];
$Password = md5($_POST['Password']);
$query = mysqli_query($con, "SELECT * FROM user WHERE Username='$Username' AND Password='$Password'");
$hasilquery = mysqli_num_rows($query);

if ($hasilquery == 1) {
    session_start();
    while ($row = mysqli_fetch_assoc($query)) {
        $_SESSION['Username'] = $row['Username'];
        header("Location: dashboard.php");
    }
} else {
    header("Location: login.php?error=user_not_found");
}

?>