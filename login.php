<?php
session_start();
include 'config.php';

// If already logged in, redirect to index.php
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['btn_login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['txt_user']);
    $password = $_POST['txt_pass'];

    if (empty($username) || empty($password)) {
        header("Location: login.php?err=empty");
        exit();
    }

    $sql = "SELECT * FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        // Verify kung yung $password ay same na nasa database
        if ($password === $user['password']) {
            session_regenerate_id(true);
            $_SESSION['authenticated'] = true;
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        }
    } else {
        echo "There's registered account in the database";
    }

    header("Location: login.php?err=invalid");
    exit();

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SIS Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .left-panel {
            width: 40%;
            background-color: #2D3561;
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .left-panel .circle {
            position: absolute;
            border-radius: 50%;
            border: 25px solid rgba(255, 255, 255, 0.1);
        }

        .left-panel .circle-1 {
            width: 300px;
            height: 300px;
            top: -80px;
            left: -80px;
        }

        .left-panel .circle-2 {
            width: 200px;
            height: 200px;
            bottom: 20px;
            right: -70px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: auto;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon img {
            height: 100%;
            width: auto;
            display: block;
        }

        .brand-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
        }

        .brand-name span {
            color: #F5C842;
        }

        .left-content {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .left-content h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.3;
            margin-bottom: 16px;
        }

        .left-content h1 span {
            color: #F5C842;
        }

        .left-content p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.7;
        }

        .right-panel {
            width: 60%;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 40px;
        }

        .right-form-wrap {
            width: 100%;
            max-width: 420px;
        }

        .right-panel h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #2D3561;
            margin-bottom: 4px;
        }

        .right-panel .subtitle {
            font-size: 0.85rem;
            color: #1f1d1d;
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #2e2d2d;
            margin-bottom: 4px;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: #999;
            pointer-events: none;
        }

        .icon-input {
            padding-left: 40px !important;
            border-radius: 999px !important;
            border-color: #ddd !important;
            font-size: 0.9rem;
        }

        .icon-input::placeholder {
            color: #bbb;
        }

        .icon-input:focus {
            border-color: #2D3561 !important;
            box-shadow: 0 0 0 0.2rem rgba(125, 17, 40, 0.15) !important;
        }

        .btn-login {
            background-color: #2D3561;
            border: none;
            border-radius: 999px;
            color: #fff;
            font-weight: 600;
            padding: 10px;
            width: 100%;
            font-size: 0.95rem;
        }

        .btn-login:hover {
            background-color: #EBA44A;
        }

        .footer-text {
            position: absolute;
            bottom: 20px;
            font-size: 0.78rem;
            color: #aaa;
            text-align: center;
        }

        .footer-text span {
            color: #2D3561;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="login-card">

        <div class="left-panel">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>

            <div class="brand">
                <div class="brand-icon">
                    <img src="Logo2sis.svg" alt="SIS Portal logo">
                </div>
                <div class="brand-name">SIS<span>Portal</span></div>
            </div>

            <div class="left-content">
                <h1>Welcome to<br>the <span>Admin Panel</span></h1>
                <p>Manage students, records, grades,<br> all in one secure platform.</p>
            </div>
        </div>

        <div class="right-panel">
            <div class="right-form-wrap">
                <h2>Admin Login</h2>
                <p class="subtitle">Sign in to access the dashboard</p>

                <form method="POST">
                    <?php if (isset($_GET['err'])): ?>
                        <div class="alert alert-danger p-2 text-center mb-3"
                            style="font-size: 0.85em; border-radius: 999px;">
                            <?php echo ($_GET['err'] == 'empty') ? 'All fields are required.' : 'Invalid credentials.'; ?>

                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="field-wrap">
                            <i class="ti ti-user field-icon"></i>
                            <input type="text" name="txt_user" class="form-control icon-input"
                                placeholder="Enter your username" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="field-wrap">
                            <i class="ti ti-lock field-icon"></i>
                            <input type="password" name="txt_pass" id="txt_pass" class="form-control icon-input"
                                placeholder="Enter your password" style="padding-right: 42px !important;" required>
                            <i class="ti ti-eye" id="toggle-pass"
                                style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 16px; color: #999;"></i>
                        </div>
                    </div>

                    <button type="submit" name="btn_login" class="btn-login mt-1">Login</button>
                </form>
            </div>

            <p class="footer-text">© 2026 <span>SIS Portal</span> &nbsp; All rights reserved</p>
        </div>

    </div>
    <script>
        const toggle = document.getElementById('toggle-pass');
        const passInput = document.getElementById('txt_pass');

        toggle.addEventListener('click', () => {
            if (passInput.type === 'password') {
                passInput.type = 'text';
                toggle.classList.replace('ti-eye', 'ti-eye-off');
            } else {
                passInput.type = 'password';
                toggle.classList.replace('ti-eye-off', 'ti-eye');
            }
        });
    </script>
</body>

</html>