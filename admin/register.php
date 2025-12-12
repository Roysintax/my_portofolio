<?php
session_start();
require_once '../config/connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $kelas = $_POST['kelas'] ?? '';
    $umur = $_POST['umur'] ?? '';

    if (empty($username) || empty($password) || empty($kelas) || empty($umur)) {
        $error = 'Please fill in all fields';
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM admin WHERE nama_username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $error = 'Username already exists';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admin (nama_username, password, kelas, umur) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $kelas, $umur])) {
                $success = 'Registration successful! You can now <a href="login.php">Login</a>';
            } else {
                $error = 'Registration failed';
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
    <title>Admin Register - My Portfolio</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--gradient-dark);
        }
        .auth-card {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 400px;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-header h2 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
        }
        .alert {
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .alert-danger {
            background: #ffe5e5;
            color: #d63031;
        }
        .alert-success {
            background: #e5ffe5;
            color: #00b894;
        }
        .btn-block {
            width: 100%;
            justify-content: center;
        }
        .auth-footer {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .auth-footer a {
            color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h2>Admin Register</h2>
            <p>Create your admin account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="kelas">Class/Position</label>
                <input type="text" id="kelas" name="kelas" class="form-control" placeholder="e.g. Administrator" required>
            </div>

            <div class="form-group">
                <label for="umur">Age</label>
                <input type="number" id="umur" name="umur" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
