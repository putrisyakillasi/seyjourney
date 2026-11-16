<?php
require_once 'koneksi.php';

$type = isset($_GET['type']) && $_GET['type'] === 'article' ? 'article' : 'activity';
$id   = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 1;

// Helper format tanggal (contoh: 12 Mei 2024)
function formatFullDate($dateStr) {
    if (!$dateStr) return '';
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    $parts = explode('-', $dateStr);
    if (count($parts) !== 3) return $dateStr;
    return ltrim($parts[2], '0') . ' ' . ($bulan[$parts[1]] ?? $parts[1]) . ' ' . $parts[0];
}

if ($type === 'article') {
    $q    = mysqli_query($conn, "SELECT * FROM articles WHERE id = $id LIMIT 1");
    $data = mysqli_fetch_assoc($q);
    $backUrl = 'artikel.php';
} else {
    $q    = mysqli_query($conn, "SELECT * FROM activities WHERE id = $id LIMIT 1");
    $data = mysqli_fetch_assoc($q);
    $backUrl = 'kegiatan.php';
}

// Jika ID tidak ditemukan, arahkan kembali
if (!$data) {
    header("Location: $backUrl");
    exit;
}

$title       = $data['title'] ?? '';
$subtitle    = $data['excerpt'] ?? '';
$dateText    = formatFullDate($type === 'article' ? ($data['published_at'] ?? '') : ($data['event_date'] ?? ''));
$location    = $type === 'article' ? ($data['tag'] ?? 'Tulisan') : ($data['location'] ?? 'Offline');
$heroImg     = $data['image'] ?? 'assets/img/kegiatan1.jpg';
$heroCaption = $data['hero_caption'] ?? '';
$quoteText   = $data['quote_text'] ?? '';
$content1    = $type === 'article' ? ($data['content'] ?? $data['excerpt']) : ($data['content_1'] ?? $data['excerpt']);
$galImg1     = $data['gallery_img_1'] ?? '';
$galCap1     = $data['gallery_cap_1'] ?? '';
$galImg2     = $data['gallery_img_2'] ?? '';
$galCap2     = $data['gallery_cap_2'] ?? '';
$subheading  = $data['subheading'] ?? '';
$content2    = $data['content_2'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — sey-journal</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Lora & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-cream: #FAF7F2;
            --card-bg: #F3EFEA;
            --card-white: #FFFFFF;
            --border-soft: #E6E0D6;
            --text-dark: #252725;
            --text-muted: #5C615D;
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

        /* Navbar konsisten */
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

        /* Kolom Baca Editorial terpusat */
        .article-container {
            max-width: 760px;
            margin: 0 auto;
            padding: 1.5rem 1rem 4rem;
        }

        /* Tombol Kembali */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.84rem;
            font-weight: 500;
            margin-bottom: 1.75rem;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .btn-back:hover {
            color: var(--accent-green);
            transform: translateX(-3px);
        }

        .detail-title {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: clamp(2rem, 4.5vw, 2.85rem);
            line-height: 1.2;
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
        }

        .detail-subtitle {
            font-size: 0.98rem;
            color: var(--text-muted);
            line-height: 1.65;
            margin-bottom: 1.5rem;
        }

        /* Bilah Meta (Tanggal & Lokasi) */
        .meta-strip {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 0.7rem 1.15rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.5rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .meta-item i {
            color: var(--accent-green);
        }

        /* Gambar Utama & Caption */
        .hero-figure {
            margin-bottom: 2.5rem;
        }

        .hero-figure img {
            width: 100%;
            max-height: 440px;
            object-fit: cover;
            border-radius: 18px;
            background-color: #DFD8CC;
            box-shadow: 0 12px 30px rgba(37, 39, 37, 0.06);
        }

        .figure-caption {
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 0.75rem;
            font-style: italic;
        }

        /* Kotak Kutipan (Blockquote) */
        .quote-box {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            padding: 1.85rem 2rem;
            margin: 2rem 0 2.5rem;
            position: relative;
        }

        .quote-icon {
            font-family: 'Lora', serif;
            font-size: 2.4rem;
            line-height: 1;
            color: rgba(77, 118, 88, 0.35);
            margin-bottom: 0.25rem;
            display: block;
        }

        .quote-box p {
            font-family: 'Lora', serif;
            font-size: clamp(1.05rem, 2.2vw, 1.22rem);
            font-weight: 500;
            line-height: 1.6;
            color: var(--text-dark);
            margin: 0;
        }

        /* Paragraf Isi */
        .story-body p {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.85;
            margin-bottom: 1.35rem;
        }

        .story-subheading {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: 1.45rem;
            margin: 2.25rem 0 1rem;
            color: var(--text-dark);
        }

        /* Galeri 2 Foto Sejajar */
        .gallery-grid {
            margin: 2.25rem 0;
        }

        .gallery-grid img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 14px;
            background-color: #DFD8CC;
        }

        .gallery-caption {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.55rem;
            line-height: 1.5;
        }

        /* Footer */
        .footer-sey {
            margin-top: auto;
            border-top: 1px solid var(--border-soft);
            padding: 1.75rem 0;
            font-size: 0.8rem;
            color: var(--text-muted);
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
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link <?= $type === 'activity' ? 'active' : '' ?>" href="kegiatan.php">Kegiatan</a></li>
                    <li class="nav-item"><a class="nav-link <?= $type === 'article' ? 'active' : '' ?>" href="artikel.php">Artikel</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- KONTEN DETAIL -->
    <main class="container">
        <article class="article-container">
            <!-- Tombol Kembali -->
            <a href="javascript:history.back()" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>

            <!-- Judul & Subjudul -->
            <h1 class="detail-title"><?= htmlspecialchars($title) ?></h1>
            <p class="detail-subtitle"><?= htmlspecialchars($subtitle) ?></p>

            <!-- Meta Strip (Tanggal & Lokasi/Tag) -->
            <div class="meta-strip">
                <?php if ($dateText) : ?>
                <div class="meta-item">
                    <i class="bi bi-calendar3"></i>
                    <span><?= htmlspecialchars($dateText) ?></span>
                </div>
                <?php endif; ?>

                <?php if ($location) : ?>
                <div class="meta-item">
                    <i class="bi <?= $type === 'article' ? 'bi-bookmark' : 'bi-geo-alt' ?>"></i>
                    <span><?= htmlspecialchars($location) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Gambar Utama -->
            <?php if ($heroImg) : ?>
            <figure class="hero-figure">
                <img src="<?= htmlspecialchars($heroImg) ?>" 
                     alt="<?= htmlspecialchars($title) ?>"
                     onerror="this.src='https://placehold.co/900x500/DFD8CC/4D7658?text=Dokumentasi+Sey-Journal'">
                <?php if ($heroCaption) : ?>
                    <figcaption class="figure-caption">“<?= htmlspecialchars($heroCaption) ?>”</figcaption>
                <?php endif; ?>
            </figure>
            <?php endif; ?>

            <!-- Kotak Kutipan (Blockquote) -->
            <?php if ($quoteText) : ?>
            <blockquote class="quote-box">
                <span class="quote-icon">“</span>
                <p><?= htmlspecialchars($quoteText) ?></p>
            </blockquote>
            <?php endif; ?>

            <!-- Paragraf Cerita Utama -->
            <div class="story-body">
                <?php
                $paragraphs1 = preg_split('/\r\n|\r|\n/', trim($content1));
                foreach ($paragraphs1 as $p) {
                    if (trim($p) !== '') {
                        echo '<p>' . htmlspecialchars($p) . '</p>';
                    }
                }
                ?>
            </div>

            <!-- Galeri 2 Foto Sejajar -->
            <?php if ($galImg1 || $galImg2) : ?>
            <div class="row g-4 gallery-grid">
                <?php if ($galImg1) : ?>
                <div class="col-md-6">
                    <img src="<?= htmlspecialchars($galImg1) ?>" 
                         alt="Dokumentasi 1"
                         onerror="this.src='https://placehold.co/600x400/DFD8CC/4D7658?text=Dokumentasi+1'">
                    <?php if ($galCap1) : ?>
                        <p class="gallery-caption"><?= htmlspecialchars($galCap1) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($galImg2) : ?>
                <div class="col-md-6">
                    <img src="<?= htmlspecialchars($galImg2) ?>" 
                         alt="Dokumentasi 2"
                         onerror="this.src='https://placehold.co/600x400/DFD8CC/4D7658?text=Dokumentasi+2'">
                    <?php if ($galCap2) : ?>
                        <p class="gallery-caption"><?= htmlspecialchars($galCap2) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Subjudul & Paragraf Penutup -->
            <?php if ($subheading || $content2) : ?>
            <div class="story-body">
                <?php if ($subheading) : ?>
                    <h2 class="story-subheading"><?= htmlspecialchars($subheading) ?></h2>
                <?php endif; ?>

                <?php
                $paragraphs2 = preg_split('/\r\n|\r|\n/', trim($content2));
                foreach ($paragraphs2 as $p) {
                    if (trim($p) !== '') {
                        echo '<p>' . htmlspecialchars($p) . '</p>';
                    }
                }
                ?>
            </div>
            <?php endif; ?>
        </article>
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