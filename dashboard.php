<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['Username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['Username'];
$queryUserID = "SELECT UserID FROM user WHERE Username = '$username'";
$resultUserID = mysqli_query($con, $queryUserID);
$rowUserID = mysqli_fetch_assoc($resultUserID);
$userID = $rowUserID['UserID'];

if (isset($_POST['submit'])) {
    $judulFoto = $_POST['JudulFoto'];
    $deskripsi = $_POST['Deskripsi'];
    $tanggalUnggah = $_POST['TanggalUnggah'];
    $album = $_POST['Album'];

    $fileName = $_FILES['UploadFoto']['name'];
    $fileTmpName = $_FILES['UploadFoto']['tmp_name'];
    $fileSize = $_FILES['UploadFoto']['size'];
    $fileError = $_FILES['UploadFoto']['error'];
    $fileType = $_FILES['UploadFoto']['type'];

    if ($fileError === 0) {
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedExts)) {
            $newFileName = uniqid('', true) . '.' . $fileExt;
            $uploadDir = 'uploads/';
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpName, $uploadPath)) {
                $queryAlbum = "SELECT AlbumID FROM album WHERE LOWER(NamaAlbum) = LOWER('$album')";
                $resultAlbum = mysqli_query($con, $queryAlbum);
                $albumID = null;
                if (mysqli_num_rows($resultAlbum) > 0) {
                    $rowAlbum = mysqli_fetch_assoc($resultAlbum);
                    $albumID = $rowAlbum['AlbumID'];
                }

                if ($albumID) {
                    $query = "INSERT INTO foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFoto, AlbumID, UserID) 
                              VALUES ('$judulFoto', '$deskripsi', '$tanggalUnggah', '$newFileName', '$albumID', '$userID')";

                    if (mysqli_query($con, $query)) {
                        echo "berhasil";
                    } else {
                        echo "" . mysqli_error($con);
                    }
                } else {
                    echo "";
                }
            } else {
                echo "";
            }
        } else {
            echo "";
        }
    } else {
        echo "";
    }



    
}

$sql = "SELECT f.FotoID, f.JudulFoto, f.DeskripsiFoto, f.TanggalUnggah, a.NamaAlbum, u.NamaLengkap, f.LokasiFoto, COUNT(k.KomentarID) AS JumlahKomentar, COUNT(l.LikeID) AS JumlahLike
        FROM foto f
        INNER JOIN album a ON f.AlbumID = a.AlbumID
        INNER JOIN user u ON f.UserID = u.UserID
        LEFT JOIN komentarfoto k ON f.FotoID = k.FotoID
        LEFT JOIN likefoto l ON f.FotoID = l.FotoID
        GROUP BY f.FotoID";

$result = $con->query($sql);

$albumQuery = "SELECT * FROM album";
$albumResult = mysqli_query($con, $albumQuery);
$statsQuery = "SELECT 
(SELECT COUNT(*) FROM foto) as total_photos,
(SELECT COUNT(*) FROM album) as total_albums,
(SELECT COUNT(*) FROM likefoto) as total_likes,
(SELECT COUNT(*) FROM komentarfoto) as total_comments";

$statsResult = mysqli_query($con, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .photo-card {
            transition: transform 0.2s;
        }

        .photo-card:hover {
            transform: translateY(-5px);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #2d3748;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4a5568;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }

        .modal {
            transition: opacity 0.3s ease-in-out;
        }

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
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400">Total Photos</p>
                        <h3 class="text-2xl font-bold"><?php echo number_format($stats['total_photos']); ?></h3>
                    </div>
                    <i class="fas fa-images text-blue-400 text-3xl"></i>
                </div>
            </div>
            <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400">Total Albums</p>
                        <h3 class="text-2xl font-bold"><?php echo number_format($stats['total_albums']); ?></h3>
                    </div>
                    <i class="fas fa-folder text-yellow-400 text-3xl"></i>
                </div>
            </div>
            <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400">Total Likes</p>
                        <h3 class="text-2xl font-bold"><?php echo number_format($stats['total_likes']); ?></h3>
                    </div>
                    <i class="fas fa-heart text-red-400 text-3xl"></i>
                </div>
            </div>
            <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400">Comments</p>
                        <h3 class="text-2xl font-bold"><?php echo number_format($stats['total_comments']); ?></h3>
                    </div>
                    <i class="fas fa-comments text-green-400 text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg mb-8">
            <h2 class="text-2xl font-bold mb-6 flex items-center">
                <i class="fas fa-cloud-upload-alt mr-3 text-blue-400"></i>
                Upload New Photo
            </h2>
            <form action="dashboard.php" method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 mb-2">Photo Title</label>
                        <input type="text" name="JudulFoto" class="w-full bg-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Description</label>
                        <textarea name="Deskripsi" rows="3" class="w-full bg-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required></textarea>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 mb-2">Upload Date</label>
                        <input type="date" name="TanggalUnggah" class="w-full bg-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Album</label>
                        <select name="Album" class="w-full bg-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="">Select Album</option>
                            <?php
                            while ($albumRow = mysqli_fetch_assoc($albumResult)) {
                                echo "<option value='" . $albumRow['NamaAlbum'] . "'>" . $albumRow['NamaAlbum'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Photo File</label>
                        <div class="relative">
                            <input type="file" name="UploadFoto" accept="image/*" class="w-full bg-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <i class="fas fa-image absolute right-4 top-3 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" name="submit" class="w-full bg-blue-500 hover:bg-blue-600 py-3 rounded-lg flex items-center justify-center space-x-2">
                        <i class="fas fa-upload"></i>
                        <span>Upload Photo</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Photo Gallery -->
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <h2 class="text-2xl font-bold mb-6 flex items-center">
                <i class="fas fa-images mr-3 text-blue-400"></i>
                Photo Gallery
            </h2>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Preview</th>
                            <th class="px-4 py-3 text-left">Title</th>
                            <th class="px-4 py-3 text-left">Description</th>
                            <th class="px-4 py-3 text-left">Upload Date</th>
                            <th class="px-4 py-3 text-left">Album</th>
                            <th class="px-4 py-3 text-left">Stats</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr class='hover:bg-gray-700'>";
                                echo "<td class='px-4 py-3'>" . $no++ . "</td>";
                                echo "<td class='px-4 py-3'><img src='uploads/" . $row["LokasiFoto"] . "' class='w-16 h-16 object-cover rounded-lg'></td>";
                                echo "<td class='px-4 py-3'>" . $row["JudulFoto"] . "</td>";
                                echo "<td class='px-4 py-3 max-w-xs truncate'>" . $row["DeskripsiFoto"] . "</td>";
                                echo "<td class='px-4 py-3'>" . $row["TanggalUnggah"] . "</td>";
                                echo "<td class='px-4 py-3'><span class='bg-blue-500 px-2 py-1 rounded-full text-sm'>" . $row["NamaAlbum"] . "</span></td>";
                                echo "<td class='px-4 py-3'>
                                        <div class='flex items-center space-x-4'>
                                            <span class='flex items-center'><i class='fas fa-heart text-red-400 mr-1'></i>" . $row["JumlahLike"] . "</span>
                                            <span class='flex items-center'><i class='fas fa-comment text-green-400 mr-1'></i>" . $row["JumlahKomentar"] . "</span>
                                        </div>
                                     </td>";
                                echo "<td class='px-4 py-3'>
                                        <div class='flex space-x-2'>
                                            <a href='edit.php?id=" . $row['FotoID'] . "' class='bg-yellow-500 hover:bg-yellow-600 p-2 rounded-lg' title='Edit'>
                                             <i class='fas fa-edit'></i>
                                            </a>
                                           <a href='delete.php?id=" . $row['FotoID'] . "' class='bg-red-500 hover:bg-yellow-600 p-2 rounded-lg' title='Edit'>
                                             <i class='fas fa-trash-alt'></i>
                                             </a>
                                        </div>
                                     </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='px-4 py-3 text-center'>No photos found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>