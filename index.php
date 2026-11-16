<?php
require_once 'koneksi.php';

// Mengambil data pengaturan welcome page dari database
$query   = mysqli_query($conn, "SELECT * FROM site_settings LIMIT 1");
$setting = mysqli_fetch_assoc($query);

// Nilai cadangan jika tabel masih kosong
$title    = $setting['hero_title'] ?? 'Sey-Journal';
$subtitle = $setting['hero_subtitle'] ?? 'when the stars appeared in February, we prayed.';
$btnText  = $setting['button_text'] ?? 'selamat menyelam!';
$btnLink  = $setting['button_link'] ?? 'home.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    
    <!-- Framework Bootstrap 5 CSS (Responsif Desktop & HP) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts: Lora & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-cream: #FAF7F2;
            --text-dark: #2B2D2B;
            --text-muted: #525753;
            --btn-green: #4D7658;
            --btn-green-hover: #3D6046;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-cream);
            color: var(--text-dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .hero-wrapper {
            position: relative;
            width: 100%;
            max-width: 820px;
            padding: 4rem 1.5rem;
            text-align: center;
            z-index: 1;
        }

        /* Efek gradasi kabut hangat (kiri) dan hijau sage (kanan) */
        .hero-wrapper::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            height: 260px;
            background: linear-gradient(90deg, rgba(235, 224, 203, 0.55) 0%, rgba(213, 224, 211, 0.55) 100%);
            filter: blur(45px);
            border-radius: 50px;
            z-index: -1;
            pointer-events: none;
        }

        /* Pill kecil dengan titik hijau di atas judul */
        .top-dot-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(230, 226, 218, 0.8);
            padding: 5px 11px;
            border-radius: 999px;
            margin-bottom: 1.1rem;
        }

        .top-dot-pill span {
            width: 5px;
            height: 5px;
            background-color: var(--btn-green);
            border-radius: 50%;
            display: block;
        }

        .hero-title {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: clamp(2.5rem, 6vw, 4.1rem);
            color: var(--text-dark);
            letter-spacing: -0.02em;
            line-height: 1.15;
            margin-bottom: 0.75rem;
        }

        .hero-subtitle {
            font-size: clamp(0.95rem, 2vw, 1.125rem);
            color: var(--text-muted);
            font-weight: 400;
            margin-bottom: 2.2rem;
            line-height: 1.6;
        }

        .btn-dive {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background-color: var(--btn-green);
            color: #FFFFFF;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            padding: 0.8rem 1.75rem;
            border-radius: 999px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(77, 118, 88, 0.18);
        }

        .btn-dive:hover {
            background-color: var(--btn-green-hover);
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(77, 118, 88, 0.28);
        }

        .btn-dive svg {
            transition: transform 0.3s ease;
        }

        .btn-dive:hover svg {
            transform: translateX(4px);
        }

        .fade-up {
            animation: fadeUp 0.9s ease-out forwards;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <main class="container d-flex justify-content-center align-items-center">
        <div class="hero-wrapper fade-up">
            <div class="top-dot-pill">
                <span></span>
            </div>

            <h1 class="hero-title">
                <?= htmlspecialchars($title) ?>
            </h1>

            <p class="hero-subtitle">
                <?= htmlspecialchars($subtitle) ?>
            </p>

            <a href="<?= htmlspecialchars($btnLink) ?>" class="btn-dive">
                <span><?= htmlspecialchars($btnText) ?></span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </main>

</body>
</html>