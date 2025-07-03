<?php
$page_title = 'Data Perpustakaan';

require_once 'includes/function.php';
require_once 'includes/db.php'; // Pastikan $pdo didefinisikan
requireLogin();

$is_admin = isAdmin();
$is_edit_mode = $is_admin && isset($_GET['edit']);

// Proses simpan data (hanya admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    $nama_aplikasi = trim($_POST['nama_aplikasi']);
    $alamat = trim($_POST['alamat']);
    $telepon = trim($_POST['telepon']);
    $email = trim($_POST['email']);
    $kepala_perpustakaan = trim($_POST['kepala_perpustakaan']);

    $stmt = $pdo->query("SELECT COUNT(*) FROM setting_aplikasi");
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $stmt = $pdo->prepare("UPDATE setting_aplikasi SET nama_aplikasi=?, alamat=?, telepon=?, email=?, kepala_perpustakaan=? WHERE id=1");
        $stmt->execute([$nama_aplikasi, $alamat, $telepon, $email, $kepala_perpustakaan]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO setting_aplikasi (nama_aplikasi, alamat, telepon, email, kepala_perpustakaan) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama_aplikasi, $alamat, $telepon, $email, $kepala_perpustakaan]);
    }

    $_SESSION['success'] = "Pengaturan berhasil disimpan.";
    header("Location: data_perpus.php");
    exit;
}

include 'includes/header.php';

// Ambil data
$stmt = $pdo->query("SELECT * FROM setting_aplikasi LIMIT 1");
$settings = $stmt->fetch() ?: [];
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-building"></i> Profil Perpustakaan</h2>
        <nav class="breadcrumb-custom">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Data Perpustakaan</li>
            </ol>
        </nav>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if ($is_admin): ?>
    <?php if ($is_edit_mode): ?>
        <!-- FORM EDIT (KHUSUS ADMIN) -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-cogs"></i> Pengaturan Aplikasi
                    </div>
                    <div class="card-body-custom">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Aplikasi</label>
                                    <input type="text" name="nama_aplikasi" class="form-control" value="<?= htmlspecialchars($settings['nama_aplikasi'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kepala Perpustakaan</label>
                                    <input type="text" name="kepala_perpustakaan" class="form-control" value="<?= htmlspecialchars($settings['kepala_perpustakaan'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($settings['alamat'] ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($settings['telepon'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($settings['email'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Pengaturan
                                </button>
                                <a href="data_perpus.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- TAMPILAN INFORMASI UNTUK ADMIN -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card-custom">
                    <div class="card-header-custom bg-info text-white">
                        <i class="fas fa-info-circle"></i> Informasi Perpustakaan
                    </div>
                    <div class="card-body-custom p-0">
                        <table class="table table-striped mb-0">
                            <tr><th>Nama Aplikasi</th><td><?= htmlspecialchars($settings['nama_aplikasi'] ?? '-') ?></td></tr>
                            <tr><th>Kepala Perpustakaan</th><td><?= htmlspecialchars($settings['kepala_perpustakaan'] ?? '-') ?></td></tr>
                            <tr><th>Alamat</th><td><?= htmlspecialchars($settings['alamat'] ?? '-') ?></td></tr>
                            <tr><th>Telepon</th><td><?= htmlspecialchars($settings['telepon'] ?? '-') ?></td></tr>
                            <tr><th>Email</th><td><?= htmlspecialchars($settings['email'] ?? '-') ?></td></tr>
                        </table>
                    </div>
                    <div class="card-footer-custom text-end">
                        <a href="data_perpus.php?edit=1" class="btn btn-warning"><i class="fas fa-edit"></i> Ubah Data</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <!-- TAMPILAN INFORMASI UNTUK USER BIASA -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="fas fa-info-circle"></i> Informasi Perpustakaan
                </div>
                <div class="card-body-custom">
                    <table class="table">
                        <tr><th>Nama Aplikasi</th><td><?= htmlspecialchars($settings['nama_aplikasi'] ?? '-') ?></td></tr>
                        <tr><th>Kepala Perpustakaan</th><td><?= htmlspecialchars($settings['kepala_perpustakaan'] ?? '-') ?></td></tr>
                        <tr><th>Alamat</th><td><?= htmlspecialchars($settings['alamat'] ?? '-') ?></td></tr>
                        <tr><th>Telepon</th><td><?= htmlspecialchars($settings['telepon'] ?? '-') ?></td></tr>
                        <tr><th>Email</th><td><?= htmlspecialchars($settings['email'] ?? '-') ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
