<?php
require_once 'koneksi.php';

// Ambil data profil dari database (atau sesuaikan langsung di bawah)
$qProfile = mysqli_query($conn, "SELECT * FROM profile_info LIMIT 1");
$profile  = mysqli_fetch_assoc($qProfile);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sey-Journal — Beranda & Portofolio</title>

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
            --accent-green-soft: #E5ECE6;
        }

        body {
            background-color: var(--bg-cream);
            color: var(--text-dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.65;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .font-serif {
            font-family: 'Lora', serif;
            color: var(--text-dark);
        }

        /* Navbar */
        .navbar-sey {
            background-color: rgba(250, 247, 242, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-soft);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: 1.25rem;
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
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.4rem 1rem;
            border-radius: 999px;
            transition: all 0.25s ease;
        }

        .nav-pill-group .nav-link:hover,
        .nav-pill-group .nav-link.active {
            background-color: var(--card-white);
            color: var(--text-dark);
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        /* Hero Section */
        .hero-section {
            padding: 4.5rem 0 3.5rem;
            position: relative;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 10%;
            left: -5%;
            width: 340px;
            height: 340px;
            background: radial-gradient(circle, rgba(235, 224, 203, 0.65) 0%, rgba(250, 247, 242, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        .hero-heading {
            font-size: clamp(2.1rem, 4.5vw, 3.25rem);
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
            margin-bottom: 1.25rem;
        }

        .hero-lead {
            font-size: 1rem;
            color: var(--text-muted);
            max-width: 520px;
        }

        .profile-circle-wrap {
            position: relative;
            width: 260px;
            height: 260px;
            margin: 0 auto;
        }

        .profile-circle {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid var(--card-white);
            box-shadow: 0 14px 32px rgba(37, 39, 37, 0.08);
            background-color: #E8DFD1;
        }

        /* Section Header */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 1.75rem;
            gap: 1rem;
        }

        .section-eyebrow {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--accent-green);
            font-weight: 600;
            display: block;
            margin-bottom: 0.25rem;
        }

        .section-title {
            font-size: clamp(1.35rem, 2.5vw, 1.75rem);
            font-weight: 700;
            margin: 0;
        }

        .link-all {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            white-space: nowrap;
            transition: color 0.2s ease;
        }

        .link-all:hover {
            color: var(--accent-green);
        }

        /* Cards (Kegiatan & Tulisan) */
        .sey-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .sey-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(37, 39, 37, 0.06);
        }

        .sey-card-img {
            width: 100%;
            height: 185px;
            object-fit: cover;
            background-color: #E2DBD0;
            display: block;
        }

        .sey-card-body {
            padding: 1.4rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-tag {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--accent-green);
            margin-bottom: 0.5rem;
        }

        .card-title-serif {
            font-family: 'Lora', serif;
            font-size: 1.08rem;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 0.65rem;
        }

        .card-excerpt {
            font-size: 0.86rem;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
            flex-grow: 1;
        }

        .card-read-more {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-dark);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: color 0.2s ease;
        }

        .card-read-more:hover {
            color: var(--accent-green);
        }

        /* Social Box Section */
        .social-box {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 22px;
            padding: clamp(1.75rem, 4vw, 3rem);
            margin: 4rem 0;
        }

        .social-item-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: var(--card-white);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 0.85rem 1.15rem;
            text-decoration: none;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }

        .social-item-btn:last-child {
            margin-bottom: 0;
        }

        .social-item-btn:hover {
            border-color: var(--accent-green);
            transform: translateX(4px);
            color: var(--accent-green);
        }

        .social-badge {
            font-size: 0.75rem;
            background-color: var(--bg-cream);
            color: var(--text-muted);
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
        }

        /* Footer */
        .footer-sey {
            border-top: 1px solid var(--border-soft);
            padding: 1.75rem 0;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        @media (max-width: 767.98px) {
            .profile-circle-wrap {
                width: 200px;
                height: 200px;
                margin-top: 2rem;
            }
            .hero-section {
                padding: 3rem 0 2.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- 1. NAVBAR -->
    <nav class="navbar navbar-expand-md navbar-sey sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Sey-Journal</a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end mt-3 mt-md-0" id="navbarNav">
                <ul class="navbar-nav nav-pill-group">
                    <li class="nav-item">
                        <a class="nav-link active" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
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

    <!-- 2. HERO SECTION -->
    <header id="beranda" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h1 class="hero-heading">
                        <?= htmlspecialchars($profile['greeting_title'] ?? 'Selamat datang! Mari menyelam kedalam isi kepalaku!') ?>
                    </h1>
                    <p class="hero-lead">
                        <?= htmlspecialchars($profile['greeting_desc'] ?? 'Ruang kecil tempat menyimpan jejak langkah, catatan perjalanan, serta berbagai pemikiran yang singgah di kepala.') ?>
                    </p>
                </div>
                <div class="col-md-5 text-center">
                    <div class="profile-circle-wrap">
                        <!-- Pastikan file foto profil ada di folder assets/img/profil.jpg -->
                        <img src="<?= htmlspecialchars($profile['profile_image'] ?? 'assets/img/profil.jpg') ?>" 
                             alt="Foto Profil Sey" 
                             class="profile-circle"
                             onerror="this.src='https://placehold.co/400x400/E8DFD1/4D7658?text=Sey'">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container">

        <!-- 3. SECTION: KEGIATAN & BERBAGI CERITA (BISA DIEDIT LANGSUNG DI SINI) -->
        <section id="kegiatan" class="py-4">
            <div class="section-header">
                <div>
                    <span class="section-eyebrow">KEGIATANKU</span>
                    <h2 class="section-title">Kegiatan & Berbagi Cerita</h2>
                </div>
                <a href="kegiatan.php" class="link-all">Lihat semua <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-4">
                <?php
                // Silakan ubah foto, kategori, judul, deskripsi, dan ID detail kegiatan di bawah ini:
                $home_activities = [
                    [
                        'id'       => 1,
                        'category' => 'WORKSHOP',
                        'title'    => 'Workshop UI/UX Design Berkelanjutan - Bandung Design Hub',
                        'excerpt'  => 'Eksplorasi metodologi perancangan antarmuka rendah karbon (low-carbon web design), efisiensi aset, dan estetika minimalis.',
                        'image'    => 'assets/img/kegiatan1.jpg' // Simpan foto kegiatan 1 di folder assets/img/
                    ],
                    [
                        'id'       => 2,
                        'category' => 'PROYEK',
                        'title'    => 'Pengembangan Sistem Desain Ekosistem Hijau',
                        'excerpt'  => 'Membangun komponen UI token library multi-platform untuk startup agritech berkelanjutan dengan fokus pada aksesibilitas.',
                        'image'    => 'assets/img/kegiatan2.jpg' // Simpan foto kegiatan 2 di folder assets/img/
                    ],
                    [
                        'id'       => 3,
                        'category' => 'PEMBICARA',
                        'title'    => 'Pembicara Tamu: Menghidupkan Portofolio Digital di Era AI',
                        'excerpt'  => 'Membahas diferensiasi desainer melalui autentisitas visual, sentuhan craft tipografi, dan narasi personal.',
                        'image'    => 'assets/img/kegiatan3.jpg' // Simpan foto kegiatan 3 di folder assets/img/
                    ]
                ];

                foreach ($home_activities as $act) :
                ?>
                <div class="col-md-4">
                    <article class="sey-card">
                        <!-- Foto bisa diklik menuju halaman detail -->
                        <a href="detail.php?type=activity&id=<?= $act['id'] ?>">
                            <img src="<?= htmlspecialchars($act['image']) ?>" 
                                 alt="<?= htmlspecialchars($act['title']) ?>" 
                                 class="sey-card-img"
                                 onerror="this.src='https://placehold.co/600x400/E2DBD0/5C615D?text=Foto+Kegiatan'">
                        </a>
                        <div class="sey-card-body">
                            <span class="card-tag"><?= htmlspecialchars($act['category']) ?></span>
                            <h3 class="card-title-serif">
                                <a href="detail.php?type=activity&id=<?= $act['id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($act['title']) ?>
                                </a>
                            </h3>
                            <p class="card-excerpt"><?= htmlspecialchars($act['excerpt']) ?></p>
                            <a href="detail.php?type=activity&id=<?= $act['id'] ?>" class="card-read-more">
                                Baca cerita <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 4. SECTION: PERIHAL AKU, ISI KEPALAKU, DAN TULISANKU (BISA DIEDIT LANGSUNG DI SINI) -->
        <section id="tulisan" class="py-5">
            <div class="section-header">
                <div>
                    <span class="section-eyebrow">ISI KEPALAKU</span>
                    <h2 class="section-title">Perihal aku, isi kepalaku, dan tulisanku</h2>
                </div>
                <a href="artikel.php" class="link-all">Semua tulisan <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-4">
                <?php
                // Silakan ubah kategori, judul, ringkasan, dan ID artikel di bawah ini:
                $home_articles = [
                    [
                        'id'      => 1,
                        'tag'     => 'ESAI DESAIN',
                        'title'   => 'Degrowth dalam Desain Web: Mengapa Website Anda Tidak Butuh 15MB Javascript',
                        'excerpt' => 'Menelaah dampak arsitektur SPA modern terhadap konsumsi energi baterai ponsel dan server, serta komparasi estetika web berbasis vanilla HTML/CSS yang tetap elegan.'
                    ],
                    [
                        'id'      => 2,
                        'tag'     => 'REFLEKSI',
                        'title'   => 'Menemukan Kembali Nilai Kerajinan Tangan dalam Era Generative AI',
                        'excerpt' => 'Ketika citra visual dapat diproduksi seketika dalam hitungan detik, tekstur tak sempurna, sentuhan fisik, dan jejak waktu justru menjadi mata uang kemewahan baru.'
                    ],
                    [
                        'id'      => 3,
                        'tag'     => 'TIPOGRAFI',
                        'title'   => 'Studi Tipografi Vernakular: Menghidupkan Kembali Karakter Aksara Nusantara di Web Modern',
                        'excerpt' => 'Menjembatani keanggunan lengkungan aksara tradisional ke dalam font variabel modern untuk antarmuka digital yang berakar pada identitas budaya.'
                    ]
                ];

                foreach ($home_articles as $art) :
                ?>
                <div class="col-md-4">
                    <article class="sey-card">
                        <div class="sey-card-body">
                            <span class="card-tag"><?= htmlspecialchars($art['tag']) ?></span>
                            <h3 class="card-title-serif">
                                <a href="detail-artikel.php?type=article&id=<?= $art['id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($art['title']) ?>
                                </a>
                            </h3>
                            <p class="card-excerpt"><?= htmlspecialchars($art['excerpt']) ?></p>
                            <a href="detail-artikel.php?type=article&id=<?= $art['id'] ?>" class="card-read-more">
                                Selengkapnya <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 5. SECTION: TEMUKAN SAYA DI MEDIA SOSIAL -->
        <section id="kontak">
            <div class="social-box">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <span class="section-eyebrow">Mari Terhubung denganku!</span>
                        <h2 class="section-title mb-3">Temukan Aku di Media Sosial</h2>
                        <p class="text-muted mb-4" style="font-size: 0.92rem;">
                            Punya topik diskusi, pertanyaan, atau sekadar ingin bertegur sapa? Kamu bisa menemukanku di beberapa platform berikut.
                        </p>
                    </div>

                    <div class="col-lg-6">
                        <?php
                        $socials = [
                            [
                                'platform' => 'Instagram',
                                'handle'   => '@syakillazhr.purwana',
                                'url'      => 'https://instagram.com/syakillazhr.purwana'
                            ],
                            [
                                'platform' => 'Substack',
                                'handle'   => '@seykilla | Substack',
                                'url'      => 'https://substack.com/@seykilla'
                            ],
                            [
                                'platform' => 'Tiktok',
                                'handle'   => '@syakilla.purwana',
                                'url'      => 'https://www.tiktok.com/@syakilla.purwana'
                            ],
                            [
                                'platform' => 'Github',
                                'handle'   => '@putrisyakillasi',
                                'url'      => 'https://github.com/putrisyakillasi'
                            ]
                        ];

                        foreach ($socials as $soc) :
                        ?>
                        <a href="<?= htmlspecialchars($soc['url']) ?>" target="_blank" class="social-item-btn">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold" style="font-size: 0.92rem;"><?= htmlspecialchars($soc['platform']) ?></span>
                                <span class="social-badge"><?= htmlspecialchars($soc['handle']) ?></span>
                            </div>
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- 6. FOOTER -->
    <footer class="footer-sey">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <span class="font-serif fw-bold text-dark">Sey-Journal</span>
            <span>&copy; <?= date('Y') ?> Sey-Journal. when the stars appeared in February, we prayed.</span>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>