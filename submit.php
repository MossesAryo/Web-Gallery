<?php
include 'koneksi.php';  

$sql = "SELECT f.FotoID, f.JudulFoto, f.DeskripsiFoto, f.TanggalUnggah, a.NamaAlbum, u.NamaLengkap, COUNT(k.KomentarID) AS JumlahKomentar, COUNT(l.LikeID) AS JumlahLike
        FROM foto f
        INNER JOIN album a ON f.AlbumID = a.AlbumID
        INNER JOIN user u ON f.UserID = u.UserID
        LEFT JOIN komentarfoto k ON f.FotoID = k.FotoID
        LEFT JOIN likefoto l ON f.FotoID = l.FotoID
        GROUP BY f.FotoID";
$result = $con->query($sql);  

?>

<!DOCTYPE html>
<html>
<head>
    <title>Tabel Foto</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Judul Foto</th>
                <th>Deskripsi</th>
                <th>Tanggal Unggah</th>
                <th>Album</th>
                <th>Diunggah oleh</th>
                <th>Jumlah Komentar</th>
                <th>Jumlah Like</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = 1;
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td><img src='path/to/images/" . $row["FotoID"] . ".jpg' width='100'></td>"; // Sesuaikan path gambar
                    echo "<td>" . $row["JudulFoto"] . "</td>";
                    echo "<td>" . $row["DeskripsiFoto"] . "</td>";
                    echo "<td>" . $row["TanggalUnggah"] . "</td>";
                    echo "<td>" . $row["NamaAlbum"] . "</td>";
                    echo "<td>" . $row["NamaLengkap"] . "</td>";
                    echo "<td>" . $row["JumlahKomentar"] . "</td>";
                    echo "<td>" . $row["JumlahLike"] . "</td>";
                    echo "<td><a href='edit.php?id=" . $row["FotoID"] . "'>Edit</a> | <a href='delete.php?id=" . $row["FotoID"] . "'>Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>Tidak ada data foto.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
