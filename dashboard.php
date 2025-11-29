<?php
require_once 'config.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$db = getDB();

// Get user stats
$stmt = $db->prepare("
    SELECT 
        COUNT(DISTINCT level_id) as completed_levels,
        COUNT(*) as total_questions,
        SUM(is_correct) as correct_answers
    FROM user_progress 
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$stats = $stmt->fetch();

$categories = [
    [
        'id' => 'angka',
        'name' => 'Pola Angka',
        'icon' => '🔢',
        'color' => '#d25a4b',
        'description' => 'Asah logika dengan pola angka'
    ],
    [
        'id' => 'huruf',
        'name' => 'Pola Huruf',
        'icon' => '🔤',
        'color' => '#88b751',
        'description' => 'Temukan pola huruf tersembunyi'
    ],
    [
        'id' => 'gambar',
        'name' => 'Pola Gambar',
        'icon' => '🎨',
        'color' => '#f3ae4d',
        'description' => 'Kenali pola dari gambar yang diberikan'
    ],
    [
        'id' => 'kalender',
        'name' => 'Pola Kalender',
        'icon' => '📅',
        'color' => '#f17cc9',
        'description' => 'Pecahkan pola tanggal pada kalender'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PatternPlay</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        .logo-pp {
            width: 250px;
            height: auto;
            display: block;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8dd5ff 0%, #5e6aa1 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-left h3 {
            color: #666;
            font-size: 18px;
            margin-top: 10px;
        }
        
        .header-right {
            text-align: right;
        }
        
        .user-name {
            color: #333;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .btn-logout {
            padding: 10px 25px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            color: #666;
            font-size: 14px;
        }
        
        /* Materi Section */
        .materi-section {
            margin-bottom: 40px;
        }

        .materi-button {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            transition: 0.3s;
            color: #333;
        }

        .materi-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        }

        .materi-list {
            margin-top: 20px;
            display: none;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .materi-card {
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            transition: all 0.3s;
        }

        .materi-card:hover {
            transform: translateX(10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        }

        .materi-card h3 {
            margin-bottom: 10px;
            font-size: 20px;
        }

        .materi-card p {
            font-size: 15px;
            opacity: 0.95;
        }
        
        /* Categories */
        .categories-title {
            color: white;
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .category-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }
        
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        
        .category-header {
            padding: 40px 30px;
            color: white;
            text-align: center;
        }
        
        .category-header .icon {
            font-size: 60px;
            margin-bottom: 15px;
        }
        
        .category-header h3 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .category-header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .category-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #333;
            font-weight: 600;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .header-left {
                margin-bottom: 15px;
            }
            
            .header-right {
                text-align: center;
            }
            
            .categories-grid {
                grid-template-columns: 1fr;
            }
            
            .logo-pp {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <img src="assets/logo2.png" alt="PatternPlay Logo" class="logo-pp">
                <h3>Ayo belajar bersama!</h3>
            </div>
            <div class="header-right">
                <div class="user-name">👋 Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="stats">
            <div class="stat-card">
                <div class="icon">🏆</div>
                <div class="number"><?php echo $stats['completed_levels'] ?? 0; ?></div>
                <div class="label">Level Diselesaikan</div>
            </div>
            <div class="stat-card">
                <div class="icon">📝</div>
                <div class="number"><?php echo $stats['total_questions'] ?? 0; ?></div>
                <div class="label">Total Soal Dijawab</div>
            </div>
            <div class="stat-card">
                <div class="icon">✅</div>
                <div class="number"><?php echo $stats['correct_answers'] ?? 0; ?></div>
                <div class="label">Jawaban Benar</div>
            </div>
        </div>
        
        <!-- Materi Section (PINDAH KE ATAS) -->
        <div class="materi-section">
            <h2 class="categories-title">📚 Materi Pembelajaran</h2>
            <div class="materi-button" onclick="toggleMateri()">
                📘 Lihat Semua Materi
            </div>

            <div class="materi-list" id="materiList">
                <!-- Pola Angka -->
                <a href="materi.php?category=angka" class="materi-card" style="background: #d25a4b; text-decoration: none; display: block;">
                    <h3>🔢 Materi Pola Angka</h3>
                    <p>Pelajari barisan aritmetika, geometri, dan pola campuran</p>
                </a>

                <!-- Pola Huruf -->
                <a href="materi.php?category=huruf" class="materi-card" style="background: #88b751; text-decoration: none; display: block;">
                    <h3>🔤 Materi Pola Huruf</h3>
                    <p>Pahami urutan alfabet dan pola maju-mundur huruf</p>
                </a>

                <!-- Pola Gambar -->
                <a href="materi.php?category=gambar" class="materi-card" style="background: #f3ae4d; text-decoration: none; display: block;">
                    <h3>🎨 Materi Pola Gambar</h3>
                    <p>Kenali pola visual dan perbedaan bentuk geometri</p>
                </a>

                <!-- Pola Kalender -->
                <a href="materi.php?category=kalender" class="materi-card" style="background: #f17cc9; text-decoration: none; display: block;">
                    <h3>📅 Materi Pola Kalender</h3>
                    <p>Pelajari pola tanggal, hari, dan pengulangan waktu</p>
                </a>
            </div>
        </div>
        
        <!-- Categories -->
        <h2 class="categories-title">Pilih Kategori Permainan</h2>
        
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="level.php?category=<?php echo $cat['id']; ?>" class="category-card">
                <div class="category-header" style="background: <?php echo $cat['color']; ?>">
                    <div class="icon"><?php echo $cat['icon']; ?></div>
                    <h3><?php echo $cat['name']; ?></h3>
                    <p><?php echo $cat['description']; ?></p>
                </div>
                <div class="category-footer">
                    10 Level Tersedia ➜
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function toggleMateri() {
            const list = document.getElementById('materiList');
            list.style.display = (list.style.display === "block") ? "none" : "block";
        }
    </script>
</body>
</html>