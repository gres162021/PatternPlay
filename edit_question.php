<?php
require_once '../config.php';
require_once '../includes/upload_handler.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$upload_handler = new UploadHandler();

// Get question_id
$question_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($question_id <= 0) {
    header('Location: levels.php');
    exit;
}

// Get question with level info
$stmt = $pdo->prepare("
    SELECT q.*, l.title as level_title, l.color, l.id as level_id
    FROM questions q
    JOIN levels l ON q.level_id = l.id
    WHERE q.id = ?
");
$stmt->execute([$question_id]);
$question = $stmt->fetch();

if (!$question) {
    header('Location: levels.php');
    exit;
}

// Get answers
$stmt = $pdo->prepare("SELECT * FROM answers WHERE question_id = ? ORDER BY id");
$stmt->execute([$question_id]);
$answers = $stmt->fetchAll();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    
    $question_text = trim($_POST['question_text']);
    $question_order = (int)$_POST['question_order'];
    $question_image = $question['question_image']; // Keep old image
    
    // Upload gambar soal baru jika ada
    if (isset($_FILES['question_image']) && $_FILES['question_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $new_image = $upload_handler->uploadImage($_FILES['question_image'], 'question');
        if ($new_image) {
            // Delete old image
            if ($question_image) {
                $upload_handler->deleteImage($question_image);
            }
            $question_image = $new_image;
        } else {
            $error = "Error upload gambar soal: " . $upload_handler->getErrorString();
        }
    }
    
    // Handle delete image
    if (isset($_POST['delete_question_image']) && $_POST['delete_question_image'] == '1') {
        if ($question_image) {
            $upload_handler->deleteImage($question_image);
            $question_image = null;
        }
    }
    
    if (empty($error)) {
        try {
            // Update question
            $stmt = $pdo->prepare("UPDATE questions SET question_text = ?, question_image = ?, question_order = ? WHERE id = ?");
            $stmt->execute([$question_text, $question_image, $question_order, $question_id]);
            
            // Update answers
            $answer_ids = $_POST['answer_ids'];
            $answer_texts = $_POST['answers'];
            $correct_answer = (int)$_POST['correct_answer'];
            $reasons = $_POST['reasons'];
            
            for ($i = 0; $i < count($answer_ids); $i++) {
                $answer_id = (int)$answer_ids[$i];
                $is_correct = ($i === $correct_answer) ? 1 : 0;
                
                // Get current answer image
                $stmt = $pdo->prepare("SELECT answer_image FROM answers WHERE id = ?");
                $stmt->execute([$answer_id]);
                $current = $stmt->fetch();
                $answer_image = $current['answer_image'];
                
                // Upload gambar jawaban baru jika ada
                if (isset($_FILES['answer_images']['tmp_name'][$i]) && $_FILES['answer_images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                    $file = [
                        'name' => $_FILES['answer_images']['name'][$i],
                        'type' => $_FILES['answer_images']['type'][$i],
                        'tmp_name' => $_FILES['answer_images']['tmp_name'][$i],
                        'error' => $_FILES['answer_images']['error'][$i],
                        'size' => $_FILES['answer_images']['size'][$i]
                    ];
                    $new_image = $upload_handler->uploadImage($file, 'answer');
                    if ($new_image) {
                        if ($answer_image) {
                            $upload_handler->deleteImage($answer_image);
                        }
                        $answer_image = $new_image;
                    }
                }
                
                // Handle delete answer image
                if (isset($_POST['delete_answer_image'][$i]) && $_POST['delete_answer_image'][$i] == '1') {
                    if ($answer_image) {
                        $upload_handler->deleteImage($answer_image);
                        $answer_image = null;
                    }
                }
                
                $stmt = $pdo->prepare("UPDATE answers SET answer_text = ?, answer_image = ?, is_correct = ?, reason = ? WHERE id = ?");
                $stmt->execute([
                    trim($answer_texts[$i]),
                    $answer_image,
                    $is_correct,
                    trim($reasons[$i]),
                    $answer_id
                ]);
            }
            
            $success = "Soal berhasil diupdate!";
            
            // Refresh question data
            $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
            $stmt->execute([$question_id]);
            $question = $stmt->fetch();
            
            $stmt = $pdo->prepare("SELECT * FROM answers WHERE question_id = ? ORDER BY id");
            $stmt->execute([$question_id]);
            $answers = $stmt->fetchAll();
            
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Soal - PatternPlay Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 10px; }
        .level-info { background: <?php echo htmlspecialchars($question['color']); ?>; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="text"], input[type="number"], textarea, input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        textarea { min-height: 80px; resize: vertical; }
        .answer-group { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 3px solid #ccc; }
        .answer-group.correct { border-left-color: #4CAF50; }
        .radio-group { margin: 10px 0; }
        .radio-group label { display: inline; font-weight: normal; margin-left: 5px; }
        .btn { padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; margin-right: 10px; }
        .btn-primary { background: #4CAF50; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-danger { background: #dc3545; color: white; padding: 8px 15px; font-size: 14px; }
        .btn:hover { opacity: 0.9; }
        .image-preview { max-width: 200px; max-height: 200px; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .current-image { margin: 10px 0; }
        .file-info { font-size: 12px; color: #666; margin-top: 5px; }
        .image-controls { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Soal</h1>
        
        <div class="level-info">
            <strong><?php echo htmlspecialchars($question['level_title']); ?></strong>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label>Teks Soal:</label>
                <textarea name="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Gambar Soal:</label>
                <?php if ($question['question_image']): ?>
                    <div class="current-image">
                        <img src="../<?php echo htmlspecialchars($question['question_image']); ?>" class="image-preview">
                        <div class="image-controls">
                            <label>
                                <input type="checkbox" name="delete_question_image" value="1"> Hapus gambar ini
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" name="question_image" accept="image/*" onchange="previewImage(this, 'question_preview')">
                <div class="file-info">Upload gambar baru untuk mengganti. Format: JPEG, PNG, GIF. Maksimal 5MB</div>
                <img id="question_preview" class="image-preview" style="display: none;">
            </div>

            <div class="form-group">
                <label>Urutan Soal:</label>
                <input type="number" name="question_order" value="<?php echo htmlspecialchars($question['question_order']); ?>" required>
            </div>

            <h3 style="margin: 30px 0 15px 0;">Jawaban</h3>
            
            <?php foreach ($answers as $index => $answer): ?>
            <div class="answer-group <?php echo $answer['is_correct'] ? 'correct' : ''; ?>" id="answer_<?php echo $index; ?>">
                <input type="hidden" name="answer_ids[]" value="<?php echo $answer['id']; ?>">
                
                <div class="radio-group">
                    <input type="radio" name="correct_answer" value="<?php echo $index; ?>" id="correct_<?php echo $index; ?>" <?php echo $answer['is_correct'] ? 'checked' : ''; ?> onchange="highlightCorrect(<?php echo $index; ?>)">
                    <label for="correct_<?php echo $index; ?>">Jawaban Benar</label>
                </div>
                
                <div class="form-group">
                    <label>Teks Jawaban <?php echo $index + 1; ?>:</label>
                    <input type="text" name="answers[]" value="<?php echo htmlspecialchars($answer['answer_text']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Gambar Jawaban:</label>
                    <?php if ($answer['answer_image']): ?>
                        <div class="current-image">
                            <img src="../<?php echo htmlspecialchars($answer['answer_image']); ?>" class="image-preview">
                            <div class="image-controls">
                                <label>
                                    <input type="checkbox" name="delete_answer_image[<?php echo $index; ?>]" value="1"> Hapus gambar ini
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="answer_images[]" accept="image/*" onchange="previewImage(this, 'answer_preview_<?php echo $index; ?>')">
                    <img id="answer_preview_<?php echo $index; ?>" class="image-preview" style="display: none;">
                </div>

                <div class="form-group">
                    <label>Alasan/Penjelasan:</label>
                    <textarea name="reasons[]" required><?php echo htmlspecialchars($answer['reason']); ?></textarea>
                </div>
            </div>
            <?php endforeach; ?>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">💾 Update Soal</button>
                <a href="questions.php?level_id=<?php echo $question['level_id']; ?>" class="btn btn-secondary">↩ Kembali</a>
            </div>
        </form>
    </div>

    <script>
        function highlightCorrect(index) {
            const answers = document.querySelectorAll('.answer-group');
            answers.forEach((elem, i) => {
                if (i === index) {
                    elem.classList.add('correct');
                } else {
                    elem.classList.remove('correct');
                }
            });
        }

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>