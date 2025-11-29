<?php
require_once __DIR__ . '/../config.php';

function getDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Upload handler function
function uploadImage($file, $prefix = 'img') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'Tidak ada file yang diupload'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Error saat upload file'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Ukuran file terlalu besar (max 5MB)'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => 'Tipe file tidak diizinkan'];
    }
    
    $ext = ($mime_type === 'image/png') ? 'png' : (($mime_type === 'image/gif') ? 'gif' : 'jpg');
    $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $ext;
    $filepath = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => 'uploads/' . $filename];
    }
    
    return ['success' => false, 'error' => 'Gagal memindahkan file'];
}

function deleteImage($filepath) {
    if (empty($filepath)) return true;
    $full_path = __DIR__ . '/../' . $filepath;
    if (file_exists($full_path)) {
        return unlink($full_path);
    }
    return true;
}
?>