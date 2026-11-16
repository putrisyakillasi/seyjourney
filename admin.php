<?php
session_start();
require_once 'koneksi.php';

// Proteksi halaman: wajib login terlebih dahulu
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$notif = '';

// 1. Proses Simpan Pengaturan Welcome Page (index.php)
if (isset($_POST['save_welcome'])) {
    $hero_title    = mysqli_real_escape_string($conn, $_POST['hero_title']);
    $hero_subtitle = mysqli_real_escape_string($conn, $_POST['hero_subtitle']);
    $button_text   = mysqli_real_escape_string($conn, $_POST['button_text']);
    mysqli_query($conn, "UPDATE site_settings SET hero_title='$hero_title', hero_subtitle='$hero_subtitle', button_text='$button_text' WHERE id=1");
    $notif = 'Pengaturan Welcome Page berhasil diperbarui!';
}

// 2. Proses Simpan Profil Home (home.php)
if (isset($_POST['save_profile'])) {
    $greeting_title = mysqli_real_escape_string($conn, $_POST['greeting_title']);
    $greeting_desc  = mysqli_real_escape_string($conn, $_POST['greeting_desc']);
    $profile_image  = mysqli_real_escape_string($conn, $_POST['profile_image']);
    mysqli_query($conn, "UPDATE profile_info SET greeting_title='$greeting_title', greeting_desc='$greeting_desc', profile_image='$profile_image' WHERE id=1");
    $notif = 'Profil halaman Home berhasil diperbarui!';
}

// 3. Proses Simpan Halaman About (about.php)
if (isset($_POST['save_about'])) {
    $t_dark   = mysqli_real_escape_string($conn, $_POST['page_title_dark']);
    $t_green  = mysqli_real_escape_string($conn, $_POST['page_title_green']);
    $f_name   = mysqli_real_escape_string($conn, $_POST['full_name']);
    $p1       = mysqli_real_escape_string($conn, $_POST['paragraph_1']);
    $p2       = mysqli_real_escape_string($conn, $_POST['paragraph_2']);
    $p3       = mysqli_real_escape_string($conn, $_POST['paragraph_3']);
    $a_img    = mysqli_real_escape_string($conn, $_POST['about_image']);
    mysqli_query($conn, "UPDATE about_page SET page_title_dark='$t_dark', page_title_green='$t_green', full_name='$f_name', paragraph_1='$p1', paragraph_2='$p2', paragraph_3='$p3', about_image='$a_img' WHERE id=1");
    $notif = 'Konten halaman About berhasil diperbarui!';
}

// 4. Tambah / Hapus Kegiatan
if (isset($_POST['add_activity'])) {
    $cat   = mysqli_real_escape_string($conn, $_POST['category']);
    $loc   = mysqli_real_escape_string($conn, $_POST['location']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $exc   = mysqli_real_escape_string($conn, $_POST['excerpt']);
    $img   = mysqli_real_escape_string($conn, $_POST['image']);
    $date  = mysqli_real_escape_string($conn, $_POST['event_date']);
    $quote = mysqli_real_escape_string($conn, $_POST['quote_text']);
    $c1    = mysqli_real_escape_string($conn, $_POST['content_1']);
    mysqli_query($conn, "INSERT INTO activities (category, location, title, excerpt, image, event_date, quote_text, content_1) VALUES ('$cat', '$loc', '$title', '$exc', '$img', '$date', '$quote', '$c1')");
    $notif = 'Kegiatan baru berhasil ditambahkan!';
}
if (isset($_GET['del_activity'])) {
    $delId = (int) $_GET['del_activity'];
    mysqli_query($conn, "DELETE FROM activities WHERE id=$delId");
    header("Location: admin.php?tab=kegiatan&msg=deleted");
    exit;
}

// 5. Tambah / Hapus Artikel
if (isset($_POST['add_article'])) {
    $tag     = mysqli_real_escape_string($conn, $_POST['tag']);
    $title   = mysqli_real_escape_string($conn, $_POST['title']);
    $exc     = mysqli_real_escape_string($conn, $_POST['excerpt']);
    $img     = mysqli_real_escape_string($conn, $_POST['image']);
    $caption = mysqli_real_escape_string($conn, $_POST['image_caption']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $date    = mysqli_real_escape_string($conn, $_POST['published_at']);
    mysqli_query($conn, "INSERT INTO articles (tag, author, title, excerpt, image, image_caption, content, published_at) VALUES ('$tag', 'Sey', '$title', '$exc', '$img', '$caption', '$content', '$date')");
    $notif = 'Artikel baru berhasil diterbitkan!';
}
if (isset($_GET['del_article'])) {
    $delId = (int) $_GET['del_article'];
    mysqli_query($conn, "DELETE FROM articles WHERE id=$delId");
    header("Location: admin.php?tab=artikel&msg=deleted");
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $notif = 'Data berhasil dihapus.';
}

$activeTab = $_GET['tab'] ?? 'kegiatan';
$welcome   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM site_settings LIMIT 1"));
$profile   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM profile_info LIMIT 1"));
$about     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_page LIMIT 1"));
$qActs     = mysqli_query($conn, "SELECT * FROM activities ORDER BY event_date DESC");
$qArts     = mysqli_query($conn, "SELECT * FROM articles ORDER BY published_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Kurasi — Admin sey-journal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-cream: #FAF7F2;
            --card-bg: #F3EFEA;
            --border-soft: #E6E0D6;
            --text-dark: #252725;
            --accent-green: #4D7658;
        }
        body { background-color: var(--bg-cream); color: var(--text-dark); font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Lora', serif; }
        .admin-nav { background: #FFFFFF; border-bottom: 1px solid var(--border-soft); padding: 1rem 0; }
        .studio-card { background: #FFFFFF; border: 1px solid var(--border-soft); border-radius: 18px; padding: 1.75rem; }
        .tab-btn { border-radius: 999px; font-size: 0.84rem; font-weight: 500; padding: 0.45rem 1.1rem; text-decoration: none; color: #5C615D; background: var(--card-bg); border: 1px solid var(--border-soft); }
        .tab-btn.active { background: var(--accent-green); color: #FFFFFF; border-color: var(--accent-green); }
        .btn-green { background: var(--accent-green); color: #fff; border: none; border-radius: 999px; padding: 0.55rem 1.4rem; font-size: 0.85rem; font-weight: 600; }
        .btn-green:hover { background: #3D6046; color: #fff; }
    </style>
</head>
<body>

<nav class="admin-nav mb-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="font-serif fw-bold fs-5">sey-journal</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle">Studio Kurasi</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="home.php" target="_blank" class="text-decoration-none text-muted small"><i class="bi bi-box-arrow-up-right me-1"></i>Lihat Website</a>
            <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Keluar</a>
        </div>
    </div>
</nav>

<main class="container pb-5">
    <?php if ($notif) : ?>
        <div class="alert alert-success rounded-4 py-2 px-3 small"><i class="bi bi-check-circle me-1"></i> <?= htmlspecialchars($notif) ?></div>
    <?php endif; ?>

    <!-- Navigasi Tab Semua Halaman -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="?tab=kegiatan" class="tab-btn <?= $activeTab === 'kegiatan' ? 'active' : '' ?>"><i class="bi bi-camera me-1"></i> Kelola Kegiatan</a>
        <a href="?tab=artikel" class="tab-btn <?= $activeTab === 'artikel' ? 'active' : '' ?>"><i class="bi bi-journal-text me-1"></i> Kelola Artikel</a>
        <a href="?tab=about" class="tab-btn <?= $activeTab === 'about' ? 'active' : '' ?>"><i class="bi bi-person me-1"></i> Edit Halaman About</a>
        <a href="?tab=home" class="tab-btn <?= $activeTab === 'home' ? 'active' : '' ?>"><i class="bi bi-house me-1"></i> Edit Profil Home</a>
        <a href="?tab=welcome" class="tab-btn <?= $activeTab === 'welcome' ? 'active' : '' ?>"><i class="bi bi-stars me-1"></i> Edit Welcome Page</a>
    </div>

    <?php if ($activeTab === 'kegiatan') : ?>
    <!-- TAB KEGIATAN -->
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="studio-card">
                <h2 class="font-serif h5 fw-bold mb-3">Tambah Dokumentasi Kegiatan</h2>
                <form method="POST">
                    <div class="mb-2"><label class="small fw-semibold">Judul Kegiatan</label><input type="text" name="title" class="form-control form-control-sm" required></div>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><label class="small fw-semibold">Kategori</label><input type="text" name="category" class="form-control form-control-sm" placeholder="WORKSHOP" required></div>
                        <div class="col-6"><label class="small fw-semibold">Tanggal</label><input type="date" name="event_date" class="form-control form-control-sm" required></div>
                    </div>
                    <div class="mb-2"><label class="small fw-semibold">Lokasi / Platform</label><input type="text" name="location" class="form-control form-control-sm" placeholder="Bandung Design Hub, Offline" required></div>
                    <div class="mb-2"><label class="small fw-semibold">Path Gambar</label><input type="text" name="image" class="form-control form-control-sm" value="assets/img/kegiatan1.jpg" required></div>
                    <div class="mb-2"><label class="small fw-semibold">Ringkasan Singkat</label><textarea name="excerpt" rows="2" class="form-control form-control-sm" required></textarea></div>
                    <div class="mb-2"><label class="small fw-semibold">Kutipan (Quote)</label><input type="text" name="quote_text" class="form-control form-control-sm"></div>
                    <div class="mb-3"><label class="small fw-semibold">Cerita Lengkap</label><textarea name="content_1" rows="3" class="form-control form-control-sm"></textarea></div>
                    <button type="submit" name="add_activity" class="btn-green w-100">Simpan Kegiatan</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="studio-card">
                <h2 class="font-serif h5 fw-bold mb-3">Daftar Kegiatan Tercatat</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle small">
                        <thead><tr><th>Judul</th><th>Lokasi</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php while ($a = mysqli_fetch_assoc($qActs)) : ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($a['title']) ?></td>
                                <td><?= htmlspecialchars($a['location']) ?></td>
                                <td><?= htmlspecialchars($a['event_date']) ?></td>
                                <td><a href="?tab=kegiatan&del_activity=<?= $a['id'] ?>" onclick="return confirm('Hapus kegiatan ini?')" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($activeTab === 'artikel') : ?>
    <!-- TAB ARTIKEL -->
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="studio-card">
                <h2 class="font-serif h5 fw-bold mb-3">Tulis Artikel Baru</h2>
                <form method="POST">
                    <div class="mb-2"><label class="small fw-semibold">Judul Artikel</label><input type="text" name="title" class="form-control form-control-sm" required></div>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><label class="small fw-semibold">Tag / Topik</label><input type="text" name="tag" class="form-control form-control-sm" placeholder="ESAI DESAIN" required></div>
                        <div class="col-6"><label class="small fw-semibold">Tanggal Terbit</label><input type="date" name="published_at" class="form-control form-control-sm" required></div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><label class="small fw-semibold">Path Gambar</label><input type="text" name="image" class="form-control form-control-sm" value="assets/img/artikel1.jpg"></div>
                        <div class="col-6"><label class="small fw-semibold">Caption Foto</label><input type="text" name="image_caption" class="form-control form-control-sm" value="Yogyakarta, 2026"></div>
                    </div>
                    <div class="mb-2"><label class="small fw-semibold">Subjudul / Ringkasan</label><textarea name="excerpt" rows="2" class="form-control form-control-sm" required></textarea></div>
                    <div class="mb-3"><label class="small fw-semibold">Isi Artikel Lengkap</label><textarea name="content" rows="5" class="form-control form-control-sm" required></textarea></div>
                    <button type="submit" name="add_article" class="btn-green w-100">Terbitkan Artikel</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="studio-card">
                <h2 class="font-serif h5 fw-bold mb-3">Daftar Artikel Diterbitkan</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle small">
                        <thead><tr><th>Judul</th><th>Tag</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php while ($art = mysqli_fetch_assoc($qArts)) : ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($art['title']) ?></td>
                                <td><?= htmlspecialchars($art['tag']) ?></td>
                                <td><?= htmlspecialchars($art['published_at']) ?></td>
                                <td><a href="?tab=artikel&del_article=<?= $art['id'] ?>" onclick="return confirm('Hapus artikel ini?')" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($activeTab === 'about') : ?>
    <!-- TAB ABOUT -->
    <div class="studio-card" style="max-width: 760px;">
        <h2 class="font-serif h5 fw-bold mb-3">Edit Konten Halaman About</h2>
        <form method="POST">
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="small fw-semibold">Judul Utama (Hitam)</label><input type="text" name="page_title_dark" class="form-control form-control-sm" value="<?= htmlspecialchars($about['page_title_dark'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="small fw-semibold">Judul Italic (Hijau)</label><input type="text" name="page_title_green" class="form-control form-control-sm" value="<?= htmlspecialchars($about['page_title_green'] ?? '') ?>"></div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="small fw-semibold">Nama Lengkap</label><input type="text" name="full_name" class="form-control form-control-sm" value="<?= htmlspecialchars($about['full_name'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="small fw-semibold">Path Foto About</label><input type="text" name="about_image" class="form-control form-control-sm" value="<?= htmlspecialchars($about['about_image'] ?? '') ?>"></div>
            </div>
            <div class="mb-2"><label class="small fw-semibold">Paragraf 1</label><textarea name="paragraph_1" rows="3" class="form-control form-control-sm"><?= htmlspecialchars($about['paragraph_1'] ?? '') ?></textarea></div>
            <div class="mb-2"><label class="small fw-semibold">Paragraf 2</label><textarea name="paragraph_2" rows="3" class="form-control form-control-sm"><?= htmlspecialchars($about['paragraph_2'] ?? '') ?></textarea></div>
            <div class="mb-3"><label class="small fw-semibold">Paragraf 3</label><textarea name="paragraph_3" rows="2" class="form-control form-control-sm"><?= htmlspecialchars($about['paragraph_3'] ?? '') ?></textarea></div>
            <button type="submit" name="save_about" class="btn-green">Simpan Perubahan About</button>
        </form>
    </div>

    <?php elseif ($activeTab === 'home') : ?>
    <!-- TAB PROFIL HOME -->
    <div class="studio-card" style="max-width: 650px;">
        <h2 class="font-serif h5 fw-bold mb-3">Edit Hero &amp; Profil Halaman Home</h2>
        <form method="POST">
            <div class="mb-3"><label class="small fw-semibold">Judul Sapaan Utama</label><input type="text" name="greeting_title" class="form-control form-control-sm" value="<?= htmlspecialchars($profile['greeting_title'] ?? '') ?>"></div>
            <div class="mb-3"><label class="small fw-semibold">Deskripsi Pengantar</label><textarea name="greeting_desc" rows="3" class="form-control form-control-sm"><?= htmlspecialchars($profile['greeting_desc'] ?? '') ?></textarea></div>
            <div class="mb-3"><label class="small fw-semibold">Path Foto Profil Lingkaran</label><input type="text" name="profile_image" class="form-control form-control-sm" value="<?= htmlspecialchars($profile['profile_image'] ?? '') ?>"></div>
            <button type="submit" name="save_profile" class="btn-green">Simpan Perubahan Home</button>
        </form>
    </div>

    <?php elseif ($activeTab === 'welcome') : ?>
    <!-- TAB WELCOME PAGE -->
    <div class="studio-card" style="max-width: 650px;">
        <h2 class="font-serif h5 fw-bold mb-3">Edit Pengaturan Welcome Page (index.php)</h2>
        <form method="POST">
            <div class="mb-3"><label class="small fw-semibold">Judul Utama</label><input type="text" name="hero_title" class="form-control form-control-sm" value="<?= htmlspecialchars($welcome['hero_title'] ?? '') ?>"></div>
            <div class="mb-3"><label class="small fw-semibold">Subjudul / Kutipan</label><input type="text" name="hero_subtitle" class="form-control form-control-sm" value="<?= htmlspecialchars($welcome['hero_subtitle'] ?? '') ?>"></div>
            <div class="mb-3"><label class="small fw-semibold">Teks Tombol</label><input type="text" name="button_text" class="form-control form-control-sm" value="<?= htmlspecialchars($welcome['button_text'] ?? '') ?>"></div>
            <button type="submit" name="save_welcome" class="btn-green">Simpan Welcome Page</button>
        </form>
    </div>
    <?php endif; ?>
</main>

</body>
</html>