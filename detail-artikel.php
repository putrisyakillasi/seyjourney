<?php
require_once 'koneksi.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 1;

// Helper format tanggal (contoh: 18 Mei 2024)
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

$q    = mysqli_query($conn, "SELECT * FROM articles WHERE id = $id LIMIT 1");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    header("Location: artikel.php");
    exit;
}

$title        = $data['title'] ?? '';
$subtitle     = $data['excerpt'] ?? '';
$author       = $data['author'] ?? 'Sey';
$dateText     = formatFullDate($data['published_at'] ?? '');
$heroImg      = $data['image'] ?? 'assets/img/artikel1.jpg';
$imageCaption = $data['image_caption'] ?? 'Yogyakarta, 2024';
$audioFile    = $data['audio_file'] ?? '';
$content      = $data['content'] ?? $subtitle;
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
            --accent-green-hover: #3D6046;
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
        .article-reader {
            max-width: 720px;
            margin: 0 auto;
            padding: 1.5rem 1rem 4.5rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.84rem;
            font-weight: 500;
            margin-bottom: 1.85rem;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .btn-back:hover {
            color: var(--accent-green);
            transform: translateX(-3px);
        }

        .article-headline {
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: clamp(2rem, 4.5vw, 2.85rem);
            line-height: 1.18;
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
        }

        .article-lead {
            font-size: 0.96rem;
            color: var(--text-muted);
            line-height: 1.65;
            margin-bottom: 1.75rem;
        }

        /* Audio Player Card ("Dengarkan Versi Audio") */
        .audio-player-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .audio-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .btn-play-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--accent-green);
            color: #FFFFFF;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.15s ease;
            flex-shrink: 0;
        }

        .btn-play-circle:hover {
            background-color: var(--accent-green-hover);
            transform: scale(1.05);
        }

        .audio-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-dark);
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .audio-label i {
            color: var(--accent-green);
            font-size: 1rem;
        }

        .audio-time {
            font-size: 0.74rem;
            color: var(--text-muted);
            font-variant-numeric: tabular-nums;
        }

        .audio-progress-wrap {
            width: 100%;
            height: 5px;
            background-color: #E0D9CE;
            border-radius: 999px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .audio-progress-fill {
            height: 100%;
            width: 24%;
            background-color: var(--accent-green);
            border-radius: 999px;
            transition: width 0.15s linear;
        }

        /* Author & Share Bar */
        .author-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0 1.5rem;
        }

        .author-name {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-dark);
            display: block;
            line-height: 1.3;
        }

        .publish-date {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .btn-share-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--card-bg);
            border: 1px solid var(--border-soft);
            color: var(--text-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-share-circle:hover {
            background-color: var(--card-white);
            border-color: var(--accent-green);
            color: var(--accent-green);
        }

        /* Gambar Utama Artikel */
        .article-figure {
            margin-bottom: 2.25rem;
        }

        .article-figure img {
            width: 100%;
            max-height: 430px;
            object-fit: cover;
            border-radius: 16px;
            background-color: #DFD8CC;
        }

        .article-caption {
            font-size: 0.74rem;
            color: var(--text-muted);
            margin-top: 0.6rem;
        }

        /* Isi Artikel dengan Drop Cap */
        .editorial-content p {
            font-size: 0.96rem;
            color: var(--text-muted);
            line-height: 1.88;
            margin-bottom: 1.45rem;
        }

        .editorial-content p:first-of-type::first-letter {
            font-family: 'Lora', serif;
            float: left;
            font-size: 3.35rem;
            line-height: 0.85;
            padding-top: 4px;
            padding-right: 10px;
            padding-left: 2px;
            font-weight: 700;
            color: var(--accent-green);
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

    <!-- KONTEN BACA ARTIKEL -->
    <main class="container">
        <article class="article-reader">
            <!-- Tombol Kembali -->
            <a href="javascript:history.back()" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>

            <!-- Judul & Ringkasan -->
            <h1 class="article-headline"><?= htmlspecialchars($title) ?></h1>
            <p class="article-lead"><?= htmlspecialchars($subtitle) ?></p>

            <!-- Audio Player Card -->
            <div class="audio-player-card">
                <div class="audio-top-row">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn-play-circle" id="playPauseBtn" aria-label="Putar Audio">
                            <i class="bi bi-play-fill" id="playPauseIcon"></i>
                        </button>
                        <span class="audio-label">
                            <i class="bi bi-soundwave"></i>
                        </span>
                    </div>
                    <span class="audio-time" id="audioTimeDisplay">01:24 / 05:42</span>
                </div>

                <div class="audio-progress-wrap" id="progressContainer">
                    <div class="audio-progress-fill" id="progressFill"></div>
                </div>

                <?php if (!empty($audioFile)) : ?>
                    <audio id="articleAudio" src="<?= htmlspecialchars($audioFile) ?>" preload="metadata"></audio>
                <?php endif; ?>
            </div>

            <!-- Bar Penulis & Tombol Share -->
            <div class="author-bar">
                <div>
                    <span class="author-name"><?= htmlspecialchars($author) ?></span>
                    <span class="publish-date"><?= htmlspecialchars($dateText) ?></span>
                </div>

                <button type="button" class="btn-share-circle" id="shareBtn" title="Salin tautan artikel">
                    <i class="bi bi-share"></i>
                </button>
            </div>

            <!-- Gambar Utama & Caption Lokasi/Tahun -->
            <?php if ($heroImg) : ?>
            <figure class="article-figure">
                <img src="<?= htmlspecialchars($heroImg) ?>" 
                     alt="<?= htmlspecialchars($title) ?>"
                     onerror="this.src='https://placehold.co/900x520/DFD8CC/4D7658?text=Ilustrasi+Artikel'">
                <?php if ($imageCaption) : ?>
                    <figcaption class="article-caption"><?= htmlspecialchars($imageCaption) ?></figcaption>
                <?php endif; ?>
            </figure>
            <?php endif; ?>

            <!-- Isi Artikel dengan Drop Cap -->
            <div class="editorial-content">
                <?php
                $paragraphs = preg_split('/\r\n|\r|\n/', trim($content));
                foreach ($paragraphs as $p) {
                    if (trim($p) !== '') {
                        echo '<p>' . htmlspecialchars($p) . '</p>';
                    }
                }
                ?>
            </div>
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

    <!-- Interaksi Audio Player & Tombol Share -->
    <script>
        const playBtn = document.getElementById('playPauseBtn');
        const playIcon = document.getElementById('playPauseIcon');
        const progressFill = document.getElementById('progressFill');
        const progressContainer = document.getElementById('progressContainer');
        const timeDisplay = document.getElementById('audioTimeDisplay');
        const realAudio = document.getElementById('articleAudio');

        let isPlaying = false;
        let simSeconds = 84; // 01:24
        const simTotal = 342; // 05:42
        let simTimer = null;

        function formatTime(sec) {
            const m = String(Math.floor(sec / 60)).padStart(2, '0');
            const s = String(Math.floor(sec % 60)).padStart(2, '0');
            return `${m}:${s}`;
        }

        if (realAudio) {
            realAudio.addEventListener('loadedmetadata', () => {
                timeDisplay.textContent = `00:00 / ${formatTime(realAudio.duration)}`;
                progressFill.style.width = '0%';
            });

            realAudio.addEventListener('timeupdate', () => {
                const pct = (realAudio.currentTime / realAudio.duration) * 100;
                progressFill.style.width = `${pct}%`;
                timeDisplay.textContent = `${formatTime(realAudio.currentTime)} / ${formatTime(realAudio.duration)}`;
            });

            playBtn.addEventListener('click', () => {
                if (realAudio.paused) {
                    realAudio.play();
                    playIcon.className = 'bi bi-pause-fill';
                } else {
                    realAudio.pause();
                    playIcon.className = 'bi bi-play-fill';
                }
            });

            progressContainer.addEventListener('click', (e) => {
                const rect = progressContainer.getBoundingClientRect();
                const ratio = (e.clientX - rect.left) / rect.width;
                realAudio.currentTime = ratio * realAudio.duration;
            });
        } else {
            // Mode simulasi interaktif jika file MP3 belum diisi
            playBtn.addEventListener('click', () => {
                isPlaying = !isPlaying;
                playIcon.className = isPlaying ? 'bi bi-pause-fill' : 'bi bi-play-fill';
                if (isPlaying) {
                    simTimer = setInterval(() => {
                        if (simSeconds < simTotal) {
                            simSeconds++;
                            progressFill.style.width = `${(simSeconds / simTotal) * 100}%`;
                            timeDisplay.textContent = `${formatTime(simSeconds)} / ${formatTime(simTotal)}`;
                        } else {
                            clearInterval(simTimer);
                            isPlaying = false;
                            playIcon.className = 'bi bi-play-fill';
                        }
                    }, 1000);
                } else {
                    clearInterval(simTimer);
                }
            });

            progressContainer.addEventListener('click', (e) => {
                const rect = progressContainer.getBoundingClientRect();
                const ratio = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                simSeconds = Math.floor(ratio * simTotal);
                progressFill.style.width = `${ratio * 100}%`;
                timeDisplay.textContent = `${formatTime(simSeconds)} / ${formatTime(simTotal)}`;
            });
        }

        // Salin URL artikel saat tombol Share diklik
        document.getElementById('shareBtn').addEventListener('click', function() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                this.innerHTML = '<i class="bi bi-check2 text-success"></i>';
                setTimeout(() => {
                    this.innerHTML = '<i class="bi bi-share"></i>';
                }, 2000);
            });
        });
    </script>
</body>
</html>