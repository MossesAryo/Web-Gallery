<?php
session_start();
include 'koneksi.php';

if (isset($_POST['Submit'])) {
    $username = $_POST['Username'];
    $password = $_POST['Password'];
    $email = $_POST['Email'];
    $namaLengkap = $_POST['NamaLengkap'];
    $alamat = $_POST['Alamat'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $queryCheckUsername = "SELECT * FROM user WHERE Username = '$username'";
    $result = mysqli_query($con, $queryCheckUsername);
    
    if (mysqli_num_rows($result) > 0) {
        $error = "Username sudah terdaftar!";
    } else {
        $query = "INSERT INTO user (Username, Password, Email, NamaLengkap, Alamat) 
                  VALUES ('$username', '$hashedPassword', '$email', '$namaLengkap', '$alamat')";

        if (mysqli_query($con, $query)) {
            header('Location: login.php');
            exit();
        } else {
            $error = "Terjadi kesalahan dalam pendaftaran: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/style.css">
   
</head>
<body>

    <h2>Register</h2>

    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <form action="register.php" method="POST">
        <label for="NamaLengkap">Nama Lengkap: </label>
        <input type="text" name="NamaLengkap" required> <br><br>

        <label for="Username">Username: </label>
        <input type="text" name="Username" required> <br><br>

        <label for="Password">Password: </label>
        <input type="password" name="Password" required> <br><br>

        <label for="Email">Email: </label>
        <input type="email" name="Email" required> <br><br>

        <label for="Alamat">Alamat: </label>
        <textarea name="Alamat" required></textarea><br><br>

        <button type="submit" name="Submit">Submit</button>
    </form>
    <br>
    <a href="login.php">Sudah punya akun? Login di sini!</a>
        
</body>
</html>
