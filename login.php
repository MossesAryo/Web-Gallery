<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <!-- Link Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #6a11cb, #2575fc);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    .btn-primary {
      background-color: #6a11cb;
      border: none;
    }
    .btn-primary:hover {
      background-color: #5a0bb5;
    }
    .text-primary {
      color: #6a11cb !important;
    }
    .form-label {
      font-weight: bold;
    }
    .form-control {
      border-radius: 8px;
    }
    .alert {
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center align-items-center">
      <div class="col-md-6 col-lg-4">
        <div class="card p-4">
          <div class="card-body">
            <h2 class="text-center mb-4 text-primary">Login</h2>

            <!-- Pesan error jika ada -->
            <?php if (isset($_GET['error'])): ?>
              <div class="alert alert-danger text-center">
                <?php 
                  if ($_GET['error'] == 'wrong_password') {
                    echo 'Password salah. Silakan coba lagi.';
                  } elseif ($_GET['error'] == 'user_not_found') {
                    echo 'Username tidak ditemukan.';
                  }
                ?>
              </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form action="ceklogin.php" method="POST">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="Username" placeholder="Masukkan username Anda" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="Password" placeholder="Masukkan password Anda" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <!-- Link Registrasi -->
            <div class="text-center mt-3">
              <p>Belum punya akun? <a href="register.php" class="text-decoration-none text-primary">Daftar sekarang!</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Link Bootstrap Bundle with Popper -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
