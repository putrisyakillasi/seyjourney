<?php
require_once 'koneksi.php';

$limit = 6;
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$qTotal      = mysqli_query($conn, "SELECT COUNT(*) AS total FROM activities");
$totalData   = mysqli_fetch_assoc($qTotal)['total'] ?? 0;
$totalPages  = max(1, ceil($totalData / $limit));

$qActivities  = mysqli_query($conn, "SELECT * FROM activities ORDER BY event_date DESC LIMIT $limit OFFSET $offset");
$jumlahTampil = mysqli_num_rows($qActivities);
$startNum     = $totalData > 0 ? $offset + 1 : 0;
$endNum       = $offset + $jumlahTampil;

function formatBadgeDate($dateStr) {
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
    <title>Kegiatan — sey-journal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

        .page-header {
            padding: 2.5rem 0 2.75rem;
            position: relative;
        }

        .page-title {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: clamp(2.15rem, 4.5vw, 3.25rem);
            line-height: 1.15;
            letter-spacing: -0.02em;
            max-width: 580px;
            margin-bottom: 1rem;
        }

        .page-subtitle {
            font-size: 0.94rem;
            color: var(--text-muted);
            max-width: 620px;
            line-height: 1.7;
        }

        .activity-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .activity-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 26px rgba(37, 39, 37, 0.06);
        }

        .activity-img-wrap {
            position: relative;
            width: 100%;
            height: 195px;
            background-color: #DFD8CC;
            overflow: hidden;
        }

        .activity-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .date-badge {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background-color: rgba(34, 37, 35, 0.78);
            color: #FFFFFF;
            font-size: 0.7rem;
            font-weight: 500;
            padding: 0.28rem 0.65rem;
            border-radius: 6px;
        }

        .activity-body {
            padding: 1.4rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .activity-meta {
            font-size: 0.74rem;
            color: var(--accent-green);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.55rem;
        }

        .activity-title {
            font-family: 'Lora', serif;
            font-size: 1.12rem;
            font-weight: 700;
            line-height: 1.38;
            margin-bottom: 0.65rem;
            color: var(--text-dark);
        }

        .activity-excerpt {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.35rem;
            flex-grow: 1;
        }

        .btn-detail-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: var(--card-white);
            border: 1px solid var(--border-soft);
            color: var(--text-dark);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.65rem 1rem;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .btn-detail-card:hover {
            border-color: var(--accent-green);
            color: var(--accent-green);
        }

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
                    <li class="nav-item"><a class="nav-link active" href="kegiatan.php">Kegiatan</a></li>
                    <li class="nav-item"><a class="nav-link" href="artikel.php">Artikel</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        <header class="page-header">
            <h1 class="page-title">Ruang Simpan &amp; Momen Singgah</h1>
            <p class="page-subtitle">
                Di sini aku menyimpan potongan dokumentasi dari hal-hal seru yang kulakukan, pertemuan baru, dan pengalaman yang sempat singgah. Setiap foto di bawah ini punya cerita kecilnya masing-masing tentang bagaimana aku belajar, tertawa, atau sekadar mengamati sekitar.
            </p>
        </header>

        <section class="row g-4">
            <?php if ($jumlahTampil > 0) : ?>
                <?php while ($act = mysqli_fetch_assoc($qActivities)) : ?>
                <div class="col-md-6 col-lg-4">
                    <article class="activity-card">
                        <div class="activity-img-wrap">
                            <img src="<?= htmlspecialchars($act['image'] ?? '') ?>" 
                                 alt="<?= htmlspecialchars($act['title'] ?? '') ?>" 
                                 class="activity-img"
                                 onerror="this.src='https://placehold.co/600x400/DFD8CC/4D7658?text=Dokumentasi'">
                            <span class="date-badge">
                                <?= htmlspecialchars(formatBadgeDate($act['event_date'] ?? '')) ?>
                            </span>
                        </div>

                        <div class="activity-body">
                            <div class="activity-meta">
                                <i class="bi bi-geo-alt"></i>
                                <span><?= htmlspecialchars($act['location'] ?? 'Offline') ?></span>
                            </div>

                            <h2 class="activity-title">
                                <?= htmlspecialchars($act['title'] ?? '') ?>
                            </h2>

                            <p class="activity-excerpt">
                                <?= htmlspecialchars($act['excerpt'] ?? '') ?>
                            </p>

                            <a href="detail.php?type=activity&id=<?= $act['id'] ?>" class="d-block h-100">
                                 <img src="<?= htmlspecialchars($act['image'] ?? '') ?>" 
                                 alt="<?= htmlspecialchars($act['title'] ?? '') ?>" 
                                  class="activity-img"
                                  onerror="this.src='https://placehold.co/600x400/DFD8CC/4D7658?text=Dokumentasi'">
                                </a>
                            </a>
                        </div>
                    </article>
                </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5 text-muted">
                    Belum ada dokumentasi kegiatan yang ditambahkan.
                </div>
            <?php endif; ?>
        </section>

        <div class="pagination-bar">
            <div>
                Menampilkan <strong><?= $startNum ?>–<?= $endNum ?></strong> dari total <strong><?= $totalData ?></strong> kegiatan tercatat
            </div>

            <nav class="page-numbers" aria-label="Navigasi halaman kegiatan">
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

    <footer class="footer-sey">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <span class="font-serif fw-bold text-dark">sey-journal</span>
            <span>&copy; <?= date('Y') ?> sey-journal. when the stars appeared in February, we prayed.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>