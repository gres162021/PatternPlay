<?php
require_once 'config.php';
require_once 'includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header('Location: add_question.php');
            } else {
                header('Location: dashboard.php');
            }
            exit;
        } else {
            $error = 'Email atau password salah!';
        }
    } else {
        $error = 'Email dan password harus diisi!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PatternPlay</title>
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
            background: linear-gradient(135deg, #8dd5ff 0%, #5e6aa1 100%);
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
            left: -100px;
        }
        
        .left-side::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -50px;
            right: -50px;
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
        
        .login-form {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        .login-form h2 {
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .login-form .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 25px;
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
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
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
                <p>Belajar matematika semakin<br>menyenangkan bersama PatternPlay!</p>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="right-side">
            <div class="login-form">
                <h2>Selamat Datang!</h2>
                <p class="subtitle">Silakan login untuk melanjutkan</p>
                
                <?php if ($error): ?>
                    <div class="alert"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="Masukkan email Anda">
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Masukkan password Anda">
                    </div>
                    
                    <button type="submit" class="btn-login">Masuk</button>
                </form>
                
                <div class="register-link">
                    Belum punya akun? <a href="register.php">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>