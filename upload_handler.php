<?php
/**
 * Upload Handler untuk Gambar
 * Mendukung upload gambar JPEG, PNG, GIF
 */

class UploadHandler {
    
    private $upload_dir;
    private $allowed_types;
    private $max_file_size;
    private $errors = [];
    
    public function __construct() {
        $this->upload_dir = UPLOAD_DIR;
        $this->allowed_types = ALLOWED_IMAGE_TYPES;
        $this->max_file_size = MAX_FILE_SIZE;
        
        // Pastikan folder upload ada
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }
    
    /**
     * Upload single file
     * @param array $file - $_FILES['fieldname']
     * @param string $prefix - prefix untuk nama file
     * @return string|false - path relatif file atau false jika gagal
     */
    public function uploadImage($file, $prefix = 'img') {
        $this->errors = [];
        
        // Validasi file upload
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $this->errors[] = "Tidak ada file yang diupload";
            return false;
        }
        
        // Validasi error upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = "Error saat upload: " . $this->getUploadErrorMessage($file['error']);
            return false;
        }
        
        // Validasi ukuran file
        if ($file['size'] > $this->max_file_size) {
            $this->errors[] = "Ukuran file terlalu besar. Maksimal " . ($this->max_file_size / 1024 / 1024) . "MB";
            return false;
        }
        
        // Validasi tipe file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $this->allowed_types)) {
            $this->errors[] = "Tipe file tidak diizinkan. Hanya JPEG, PNG, dan GIF";
            return false;
        }
        
        // Generate nama file unik
        $extension = $this->getExtensionFromMime($mime_type);
        $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $this->upload_dir . $filename;
        
        // Pindahkan file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Return path relatif untuk disimpan di database
            return 'uploads/' . $filename;
        } else {
            $this->errors[] = "Gagal memindahkan file";
            return false;
        }
    }
    
    /**
     * Hapus file gambar
     * @param string $filepath - path relatif file
     * @return bool
     */
    public function deleteImage($filepath) {
        if (empty($filepath)) {
            return true;
        }
        
        $full_path = __DIR__ . '/../' . $filepath;
        
        if (file_exists($full_path)) {
            return unlink($full_path);
        }
        
        return true;
    }
    
    /**
     * Get error messages
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get error message string
     * @return string
     */
    public function getErrorString() {
        return implode(', ', $this->errors);
    }
    
    /**
     * Konversi MIME type ke extension
     */
    private function getExtensionFromMime($mime_type) {
        $mime_map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];
        
        return $mime_map[$mime_type] ?? 'jpg';
    }
    
    /**
     * Get upload error message
     */
    private function getUploadErrorMessage($error_code) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (php.ini)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (form)',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION => 'Extension PHP menghentikan upload'
        ];
        
        return $errors[$error_code] ?? 'Unknown error';
    }
    
    /**
     * Validasi apakah file adalah gambar valid
     */
    public function isValidImage($filepath) {
        $full_path = __DIR__ . '/../' . $filepath;
        
        if (!file_exists($full_path)) {
            return false;
        }
        
        $image_info = @getimagesize($full_path);
        return $image_info !== false;
    }
}
?>