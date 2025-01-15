<?php
session_start();
include 'koneksi.php';  

if (isset($_GET['id'])) {
    $fotoID = $_GET['id'];

    $query = "SELECT f.FotoID, f.JudulFoto, f.DeskripsiFoto, f.TanggalUnggah, f.LokasiFoto, a.NamaAlbum, f.AlbumID
              FROM foto f
              INNER JOIN album a ON f.AlbumID = a.AlbumID
              WHERE f.FotoID = '$fotoID'";

    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $currentAlbumID = $row['AlbumID'];
    } else {
        die("Foto tidak ditemukan.");
    }
} else {
    die("ID foto tidak ditemukan.");
}

if (isset($_POST['submit'])) {
    $judulFoto = $_POST['JudulFoto'];
    $deskripsi = $_POST['Deskripsi'];
    $tanggalUnggah = $_POST['TanggalUnggah'];
    $album = $_POST['Album'];

    $fileName = $_FILES['UploadFoto']['name'];
    $fileTmpName = $_FILES['UploadFoto']['tmp_name'];
    $fileError = $_FILES['UploadFoto']['error'];

    $queryAlbum = "SELECT AlbumID FROM album WHERE LOWER(NamaAlbum) = LOWER('$album')";
    $resultAlbum = mysqli_query($con, $queryAlbum);
    $albumID = null;
    if (mysqli_num_rows($resultAlbum) > 0) {
        $rowAlbum = mysqli_fetch_assoc($resultAlbum);
        $albumID = $rowAlbum['AlbumID'];
    }

    if ($fileError === 0) {
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedExts)) {
            $newFileName = uniqid('', true) . '.' . $fileExt;
            $uploadDir = 'uploads/';
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpName, $uploadPath)) {
                $queryUpdate = "UPDATE foto SET 
                                JudulFoto = '$judulFoto', 
                                DeskripsiFoto = '$deskripsi', 
                                TanggalUnggah = '$tanggalUnggah', 
                                LokasiFoto = '$newFileName', 
                                AlbumID = '$albumID' 
                                WHERE FotoID = '$fotoID'";

                if (mysqli_query($con, $queryUpdate)) {
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo "Gagal memperbarui data foto: " . mysqli_error($con);
                }
            } else {
                echo "Gagal mengunggah foto. Coba lagi.";
            }
        } else {
            echo "Ekstensi file tidak valid!";
        }
    } else {
        $queryUpdate = "UPDATE foto SET 
                        JudulFoto = '$judulFoto', 
                        DeskripsiFoto = '$deskripsi', 
                        TanggalUnggah = '$tanggalUnggah', 
                        AlbumID = '$albumID' 
                        WHERE FotoID = '$fotoID'";

        if (mysqli_query($con, $queryUpdate)) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Gagal memperbarui data foto: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Foto</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        .grid-mask {
            background-image: radial-gradient(#4a5568 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen grid-mask">
    <!-- Navigation -->
    <nav class="bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-camera-retro text-2xl text-blue-400"></i>
                    <span class="text-xl font-bold">Gammaz</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span><?php echo $_SESSION['Username']; ?></span>
                        <a href="logout.php" class="text-red-400 hover:text-red-300">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Edit Form -->
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <h2 class="text-2xl font-bold mb-6 flex items-center">
                <i class="fas fa-edit mr-3 text-blue-400"></i>
                Edit Foto
            </h2>
            <form action="edit.php?id=<?php echo $fotoID; ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="JudulFoto" class="block text-gray-400 mb-2">Judul Foto:</label>
                    <input type="text" name="JudulFoto" value="<?php echo $row['JudulFoto']; ?>" class="w-full bg-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div>
                    <label for="Deskripsi" class="block text-gray-400 mb-2">Deskripsi Foto:</label>
                    <textarea name="Deskripsi" rows="3" class="w-full bg-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required><?php echo $row['DeskripsiFoto']; ?></textarea>
                </div>

                <div>
                    <label for="TanggalUnggah" class="block text-gray-400 mb-2">Tanggal Unggah:</label>
                    <input type="date" name="TanggalUnggah" value="<?php echo $row['TanggalUnggah']; ?>" class="w-full bg-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div>
                    <label for="UploadFoto" class="block text-gray-400 mb-2">Upload Foto Baru (Optional):</label>
                    <input type="file" name="UploadFoto" accept="image/*" class="w-full bg-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div>
                    <label for="Album" class="block text-gray-400 mb-2">Album:</label>
                    <select name="Album" class="w-full bg-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                        <?php
                        $queryAlbums = "SELECT AlbumID, NamaAlbum FROM album";
                        $resultAlbums = mysqli_query($con, $queryAlbums);

                        if (mysqli_num_rows($resultAlbums) > 0) {
                            while ($album = mysqli_fetch_assoc($resultAlbums)) {
                                $selected = ($album['AlbumID'] == $currentAlbumID) ? 'selected' : '';
                                echo "<option value='" . $album['NamaAlbum'] . "' $selected>" . $album['NamaAlbum'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>Tidak ada album</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <button type="submit" name="submit" class="w-full bg-blue-500 hover:bg-blue-600 py-3 rounded-lg flex items-center justify-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>

        <br>
        <div class="text-center">
            <a href="dashboard.php" class="text-blue-400 hover:underline">Kembali untuk Batal Mengedit</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
