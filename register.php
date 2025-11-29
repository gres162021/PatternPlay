<?php
require_once 'config.php';
require_once 'includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        $db = getDB();
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar!';
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Terjadi kesalahan saat registrasi!';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - PatternPlay</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
        }
        
        .split-container {
            display: flex;
            height: 100vh;
        }
        
        /* Left Side - Image */
        .left-side {
            flex: 1;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .left-side::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }
        
        .left-side::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -50px;
            left: -50px;
        }
        
        .logo-section {
            text-align: center;
            z-index: 1;
        }
        
        .logo-section img {
            max-width: 400px;
            width: 100%;
            height: auto;
            margin-bottom: 30px;
            filter: drop-shadow(0 10px 30px rgba(0,0,0,0.2));
        }
        
        .logo-section h1 {
            color: white;
            font-size: 48px;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .logo-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 20px;
            line-height: 1.6;
        }
        
        /* Right Side - Form */
        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #f8f9fa;
        }
        
        .register-form {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        .register-form h2 {
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .register-form .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #f5576c;
            box-shadow: 0 0 0 3px rgba(245, 87, 108, 0.1);
        }
        
        .btn-register {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(245, 87, 108, 0.4);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }
        
        .login-link a {
            color: #f5576c;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 968px) {
            .split-container {
                flex-direction: column;
            }
            
            .left-side, .right-side {
                flex: none;
                height: 50vh;
            }
            
            .logo-section h1 {
                font-size: 32px;
            }
            
            .logo-section p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="split-container">

        <!-- Left Side - Logo & Image -->
        <div class="left-side">
            <div class="logo-section">
                <img src="assets/Tak berjudul444_20251124135722.png" alt="PatternPlay" onerror="this.style.display='none'">
                <p>Bergabunglah dengan ribuan<br>pemain lainnya dan mulai<br>tantangan seru Anda!</p>
            </div>
        </div>
        
        <!-- Right Side - Register Form -->
        <div class="right-side">
            <div class="register-form">
                <h2>Daftar Akun</h2>
                <p class="subtitle">Buat akun baru untuk memulai</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success); ?>
                        <a href="index.php" style="color: #155724; font-weight: bold;">Klik di sini untuk login</a>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Masukkan nama lengkap" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="Masukkan email Anda" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter">
                    </div>
                    
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="confirm_password" required placeholder="Ketik ulang password">
                    </div>
                    
                    <button type="submit" class="btn-register">Daftar Sekarang</button>
                </form>
                
                <div class="login-link">
                    Sudah punya akun? <a href="index.php">Login Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>