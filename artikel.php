<?php
require_once 'koneksi.php';

// Pengaturan Pagination (6 kartu per halaman sesuai desain)
$limit = 6;
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total artikel di database
$qTotal     = mysqli_query($conn, "SELECT COUNT(*) AS total FROM articles");
$totalData  = mysqli_fetch_assoc($qTotal)['total'] ?? 0;
$totalPages = max(1, ceil($totalData / $limit));

// Ambil data artikel untuk halaman aktif
$qArticles    = mysqli_query($conn, "SELECT * FROM articles ORDER BY published_at DESC LIMIT $limit OFFSET $offset");
$jumlahTampil = mysqli_num_rows($qArticles);
$startNum     = $totalData > 0 ? $offset + 1 : 0;
$endNum       = $offset + $jumlahTampil;

// Helper format tanggal (contoh: 28 Apr 2025)
function formatArticleDate($dateStr) {
    if (!$dateStr) return '';
    $bulan = [
        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
        '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
        '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
    ];
    $parts = explode('-', $dateStr);
    if (count($parts) !== 3) return $dateStr;
    return ltrim($parts[2], '0') . ' ' . ($bulan[$parts[1]] ?? $parts[1]) . ' ' . $parts[0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel — sey-journal</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Lora & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-cream: #FAF7F2;
            --card-bg: #F3EFEA;
            --card-white: #FFFFFF;
            --border-soft: #E6E0D6;
            --text-dark: #252725;
            --text-muted: #5C615D;
            --accent-green: #4D7658;
            --accent-green-soft: #E7EFE8;
        }

        body {
            background-color: var(--bg-cream);
            color: var(--text-dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.65;
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

        /* Header Halaman Artikel */
        .page-header {
            padding: 2.5rem 0 2.5rem;
            position: relative;
        }

        .page-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: -4%;
            width: 360px;
            height: 220px;
            background: radial-gradient(circle, rgba(235, 224, 203, 0.65) 0%, rgba(250, 247, 242, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        .page-title {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: clamp(2.15rem, 4.5vw, 3.25rem);
            line-height: 1.15;
            letter-spacing: -0.02em;
            max-width: 600px;
            margin-bottom: 1rem;
        }

        .page-subtitle {
            font-size: 0.94rem;
            color: var(--text-muted);
            max-width: 580px;
            line-height: 1.7;
        }

        /* Tajuk Bagian "Tulisanku [6 Artikel]" */
        .section-subbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .section-subbar h2 {
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0;
        }

        .count-pill {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.22rem 0.7rem;
            border-radius: 999px;
        }

        /* Kartu Artikel (2 Kolom) */
        .article-card {
            background-color: var(--card-white);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .article-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 26px rgba(37, 39, 37, 0.06);
        }

        .article-img-wrap {
            width: 100%;
            height: 220px;
            background-color: #DFD8CC;
            overflow: hidden;
        }

        .article-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .article-card:hover .article-img {
            transform: scale(1.03);
        }

        .article-body {
            padding: 1.55rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .article-date {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 0.65rem;
        }

        .article-date .dot {
            width: 6px;
            height: 6px;
            background-color: var(--accent-green);
            border-radius: 50%;
            display: inline-block;
        }

        .article-title {
            font-family: 'Lora', serif;
            font-size: 1.2rem;
            font-weight: 700;
            line-height: 1.38;
            margin-bottom: 0.75rem;
            color: var(--text-dark);
        }

        .article-excerpt {
            font-size: 0.87rem;
            color: var(--text-muted);
            line-height: 1.65;
            margin-bottom: 1.4rem;
            flex-grow: 1;
        }

        .article-read-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--accent-green);
            text-decoration: none;
            transition: gap 0.2s ease, color 0.2s ease;
        }

        .article-read-link:hover {
            color: var(--text-dark);
            gap: 0.55rem;
        }

        /* Pagination Bar */
        .pagination-bar {
            border-top: 1px solid var(--border-soft);
            margin-top: 3.5rem;
            padding-top: 1.75rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        .page-numbers {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .page-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.82rem;
            font-weight: 500;
        }

        .page-btn:hover {
            background-color: var(--card-bg);
            color: var(--text-dark);
        }

        .page-btn.active {
            background-color: var(--accent-green);
            color: #FFFFFF;
            font-weight: 600;
        }

        .page-btn.disabled {
            opacity: 0.4;
            pointer-events: none;
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
                    <li class="nav-item"><a class="nav-link" href="kegiatan.php">Kegiatan</a></li>
                    <li class="nav-item"><a class="nav-link active" href="artikel.php">Artikel</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA ARTIKEL -->
    <main class="container pb-5">
        <header class="page-header">
            <h1 class="page-title">Isi Kepala yang Dituangkan Jadi Kata</h1>
            <p class="page-subtitle">
                Daripada cuma berisik di kepala, lebih baik kutulis di sini. Tidak semua tulisan harus punya jawaban besar; kadang cukup menjadi tempat pulang bagi hal-hal yang sempat singgah.
            </p>
        </header>

        <!-- Sub-header Tulisanku -->
        <div class="section-subbar">
            <h2>Tulisanku</h2>
            <span class="count-pill"><?= $totalData ?> Artikel</span>
        </div>

        <!-- Grid 2 Kolom Kartu Artikel -->
        <section class="row g-4">
            <?php if ($jumlahTampil > 0) : ?>
                <?php while ($art = mysqli_fetch_assoc($qArticles)) : ?>
                <div class="col-md-6">
                    <article class="article-card">
                        <div class="article-img-wrap">
                            <img src="<?= htmlspecialchars($art['image'] ?? '') ?>" 
                                 alt="<?= htmlspecialchars($art['title'] ?? '') ?>" 
                                 class="article-img"
                                 onerror="this.src='https://placehold.co/800x450/DFD8CC/4D7658?text=Tulisan+Sey'">
                        </div>

                        <div class="article-body">
                            <div class="article-date">
                                <span class="dot"></span>
                                <span><?= htmlspecialchars(formatArticleDate($art['published_at'] ?? '')) ?></span>
                            </div>

                            <h3 class="article-title">
                                <?= htmlspecialchars($art['title'] ?? '') ?>
                            </h3>

                            <p class="article-excerpt">
                                <?= htmlspecialchars($art['excerpt'] ?? '') ?>
                            </p>

                            <a href="detail-artikel.php?type=article&id=<?= $art['id'] ?>" class="article-read-link">
                                <span>Baca Selengkapnya</span>
                                <i class="bi bi-arrow-up-right"></i>
                            </a>
                        </div>
                    </article>
                </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5 text-muted">
                    Belum ada tulisan yang diterbitkan.
                </div>
            <?php endif; ?>
        </section>

        <!-- Pagination Bar -->
        <div class="pagination-bar">
            <div>
                Menampilkan <strong><?= $startNum ?>–<?= $endNum ?></strong> dari <strong><?= $totalData ?></strong> esai
            </div>

            <nav class="page-numbers" aria-label="Navigasi halaman artikel">
                <a href="?page=<?= $page - 1 ?>" class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>

                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?page=<?= $i ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="?page=<?= $page + 1 ?>" class="page-btn <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </nav>
        </div>
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