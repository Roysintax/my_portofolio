<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: ?page=admin");
    exit;
}

// Handle Login & Register Logic (Keep existing logic)
$error = '';
$success = '';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $kelas = $_POST['kelas'];
    $umur = $_POST['umur'];

    try {
        $check = $pdo->prepare("SELECT id FROM admin WHERE nama_username = :username");
        $check->execute(['username' => $username]);
        if ($check->rowCount() > 0) {
            $error = "Username already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO admin (nama_username, password, kelas, umur) VALUES (:username, :password, :kelas, :umur)");
            $stmt->execute(['username' => $username, 'password' => $password, 'kelas' => $kelas, 'umur' => $umur]);
            $success = "Registration successful! Please login.";
        }
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE nama_username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($password == $user['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $user['nama_username'];
            header("Location: ?page=admin");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        if ($username == 'admin' && $password == 'admin') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = 'Admin';
            header("Location: ?page=admin");
            exit;
        }
        $error = "User not found.";
    }
}

$is_register_mode = isset($_GET['mode']) && $_GET['mode'] == 'register';
$admin_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard_content';
?>

<?php if (isset($_SESSION['admin_logged_in'])): ?>
    <div style="display: flex; min-height: 80vh; gap: 20px;">
        <!-- Sidebar -->
        <div class="glass-card" style="width: 250px; padding: 20px; height: fit-content; flex-shrink: 0;">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 80px; height: 80px; background: var(--accent-color); border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #000; font-weight: bold;">
                    <?= strtoupper(substr($_SESSION['admin_name'], 0, 1)) ?>
                </div>
                <h3><?= htmlspecialchars($_SESSION['admin_name']) ?></h3>
                <p style="font-size: 0.8rem; opacity: 0.7;">Administrator</p>
            </div>
            
            <nav style="display: flex; flex-direction: column; gap: 10px;">
                <a href="?page=admin&tab=dashboard_content" class="btn-sidebar <?= $admin_tab == 'dashboard_content' ? 'active' : '' ?>">Dashboard Content</a>
                <a href="?page=admin&tab=projects" class="btn-sidebar <?= $admin_tab == 'projects' ? 'active' : '' ?>">Projects</a>
                <a href="?page=admin&tab=designs" class="btn-sidebar <?= $admin_tab == 'designs' ? 'active' : '' ?>">Designs</a>
                <a href="?page=admin&tab=education" class="btn-sidebar <?= $admin_tab == 'education' ? 'active' : '' ?>">Education</a>
                <a href="?page=admin&tab=experience" class="btn-sidebar <?= $admin_tab == 'experience' ? 'active' : '' ?>">Experience</a>
                <hr style="border-color: rgba(255,255,255,0.1); width: 100%;">
                <a href="?page=admin&action=logout" class="btn-sidebar" style="color: #ff4d4d;">Logout</a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="glass-card" style="flex-grow: 1; padding: 30px;">
            <?php
            $crud_file = "modules/admin/{$admin_tab}_crud.php";
            if (file_exists($crud_file)) {
                include $crud_file;
            } else {
                echo "<h2>Welcome to Admin Dashboard</h2><p>Select a menu from the sidebar to manage content.</p>";
            }
            ?>
        </div>
    </div>

    <style>
        .btn-sidebar {
            display: block;
            padding: 10px 15px;
            border-radius: 10px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-sidebar:hover, .btn-sidebar.active {
            background: rgba(255,255,255,0.1);
            color: var(--accent-color);
            padding-left: 20px;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; opacity: 0.8; }
        .form-control {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(0,0,0,0.2);
            color: white;
        }
        .btn-primary {
            background: var(--accent-color);
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        th { color: var(--accent-color); }
        .btn-sm { padding: 5px 10px; font-size: 0.8rem; border-radius: 3px; text-decoration: none; display: inline-block; margin-right: 5px; }
        .btn-edit { background: #ffc107; color: #000; }
        .btn-delete { background: #ff4d4d; color: #fff; }
    </style>

<?php else: ?>
    <!-- Login/Register Form (Existing) -->
    <div class="glass-card" style="max-width: 400px; margin: 0 auto;">
        <h2 class="text-center"><?= $is_register_mode ? 'Sign Up' : 'Admin Login' ?></h2>
        <?php if ($error): ?><p style="color: #ff4d4d; text-align: center;"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p style="color: #00ff00; text-align: center;"><?= $success ?></p><?php endif; ?>

        <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            <input type="text" name="username" placeholder="Username" required style="padding: 10px; border-radius: 5px; border: none; background: rgba(255,255,255,0.1); color: white;">
            <?php if ($is_register_mode): ?>
                <input type="text" name="kelas" placeholder="Class (Kelas)" required style="padding: 10px; border-radius: 5px; border: none; background: rgba(255,255,255,0.1); color: white;">
                <input type="number" name="umur" placeholder="Age (Umur)" required style="padding: 10px; border-radius: 5px; border: none; background: rgba(255,255,255,0.1); color: white;">
            <?php endif; ?>
            <input type="password" name="password" placeholder="Password" required style="padding: 10px; border-radius: 5px; border: none; background: rgba(255,255,255,0.1); color: white;">
            <button type="submit" name="<?= $is_register_mode ? 'register' : 'login' ?>" style="padding: 10px; border-radius: 5px; border: none; background: var(--accent-color); color: #000; font-weight: bold; cursor: pointer;"><?= $is_register_mode ? 'Create Account' : 'Login' ?></button>
        </form>
        <p class="text-center" style="margin-top: 15px; font-size: 0.9rem;">
            <?= $is_register_mode ? 'Already have an account?' : "Don't have an account?" ?> 
            <a href="?page=admin<?= $is_register_mode ? '' : '&mode=register' ?>" style="color: var(--accent-color);"><?= $is_register_mode ? 'Login here' : 'Sign Up' ?></a>
        </p>
    </div>
<?php endif; ?>
