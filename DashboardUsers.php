<?php 
session_start();
include 'koneksi.php';

if (!isset($_SESSION['Username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['Username'];

// Get all photos with user details, likes, and comments
$sql = "SELECT 
    f.FotoID,
    f.JudulFoto,
    f.DeskripsiFoto,
    f.TanggalUnggah,
    f.LokasiFoto,
    u.Username,
    u.NamaLengkap,
    a.NamaAlbum,
    COUNT(DISTINCT l.LikeID) as JumlahLike,
    COUNT(DISTINCT k.KomentarID) as JumlahKomentar,
    EXISTS(SELECT 1 FROM likefoto l2 WHERE l2.FotoID = f.FotoID AND l2.UserID = (SELECT UserID FROM user WHERE Username = '$username')) as UserLiked
FROM foto f
INNER JOIN user u ON f.UserID = u.UserID
INNER JOIN album a ON f.AlbumID = a.AlbumID
LEFT JOIN likefoto l ON f.FotoID = l.FotoID
LEFT JOIN komentarfoto k ON f.FotoID = k.FotoID
GROUP BY f.FotoID
ORDER BY f.TanggalUnggah DESC";

$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .photo-card {
            transition: transform 0.3s ease;
        }
        
        .photo-card:hover {
            transform: translateY(-5px);
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
                    <span class="text-xl font-bold">Gammaz Gallery</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-300"><?php echo $_SESSION['Username']; ?></span>
                    <a href="logout.php" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Photo Gallery Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden photo-card">
                    <!-- Photo -->
                    <div class="relative group">
                        <img src="uploads/<?php echo $row['LokasiFoto']; ?>" alt="<?php echo $row['JudulFoto']; ?>" 
                             class="w-full h-64 object-cover">
                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                            <div class="flex justify-between items-center">
                                <span class="text-sm">
                                    <i class="fas fa-user mr-2"></i><?php echo $row['Username']; ?>
                                </span>
                                <span class="bg-blue-500 px-2 py-1 rounded-full text-xs">
                                    <?php echo $row['NamaAlbum']; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-2"><?php echo $row['JudulFoto']; ?></h3>
                        <p class="text-gray-400 text-sm mb-3"><?php echo $row['DeskripsiFoto']; ?></p>
                        
                        <!-- Stats -->
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-4">
                                <!-- Like button -->
                                <button class="flex items-center space-x-1 <?php echo $row['UserLiked'] ? 'text-red-400' : 'text-gray-400 hover:text-red-400'; ?>">
                                    <i class="fas fa-heart"></i>
                                    <span><?php echo $row['JumlahLike']; ?></span>
                                </button>
                                <!-- Comment count -->
                                <div class="flex items-center space-x-1 text-gray-400">
                                    <i class="fas fa-comment"></i>
                                    <span><?php echo $row['JumlahKomentar']; ?></span>
                                </div>
                            </div>
                            <!-- Upload date -->
                            <span class="text-gray-500 text-xs">
                                <?php echo date('M d, Y', strtotime($row['TanggalUnggah'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>

</html>