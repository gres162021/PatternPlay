<?php
require_once 'config.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$level_id = $_GET['level_id'] ?? 0;
$db = getDB();

// Get level info
$stmt = $db->prepare("SELECT * FROM levels WHERE id = ?");
$stmt->execute([$level_id]);
$level = $stmt->fetch();

if (!$level) {
    header('Location: dashboard.php');
    exit;
}

// Get all questions for this level
$stmt = $db->prepare("SELECT * FROM questions WHERE level_id = ? ORDER BY question_order, id");
$stmt->execute([$level_id]);
$questions = $stmt->fetchAll();

if (empty($questions)) {
    die("Belum ada soal untuk level ini");
}

// Get current question index
$current_index = $_GET['q'] ?? 0;
$current_index = max(0, min($current_index, count($questions) - 1));
$question = $questions[$current_index];

// Get answers for current question
$stmt = $db->prepare("SELECT * FROM answers WHERE question_id = ? ORDER BY id");
$stmt->execute([$question['id']]);
$answers = $stmt->fetchAll();

// Handle answer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer_id'])) {
    $answer_id = (int)$_POST['answer_id'];
    
    // Get answer info
    $stmt = $db->prepare("SELECT * FROM answers WHERE id = ?");
    $stmt->execute([$answer_id]);
    $selected_answer = $stmt->fetch();
    
    if ($selected_answer) {
        // Save user progress
        $stmt = $db->prepare("
            INSERT INTO user_progress (user_id, level_id, question_id, answer_id, is_correct)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $level_id,
            $question['id'],
            $answer_id,
            $selected_answer['is_correct']
        ]);
        
        // Redirect to result
        header("Location: result.php?level_id=$level_id&question_id={$question['id']}&answer_id=$answer_id&q=$current_index");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - PatternPlay</title>
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
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Header */
        .quiz-header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .level-info h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .level-info p {
            color: #666;
            font-size: 14px;
        }
        
        .question-counter {
            background: <?php echo $level['color']; ?>;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
        }
        
        /* Question Card */
        .question-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .question-text {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .question-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        /* Answers */
        .answers-grid {
            display: grid;
            gap: 15px;
        }
        
        .answer-option {
            background: #f8f9fa;
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .answer-option:hover {
            background: #e9ecef;
            border-color: <?php echo $level['color']; ?>;
            transform: translateX(5px);
        }
        
        .answer-option input[type="radio"] {
            display: none;
        }
        
        .answer-option .radio-custom {
            width: 24px;
            height: 24px;
            border: 3px solid #ddd;
            border-radius: 50%;
            flex-shrink: 0;
            transition: all 0.3s;
        }
        
        .answer-option input[type="radio"]:checked + .radio-custom {
            border-color: <?php echo $level['color']; ?>;
            background: <?php echo $level['color']; ?>;
            box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
        }
        
        .answer-content {
            flex: 1;
        }
        
        .answer-text {
            font-size: 18px;
            color: #333;
        }
        
        .answer-image {
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 18px;
            background: <?php echo $level['color']; ?>;
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        
        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-back {
            display: inline-block;
            padding: 12px 25px;
            background: white;
            color: #333;
            border-radius: 10px;
            text-decoration: none;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .quiz-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .question-card {
                padding: 25px;
            }
            
            .question-text {
                font-size: 20px;
            }
            
            .answer-text {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="level.php?category=<?php echo $level['category']; ?>" class="btn-back">← Kembali ke Level</a>
        
        <!-- Header -->
        <div class="quiz-header">
            <div class="level-info">
                <h2><?php echo htmlspecialchars($level['title']); ?></h2>
                <p><?php echo htmlspecialchars($level['description']); ?></p>
            </div>
            <div class="question-counter">
                Soal <?php echo $current_index + 1; ?> dari <?php echo count($questions); ?>
            </div>
        </div>
        
        <!-- Question Card -->
        <div class="question-card">
            <div class="question-text">
                <?php echo nl2br(htmlspecialchars($question['question_text'])); ?>
            </div>
            
            <?php if ($question['question_image']): ?>
                <img src="<?php echo htmlspecialchars($question['question_image']); ?>" alt="Question Image" class="question-image">
            <?php endif; ?>
            
            <form method="POST" id="quizForm">
                <div class="answers-grid">
                    <?php foreach ($answers as $answer): ?>
                    <label class="answer-option">
                        <input type="radio" name="answer_id" value="<?php echo $answer['id']; ?>" required onchange="enableSubmit()">
                        <div class="radio-custom"></div>
                        <div class="answer-content">
                            <div class="answer-text"><?php echo htmlspecialchars($answer['answer_text']); ?></div>
                            <?php if ($answer['answer_image']): ?>
                                <img src="<?php echo htmlspecialchars($answer['answer_image']); ?>" alt="Answer Image" class="answer-image">
                            <?php endif; ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                
                <br>
                <button type="submit" class="submit-btn" id="submitBtn" disabled>Jawab</button>
            </form>
        </div>
    </div>
    
    <script>
        function enableSubmit() {
            document.getElementById('submitBtn').disabled = false;
        }
    </script>
</body>
</html>