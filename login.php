<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gammaz Gallery</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <i class="fas fa-camera-retro text-4xl text-blue-400 mb-4"></i>
            <h1 class="text-2xl font-bold text-white">Welcome to Gammaz Gallery</h1>
            <p class="text-gray-400">Please sign in to continue</p>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'user_not_found'): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-2 rounded mb-4">
                Invalid username or password
            </div>
        <?php endif; ?>

        <form action="ceklogin.php" method="POST">
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-400 mb-2">Username</label>
                    <input type="text" name="Username" required
                           class="w-full bg-gray-700 rounded px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                
                <div>
                    <label class="block text-gray-400 mb-2">Password</label>
                    <input type="password" name="Password" required
                           class="w-full bg-gray-700 rounded px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <button type="submit" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded transition duration-200">
                    Sign In
                </button>
            </div>
        </form>

        <div class="mt-6 text-center text-gray-400">
            Don't have an account? 
            <a href="register.php" class="text-blue-400 hover:underline">Register here</a>
        </div>
    </div>
</body>
</html>