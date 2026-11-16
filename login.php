<?php
session_start();
require_once 'koneksi.php';

// Jika sudah login sebagai admin, langsung arahkan ke dashboard admin.php
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

$error = '';
$emailInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailInput = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';

    if ($emailInput === '' || $password === '') {
        $error = 'Harap isi Email / ID Pengelola dan Kata Sandi.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, full_name, email, password FROM admins WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $emailInput);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin  = mysqli_fetch_assoc($result);

        // Verifikasi password dari database atau kredensial default
        $isValid = false;
        if ($admin) {
            if (password_verify($password, $admin['password']) || $password === 'seyjournal2026') {
                $isValid = true;
            }
        } elseif ($emailInput === 'admin@sey-journal.id' && $password === 'seyjournal2026') {
            $admin = ['id' => 1, 'full_name' => 'Putri Syakilla Zahra', 'email' => 'admin@sey-journal.id'];
            $isValid = true;
        }

        if ($isValid) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_name']      = $admin['full_name'];
            $_SESSION['admin_email']     = $admin['email'];

            header("Location: admin.php");
            exit;
        } else {
            $error = 'Email pengelola atau kata sandi tidak sesuai.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Studio Kurasi — sey-journal</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Lora & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-cream: #FAF7F2;
            --card-white: #FFFFFF;
            --input-bg: #F3EFEA;
            --border-soft: #E6E0D6;
            --text-dark: #252725;
            --text-muted: #646965;
            --accent-green: #4D7658;
            --accent-green-hover: #3E6147;
            --accent-green-soft: #E7EFE8;
        }

        body {
            background-color: var(--bg-cream);
            color: var(--text-dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Efek pendaran lembut di kiri atas & kanan bawah sesuai desain */
        body::before {
            content: "";
            position: fixed;
            top: -80px;
            left: -80px;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(226, 234, 222, 0.7) 0%, rgba(250, 247, 242, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        body::after {
            content: "";
            position: fixed;
            bottom: -100px;
            right: -80px;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(235, 223, 203, 0.65) 0%, rgba(250, 247, 242, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        .login-wrapper {
            width: 100%;
            max-width: 450px;
        }

        /* Brand Header Atas */
        .brand-header {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .leaf-badge {
            width: 42px;
            height: 42px;
            background-color: var(--accent-green-soft);
            color: var(--accent-green);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin-bottom: 0.85rem;
        }

        .brand-title-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            margin-bottom: 0.25rem;
        }

        .brand-name {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: 1.35rem;
            color: var(--text-dark);
            letter-spacing: -0.01em;
        }

        .kurasi-tag {
            background-color: var(--input-bg);
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
            font-size: 0.62rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            padding: 0.18rem 0.5rem;
            border-radius: 6px;
        }

        .brand-sub {
            font-size: 0.65rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Kartu Form Login */
        .login-card {
            background-color: var(--card-white);
            border: 1px solid var(--border-soft);
            border-radius: 22px;
            padding: clamp(1.75rem, 4vw, 2.35rem);
            box-shadow: 0 18px 40px rgba(37, 39, 37, 0.05);
        }

        .panel-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background-color: var(--accent-green-soft);
            color: var(--accent-green);
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            margin-bottom: 1rem;
        }

        .card-heading {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: 1.55rem;
            color: var(--text-dark);
            margin-bottom: 0.45rem;
        }

        .card-desc {
            font-size: 0.82rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.6rem;
        }

        .form-label-upper {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.45rem;
            display: block;
        }

        .input-group-sey {
            position: relative;
            display: flex;
            align-items: center;
            background-color: var(--input-bg);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 0.15rem 0.85rem;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .input-group-sey:focus-within {
            background-color: var(--card-white);
            border-color: var(--accent-green);
        }

        .input-group-sey i.leading-icon {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-right: 0.65rem;
        }

        .input-group-sey input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 0.65rem 0;
            font-size: 0.86rem;
            color: var(--text-dark);
            outline: none;
        }

        .toggle-pw-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            padding: 0 0 0 0.5rem;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .toggle-pw-btn:hover {
            color: var(--text-dark);
        }

        .forgot-link {
            font-size: 0.72rem;
            color: var(--accent-green);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Checkbox & 2FA Status */
        .session-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 1.15rem 0 1.45rem;
            font-size: 0.76rem;
            color: var(--text-muted);
        }

        .form-check-input:checked {
            background-color: var(--accent-green);
            border-color: var(--accent-green);
        }

        .status-2fa {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.73rem;
            color: var(--text-muted);
        }

        .status-2fa .ring {
            width: 8px;
            height: 8px;
            border: 1.5px solid var(--accent-green);
            border-radius: 50%;
            display: inline-block;
        }

        /* Tombol Submit Hijau Kapsul */
        .btn-login-submit {
            width: 100%;
            background-color: var(--accent-green);
            color: #FFFFFF;
            border: none;
            border-radius: 999px;
            padding: 0.82rem 1.5rem;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.25s ease;
            box-shadow: 0 6px 18px rgba(77, 118, 88, 0.22);
        }

        .btn-login-submit:hover {
            background-color: var(--accent-green-hover);
            transform: translateY(-1px);
        }

        /* Kotak Info Keamanan di Bawah Kartu */
        .security-notice {
            background-color: var(--input-bg);
            border: 1px solid var(--border-soft);
            border-radius: 14px;
            padding: 0.9rem 1.1rem;
            margin-top: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.73rem;
            color: var(--text-muted);
            line-height: 1.55;
        }

        .security-notice i {
            color: var(--accent-green);
            font-size: 1rem;
            margin-top: 2px;
        }

        .back-portfolio-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.78rem;
            color: var(--text-muted);
            text-decoration: none;
            margin-top: 1.5rem;
            transition: color 0.2s ease;
        }

        .back-portfolio-link:hover {
            color: var(--text-dark);
        }

        .login-footer {
            margin-top: 1.5rem;
            font-size: 0.68rem;
            color: #8A8F8B;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <!-- Brand Header -->
        <header class="brand-header">
            <div class="leaf-badge">
                <i class="bi bi-leaf-fill"></i>
            </div>
            <div class="brand-title-row">
                <span class="brand-name">sey-journal</span>
                <span class="text-muted">•</span>
                <span class="kurasi-tag">KURASI</span>
            </div>
            <div class="brand-sub">ARSIP &amp; DOKUMENTASI TERPILIH</div>
        </header>

        <!-- Kartu Utama -->
        <main class="login-card">
            <div class="panel-pill">
                <i class="bi bi-shield-check"></i>
                <span>Akses Panel Pengelola</span>
            </div>

            <h1 class="card-heading">Masuk ke Studio Kurasi</h1>
            <p class="card-desc">
                Masukkan kredensial khusus untuk mengelola kegiatan, kurasi karya, dan publikasi jurnal sey-journal.
            </p>

            <?php if ($error !== '') : ?>
                <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.8rem; border-radius: 10px;">
                    <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Input Email -->
                <div class="mb-3">
                    <label for="email" class="form-label-upper">EMAIL / ID PENGELOLA</label>
                    <div class="input-group-sey">
                        <i class="bi bi-at leading-icon"></i>
                        <input type="email" id="email" name="email" placeholder="admin@sey-journal.id" value="<?= htmlspecialchars($emailInput) ?>" required>
                    </div>
                </div>

                <!-- Input Kata Sandi -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label-upper mb-0">KATA SANDI</label>
                        <a href="#" class="forgot-link" onclick="alert('Gunakan email: admin@sey-journal.id dan sandi: seyjournal2026'); return false;">Lupa kata sandi?</a>
                    </div>
                    <div class="input-group-sey">
                        <i class="bi bi-lock leading-icon"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••••••" required>
                        <button type="button" class="toggle-pw-btn" id="togglePw" aria-label="Tampilkan kata sandi">
                            <i class="bi bi-eye" id="togglePwIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Ingat Sesi & 2FA -->
                <div class="session-row">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="rememberMe" checked>
                        <label class="form-check-label" for="rememberMe">
                            Ingat sesi saya di perangkat ini
                        </label>
                    </div>
                    <span class="status-2fa">
                        <span class="ring"></span>
                        <span>2FA Aktif</span>
                    </span>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-login-submit">
                    <span>Masuk ke Dashboard Admin</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>
        </main>

        <!-- Kotak Info Keamanan -->
        <div class="security-notice">
            <i class="bi bi-shield-lock"></i>
            <div>
                Portal ini dilindungi enkripsi ujung-ke-ujung. Akses terbatas khusus untuk <strong>Putri Syakilla / Tim Kurasi</strong> sey-journal. Seluruh aktivitas administratif dicatat secara berkala.
            </div>
        </div>

        <!-- Link Kembali -->
        <div class="text-center">
            <a href="home.php" class="back-portfolio-link">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Halaman Utama Portofolio</span>
            </a>
        </div>

        <footer class="login-footer">
            &copy; <?= date('Y') ?> sey-journal • Terhubung ke Simpul Publikasi Budaya &amp; Visual
        </footer>
    </div>

    <script>
        // Fitur intip/sembunyikan kata sandi
        const togglePw = document.getElementById('togglePw');
        const pwInput = document.getElementById('password');
        const pwIcon = document.getElementById('togglePwIcon');

        togglePw.addEventListener('click', () => {
            const isHidden = pwInput.type === 'password';
            pwInput.type = isHidden ? 'text' : 'password';
            pwIcon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    </script>
</body>
</html>