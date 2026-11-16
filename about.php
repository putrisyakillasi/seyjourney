<?php
require_once 'koneksi.php';

// Ambil data halaman About dari database
$qAbout = mysqli_query($conn, "SELECT * FROM about_page LIMIT 1");
$about  = mysqli_fetch_assoc($qAbout);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About — sey-journal</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Lora (dengan Italic) & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700;1,400;1,500&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-cream: #FAF7F2;
            --card-bg: #F3EFEA;
            --card-white: #FFFFFF;
            --border-soft: #E6E0D6;
            --text-dark: #252725;
            --text-muted: #525753;
            --accent-green: #4D7658;
        }

        body {
            background-color: var(--bg-cream);
            color: var(--text-dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.75;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        h1, h2, h3, .font-serif {
            font-family: 'Lora', serif;
            color: var(--text-dark);
        }

        /* Navbar konsisten dengan home.php */
        .navbar-sey {
            background-color: rgba(250, 247, 242, 0.92);
            backdrop-filter: blur(10px);
            padding: 1.15rem 0;
        }

        .navbar-brand {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--text-dark) !important;
            letter-spacing: -0.01em;
        }

        .nav-pill-group {
            background-color: var(--card-bg);
            padding: 0.25rem;
            border-radius: 999px;
            border: 1px solid var(--border-soft);
        }

        .nav-pill-group .nav-link {
            color: var(--text-muted);
            font-size: 0.84rem;
            font-weight: 500;
            padding: 0.38rem 1.05rem;
            border-radius: 999px;
            transition: all 0.25s ease;
        }

        .nav-pill-group .nav-link:hover,
        .nav-pill-group .nav-link.active {
            background-color: var(--card-white);
            color: var(--text-dark);
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        /* Judul Besar Halaman About */
        .about-header {
            padding: 2.5rem 0 2.75rem;
        }

        .about-main-title {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: clamp(2.2rem, 4.8vw, 3.35rem);
            line-height: 1.15;
            letter-spacing: -0.02em;
            max-width: 650px;
        }

        .about-main-title .highlight-italic {
            display: block;
            font-style: italic;
            font-weight: 500;
            color: var(--accent-green);
        }

        /* Foto Lingkaran Kiri */
        .about-photo-wrap {
            position: relative;
            width: 100%;
            max-width: 340px;
            aspect-ratio: 1 / 1;
            margin: 0 auto;
        }

        .about-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid var(--card-white);
            box-shadow: 0 16px 36px rgba(37, 39, 37, 0.08);
            background-color: #DFD8CC;
        }

        /* Kartu Narasi Biografi Kanan */
        .bio-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 20px;
            padding: clamp(1.75rem, 4vw, 2.75rem);
        }

        .bio-heading {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: clamp(1.3rem, 2.5vw, 1.65rem);
            line-height: 1.35;
            margin-bottom: 1.35rem;
            color: var(--text-dark);
        }

        .bio-text {
            font-size: 0.92rem;
            color: var(--text-muted);
            margin-bottom: 1.15rem;
            text-align: left;
        }

        .bio-text:last-child {
            margin-bottom: 0;
        }

        /* Footer */
        .footer-sey {
            margin-top: auto;
            border-top: 1px solid var(--border-soft);
            padding: 1.75rem 0;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        @media (max-width: 991.98px) {
            .about-photo-wrap {
                max-width: 240px;
                margin-bottom: 2.25rem;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-md navbar-sey sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">sey-journal</a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end mt-3 mt-md-0" id="navbarNav">
                <ul class="navbar-nav nav-pill-group">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kegiatan.php">Kegiatan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="artikel.php">Artikel</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA ABOUT -->
    <main class="container pb-5">
        <!-- Judul Besar -->
        <header class="about-header">
            <h1 class="about-main-title">
                <?= htmlspecialchars($about['page_title_dark'] ?? 'Mengenal Syakilla:') ?>
                <span class="highlight-italic">
                    <?= htmlspecialchars($about['page_title_green'] ?? 'Cerita, Pikiran dan Ruang kecil.') ?>
                </span>
            </h1>
        </header>

        <!-- Grid 2 Kolom: Foto Kiri & Kartu Cerita Kanan -->
        <section class="row align-items-center g-4 g-lg-5 pb-4">
            <div class="col-lg-5 text-center">
                <div class="about-photo-wrap">
                    <img src="<?= htmlspecialchars($about['about_image'] ?? 'assets/img/about-syakilla.jpg') ?>" 
                         alt="Putri Syakilla Zahra Purwana" 
                         class="about-photo"
                         onerror="this.src='https://placehold.co/500x500/DFD8CC/4D7658?text=Syakilla'">
                </div>
            </div>

            <div class="col-lg-7">
                <article class="bio-card">
                    <h2 class="bio-heading">
                        <?= htmlspecialchars($about['intro_heading'] ?? 'Aku.') ?><br>
                        <?= htmlspecialchars($about['full_name'] ?? 'Putri Syakilla Zahra Purwana.') ?>
                    </h2>

                    <p class="bio-text">
                        <?= nl2br(htmlspecialchars($about['paragraph_1'] ?? '')) ?>
                    </p>
                    <p class="bio-text">
                        <?= nl2br(htmlspecialchars($about['paragraph_2'] ?? '')) ?>
                    </p>
                    <p class="bio-text">
                        <?= nl2br(htmlspecialchars($about['paragraph_3'] ?? '')) ?>
                    </p>
                </article>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="footer-sey">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <span class="font-serif fw-bold text-dark">sey-journal</span>
            <span>&copy; <?= date('Y') ?> sey-journal. when the stars appeared in February, we prayed.</span>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>