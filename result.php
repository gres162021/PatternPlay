<?php
require_once 'config.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$level_id = $_GET['level_id'] ?? 0;
$question_id = $_GET['question_id'] ?? 0;
$answer_id = $_GET['answer_id'] ?? 0;
$current_index = $_GET['q'] ?? 0;

$db = getDB();

// Get level
$stmt = $db->prepare("SELECT * FROM levels WHERE id = ?");
$stmt->execute([$level_id]);
$level = $stmt->fetch();

// Get question
$stmt = $db->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$question_id]);
$question = $stmt->fetch();

// Get selected answer
$stmt = $db->prepare("SELECT * FROM answers WHERE id = ?");
$stmt->execute([$answer_id]);
$selected_answer = $stmt->fetch();

// Get correct answer
$stmt = $db->prepare("SELECT * FROM answers WHERE question_id = ? AND is_correct = 1");
$stmt->execute([$question_id]);
$correct_answer = $stmt->fetch();

// Get total questions
$stmt = $db->prepare("SELECT COUNT(*) as total FROM questions WHERE level_id = ?");
$stmt->execute([$level_id]);
$total_questions = $stmt->fetch()['total'];

$is_correct = $selected_answer['is_correct'] == 1;
$is_last_question = ($current_index + 1) >= $total_questions;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil - PatternPlay</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, <?php echo $level['color']; ?> 0%, <?php echo $level['color']; ?>dd 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 700px;
            width: 100%;
        }
        
        .result-card {
            background: white;
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        
        .result-icon {
            font-size: 100px;
            margin-bottom: 20px;
            animation: bounce 0.6s;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .result-title {
            font-size: 36px;
            margin-bottom: 15px;
            color: <?php echo $is_correct ? '#28a745' : '#dc3545'; ?>;
        }
        
        .result-subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }
        
        .answer-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: left;
        }
        
        .answer-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .answer-text {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .answer-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-top: 10px;
        }
        
        .reason-box {
            background: <?php echo $is_correct ? '#d4edda' : '#f8d7da'; ?>;
            border-left: 4px solid <?php echo $is_correct ? '#28a745' : '#dc3545'; ?>;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }
        
        .reason-text {
            color: #333;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 18px;
            border: none;
            border-radius: 15px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: <?php echo $level['color']; ?>;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .progress-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .result-card {
                padding: 35px 25px;
            }
            
            .result-icon {
                font-size: 80px;
            }
            
            .result-title {
                font-size: 28px;
            }
            
            .buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-card">
            <div class="result-icon">
                <?php echo $is_correct ? '🎉' : '😅'; ?>
            </div>
            
            <h1 class="result-title">
                <?php echo $is_correct ? 'Jawaban Benar!' : 'Jawaban Salah'; ?>
            </h1>
            
            <p class="result-subtitle">
                <?php echo $is_correct ? 'Hebat! Kamu berhasil menjawab dengan tepat!' : 'Jangan menyerah! Coba lagi di soal berikutnya.'; ?>
            </p>
            
            <!-- Jawaban User -->
            <div class="answer-box">
                <div class="answer-label">Jawaban Kamu:</div>
                <div class="answer-text"><?php echo htmlspecialchars($selected_answer['answer_text']); ?></div>
                <?php if ($selected_answer['answer_image']): ?>
                    <img src="<?php echo htmlspecialchars($selected_answer['answer_image']); ?>" class="answer-image" alt="Your Answer">
                <?php endif; ?>
            </div>
            
            <!-- Jawaban Benar (jika salah) -->
            <?php if (!$is_correct): ?>
            <div class="answer-box">
                <div class="answer-label">Jawaban yang Benar:</div>
                <div class="answer-text"><?php echo htmlspecialchars($correct_answer['answer_text']); ?></div>
                <?php if ($correct_answer['answer_image']): ?>
                    <img src="<?php echo htmlspecialchars($correct_answer['answer_image']); ?>" class="answer-image" alt="Correct Answer">
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Penjelasan -->
<div class="reason-box">
    <div class="reason-text">
        <strong>💡 Penjelasan:</strong><br>
        <?php 
        if ($is_correct) {
            // Jika benar, tampilkan penjelasan dari jawaban yang dipilih
            echo nl2br(htmlspecialchars($selected_answer['reason']));
        } else {
            // Jika salah, tampilkan penjelasan dari jawaban yang SALAH (bukan yang benar)
            echo nl2br(htmlspecialchars($selected_answer['reason']));
        }
        ?>
    </div>
</div>
            <!-- Progress Info -->
            <div class="progress-info">
                Soal <?php echo $current_index + 1; ?> dari <?php echo $total_questions; ?> selesai
            </div>
            
            <!-- Buttons -->
            <div class="buttons">
                <?php if (!$is_last_question): ?>
                    <a href="quiz.php?level_id=<?php echo $level_id; ?>&q=<?php echo $current_index + 1; ?>" class="btn btn-primary">
                        Soal Selanjutnya ➜
                    </a>
                <?php else: ?>
                    <a href="level.php?category=<?php echo $level['category']; ?>" class="btn btn-primary">
                        Selesai ✓
                    </a>
                <?php endif; ?>
                
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>