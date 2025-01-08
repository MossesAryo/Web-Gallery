<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $fotoID = $_GET['id'];

    $sql = "SELECT LokasiFoto FROM foto WHERE FotoID = '$fotoID'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $fotoFile = $row['LokasiFoto'];

        if (file_exists('uploads/' . $fotoFile)) {
            unlink('uploads/' . $fotoFile);
        }

        $query = "DELETE FROM foto WHERE FotoID = '$fotoID'";

        if (mysqli_query($con, $query)) {
            echo "Foto berhasil dihapus!";
            header('Location: dashboard.php');
            exit();
        } else {
            echo "Gagal menghapus data foto. Error: " . mysqli_error($con);
        }
    } else {
        echo "Foto tidak ditemukan!";
    }
} else {
    echo "ID foto tidak ditemukan!";
}
?>
