<?php
session_start();
include "koneksi.php";

// Process registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($con, $_POST['Username']);
    $password = md5($_POST['Password']);
    $email = mysqli_real_escape_string($con, $_POST['Email']);
    $namaLengkap = mysqli_real_escape_string($con, $_POST['NamaLengkap']);
    $alamat = mysqli_real_escape_string($con, $_POST['Alamat']);

    // Check if username already exists
    $check_query = mysqli_query($con, "SELECT * FROM user WHERE Username='$username'");
    if (mysqli_num_rows($check_query) > 0) {
        header("Location: register.php?error=username_exists");
        exit();
    }

    // Insert new user
    $query = "INSERT INTO user (Username, Password, Email, NamaLengkap, Alamat, role) 
              VALUES ('$username', '$password', '$email', '$namaLengkap', '$alamat', 'user')";
    
    if (mysqli_query($con, $query)) {
        header("Location: login.php?success=registered");
        exit();
    } else {
        header("Location: register.php?error=registration_failed");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Gammaz Gallery</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center py-8">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md mx-4">
        <div class="text-center mb-8">
            <i class="fas fa-camera-retro text-4xl text-blue-400 mb-4"></i>
            <h1 class="text-2xl font-bold text-white">Create Your Account</h1>
            <p class="text-gray-400">Join Gammaz Gallery community</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-2 rounded mb-4">
                <?php 
                    switch($_GET['error']) {
                        case 'username_exists':
                            echo 'Username already taken';
                            break;
                        case 'registration_failed':
                            echo 'Registration failed. Please try again';
                            break;
                        default:
                            echo 'An error occurred';
                    }
                ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <!-- Username -->
            <div>
                <label class="block text-gray-400 mb-2">Username*</label>
                <input type="text" name="Username" required
                       class="w-full bg-gray-700 rounded px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="Choose a username">
            </div>
            
            <!-- Password -->
            <div>
                <label class="block text-gray-400 mb-2">Password*</label>
                <input type="password" name="Password" required
                       class="w-full bg-gray-700 rounded px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="Create a password">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-gray-400 mb-2">Email*</label>
                <input type="email" name="Email" required
                       class="w-full bg-gray-700 rounded px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="Enter your email">
            </div>

            <!-- Full Name -->
            <div>
                <label class="block text-gray-400 mb-2">Full Name*</label>
                <input type="text" name="NamaLengkap" required
                       class="w-full bg-gray-700 rounded px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="Enter your full name">
            </div>

            <!-- Address -->
            <div>
                <label class="block text-gray-400 mb-2">Address</label>
                <textarea name="Alamat" rows="2"
                          class="w-full bg-gray-700 rounded px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400"
                          placeholder="Enter your address"></textarea>
            </div>

            <button type="submit" 
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded transition duration-200">
                Create Account
            </button>
        </form>

        <div class="mt-6 text-center text-gray-400">
            Already have an account? 
            <a href="login.php" class="text-blue-400 hover:underline">Sign in here</a>
        </div>

        <div class="mt-4 text-center text-gray-500 text-sm">
            By registering, you agree to our Terms of Service and Privacy Policy
        </div>
    </div>

    <script>
        // Simple form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="Password"]').value;
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long');
            }
        });
    </script>
</body>
</html>