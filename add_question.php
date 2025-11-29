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

// Get level_id from URL
$level_id = isset($_GET['level_id']) ? (int)$_GET['level_id'] : 0;

if ($level_id <= 0) {
    header('Location: levels.php');
    exit;
}

// Get level info
$stmt = $pdo->prepare("SELECT * FROM levels WHERE id = ?");
$stmt->execute([$level_id]);
$level = $stmt->fetch();

if (!$level) {
    header('Location: levels.php');
    exit;
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    
    $question_text = trim($_POST['question_text']);
    $question_order = (int)$_POST['question_order'];
    
    // Upload gambar soal jika ada
    $question_image = null;
    if (isset($_FILES['question_image']) && $_FILES['question_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $question_image = $upload_handler->uploadImage($_FILES['question_image'], 'question');
        if (!$question_image) {
            $error = "Error upload gambar soal: " . $upload_handler->getErrorString();
        }
    }
    
    if (empty($error)) {
        try {
            // Insert question
            $stmt = $pdo->prepare("INSERT INTO questions (level_id, question_text, question_image, question_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$level_id, $question_text, $question_image, $question_order]);
            $question_id = $pdo->lastInsertId();
            
            // Insert answers
            $answers = $_POST['answers'];
            $correct_answer = (int)$_POST['correct_answer'];
            $reasons = $_POST['reasons'];
            
            for ($i = 0; $i < count($answers); $i++) {
                if (!empty(trim($answers[$i]))) {
                    $is_correct = ($i === $correct_answer) ? 1 : 0;
                    
                    // Upload gambar jawaban jika ada
                    $answer_image = null;
                    if (isset($_FILES['answer_images']['tmp_name'][$i]) && $_FILES['answer_images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                        $file = [
                            'name' => $_FILES['answer_images']['name'][$i],
                            'type' => $_FILES['answer_images']['type'][$i],
                            'tmp_name' => $_FILES['answer_images']['tmp_name'][$i],
                            'error' => $_FILES['answer_images']['error'][$i],
                            'size' => $_FILES['answer_images']['size'][$i]
                        ];
                        $answer_image = $upload_handler->uploadImage($file, 'answer');
                    }
                    
                    $stmt = $pdo->prepare("INSERT INTO answers (question_id, answer_text, answer_image, is_correct, reason) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $question_id,
                        trim($answers[$i]),
                        $answer_image,
                        $is_correct,
                        trim($reasons[$i])
                    ]);
                }
            }
            
            $success = "Soal berhasil ditambahkan!";
            
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Get next question order
$stmt = $pdo->prepare("SELECT MAX(question_order) as max_order FROM questions WHERE level_id = ?");
$stmt->execute([$level_id]);
$result = $stmt->fetch();
$next_order = ($result['max_order'] ?? 0) + 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Soal - PatternPlay Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 10px; }
        .level-info { background: <?php echo htmlspecialchars($level['color']); ?>; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
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
        .btn:hover { opacity: 0.9; }
        .image-preview { max-width: 200px; max-height: 200px; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .file-input-wrapper { position: relative; }
        .file-info { font-size: 12px; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Tambah Soal Baru</h1>
        
        <div class="level-info">
            <strong><?php echo htmlspecialchars($level['title']); ?></strong><br>
            <small><?php echo htmlspecialchars($level['description']); ?></small>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="questionForm">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label>Teks Soal:</label>
                <textarea name="question_text" required placeholder="Contoh: Lengkapi pola berikut: 2, 4, 6, 8, __"></textarea>
            </div>

            <div class="form-group">
                <label>Gambar Soal (Opsional):</label>
                <input type="file" name="question_image" accept="image/*" onchange="previewImage(this, 'question_preview')">
                <div class="file-info">Format: JPEG, PNG, GIF. Maksimal 5MB</div>
                <img id="question_preview" class="image-preview" style="display: none;">
            </div>

            <div class="form-group">
                <label>Urutan Soal:</label>
                <input type="number" name="question_order" value="<?php echo $next_order; ?>" required>
            </div>

            <h3 style="margin: 30px 0 15px 0;">Jawaban</h3>
            
            <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="answer-group" id="answer_<?php echo $i; ?>">
                <div class="radio-group">
                    <input type="radio" name="correct_answer" value="<?php echo $i; ?>" id="correct_<?php echo $i; ?>" <?php echo $i === 0 ? 'checked' : ''; ?> onchange="highlightCorrect(<?php echo $i; ?>)">
                    <label for="correct_<?php echo $i; ?>">Jawaban Benar</label>
                </div>
                
                <div class="form-group">
                    <label>Teks Jawaban <?php echo $i + 1; ?>:</label>
                    <input type="text" name="answers[]" required placeholder="Masukkan jawaban">
                </div>

                <div class="form-group">
                    <label>Gambar Jawaban (Opsional):</label>
                    <input type="file" name="answer_images[]" accept="image/*" onchange="previewImage(this, 'answer_preview_<?php echo $i; ?>')">
                    <img id="answer_preview_<?php echo $i; ?>" class="image-preview" style="display: none;">
                </div>

                <div class="form-group">
                    <label>Alasan/Penjelasan:</label>
                    <textarea name="reasons[]" required placeholder="Jelaskan mengapa jawaban ini benar/salah"></textarea>
                </div>
            </div>
            <?php endfor; ?>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">💾 Simpan Soal</button>
                <a href="questions.php?level_id=<?php echo $level_id; ?>" class="btn btn-secondary">↩ Kembali</a>
            </div>
        </form>
    </div>

    <script>
        function highlightCorrect(index) {
            for (let i = 0; i < 4; i++) {
                const elem = document.getElementById('answer_' + i);
                if (i === index) {
                    elem.classList.add('correct');
                } else {
                    elem.classList.remove('correct');
                }
            }
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

        // Highlight correct answer on page load
        highlightCorrect(0);
    </script>
</body>
</html>