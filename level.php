<?php
require_once 'config.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$category = $_GET['category'] ?? '';
$valid_categories = ['angka', 'huruf', 'gambar', 'kalender'];

if (!in_array($category, $valid_categories)) {
    header('Location: dashboard.php');
    exit;
}

$db = getDB();

// Get category info
$category_info = [
    'angka' => ['name' => 'Pola Angka', 'icon' => '🔢', 'color' => '#d25a4b'],
    'huruf' => ['name' => 'Pola Huruf', 'icon' => '🔤', 'color' => '#88b751'],
    'gambar' => ['name' => 'Pola Gambar', 'icon' => '🎨', 'color' => '#efb739'],
    'kalender' => ['name' => 'Pola Kalender', 'icon' => '📅', 'color' => '#f17cc9']
];

$cat_info = $category_info[$category];

// Get all levels for this category
$stmt = $db->prepare("
    SELECT l.*,
    (SELECT COUNT(*) FROM questions WHERE level_id = l.id) as total_questions,
    (SELECT COUNT(DISTINCT question_id) 
     FROM user_progress 
     WHERE level_id = l.id AND user_id = ? AND is_correct = 1) as completed_questions
    FROM levels l
    WHERE category = ?
    ORDER BY level_number
");
$stmt->execute([$_SESSION['user_id'], $category]);
$levels = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $cat_info['name']; ?> - PatternPlay</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, <?php echo $cat_info['color']; ?> 0%, <?php echo $cat_info['color']; ?>dd 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .header .icon {
            font-size: 60px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            color: #333;
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 18px;
        }
        
        .btn-back {
            position: absolute;
            top: 30px;
            left: 30px;
            padding: 12px 25px;
            background: white;
            color: #333;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Levels Grid */
        .levels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .level-card {
            background: white;
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            position: relative;
            overflow: hidden;
        }
        
        .level-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: <?php echo $cat_info['color']; ?>;
        }
        
        .level-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .level-card.completed {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
        }
        
        .level-card.locked {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f5f5f5;
        }
        
        .level-number {
            font-size: 48px;
            font-weight: bold;
            color: <?php echo $cat_info['color']; ?>;
            margin-bottom: 10px;
        }
        
        .level-card.completed .level-number {
            color: #28a745;
        }
        
        .level-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .level-progress {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }
        
        .level-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
        }
        
        .no-questions {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 15px;
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .levels-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 15px;
            }
            
            .level-number {
                font-size: 36px;
            }
            
            .level-title {
                font-size: 14px;
            }
            
            .btn-back {
                position: static;
                display: inline-block;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="btn-back">← Kembali</a>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="icon"><?php echo $cat_info['icon']; ?></div>
            <h1><?php echo $cat_info['name']; ?></h1>
            <p>Pilih level yang ingin kamu mainkan</p>
        </div>
        
        <!-- Levels Grid -->
        <div class="levels-grid">
            <?php foreach ($levels as $level): ?>
                <?php 
                $is_completed = $level['total_questions'] > 0 && $level['completed_questions'] >= $level['total_questions'];
                $has_questions = $level['total_questions'] > 0;
                $progress = $level['total_questions'] > 0 ? round(($level['completed_questions'] / $level['total_questions']) * 100) : 0;
                ?>
                
                <?php if ($has_questions): ?>
                    <a href="quiz.php?level_id=<?php echo $level['id']; ?>" class="level-card <?php echo $is_completed ? 'completed' : ''; ?>">
                        <?php if ($is_completed): ?>
                            <span class="level-badge">✅</span>
                        <?php endif; ?>
                        
                        <div class="level-number"><?php echo $level['level_number']; ?></div>
                        <div class="level-title">Level <?php echo $level['level_number']; ?></div>
                        <div class="level-progress">
                            <?php echo $level['completed_questions']; ?>/<?php echo $level['total_questions']; ?> Soal
                            <?php if ($progress > 0 && !$is_completed): ?>
                                <br><small>(<?php echo $progress; ?>%)</small>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php else: ?>
                    <div class="level-card locked">
                        <span class="level-badge">🔒</span>
                        <div class="level-number"><?php echo $level['level_number']; ?></div>
                        <div class="level-title">Level <?php echo $level['level_number']; ?></div>
                        <div class="level-progress">Belum ada soal</div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($levels)): ?>
        <div class="no-questions">
            <h3>Belum ada level tersedia</h3>
            <p>Hubungi admin untuk menambahkan soal</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>