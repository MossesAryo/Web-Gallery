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
  
</head>
<body>
    <h2>Edit Foto</h2>

    <form action="edit.php?id=<?php echo $fotoID; ?>" method="post" enctype="multipart/form-data">
        <label for="JudulFoto">Judul Foto:</label>
        <input type="text" name="JudulFoto" value="<?php echo $row['JudulFoto']; ?>" required><br>

        <label for="Deskripsi">Deskripsi Foto:</label>
        <textarea name="Deskripsi" required><?php echo $row['DeskripsiFoto']; ?></textarea><br>

        <label for="TanggalUnggah">Tanggal Unggah:</label>
        <input type="date" name="TanggalUnggah" value="<?php echo $row['TanggalUnggah']; ?>" required><br>

        <label for="UploadFoto">Upload Foto Baru (Optional):</label>
        <input type="file" name="UploadFoto" accept="image/*"><br>

        <label for="Album">Album:</label>
        <select name="Album" required>
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
        </select><br>

        <button type="submit" name="submit">Simpan Perubahan</button>
    </form>

    <br>
    <a href="dashboard.php">Kembali untuk Batal Mengedit</a>
</body>
</html>
