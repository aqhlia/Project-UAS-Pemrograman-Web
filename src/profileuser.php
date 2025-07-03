<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['nomor_anggota'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'user') {
    header("Location: profileuser.php");
    exit;
}

// Ambil data kunjungan berdasarkan nomor anggota
$stmt = $pdo->prepare("SELECT * FROM kunjungan WHERE nomor_anggota = ? ORDER BY tanggal_kunjungan DESC, waktu_masuk DESC LIMIT 1");
$stmt->execute([$_SESSION['nomor_anggota']]);
$kunjungan = $stmt->fetch();

// Set judul halaman
$page_title = "Profil Pengguna";
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-user"></i> Profil Kunjungan Terakhir</h5>
                </div>
                <div class="card-body">
                    <?php if ($kunjungan): ?>
                        <table class="table table-bordered">
                            <tr>
                                <th>Nama Pengunjung</th>
                                <td><?= htmlspecialchars($kunjungan['nama_pengunjung']) ?></td>
                            </tr>
                            <tr>
                                <th>Nomor Anggota</th>
                                <td><?= htmlspecialchars($kunjungan['nomor_anggota']) ?></td>
                            </tr>
                            <tr>
                                <th>Keperluan</th>
                                <td><?= htmlspecialchars($kunjungan['keperluan']) ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Kunjungan</th>
                                <td><?= htmlspecialchars($kunjungan['tanggal_kunjungan']) ?></td>
                            </tr>
                            <tr>
                                <th>Waktu Masuk</th>
                                <td><?= htmlspecialchars($kunjungan['waktu_masuk']) ?></td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning">Belum ada data kunjungan.</div>
                    <?php endif; ?>
                    <a href="dashboard.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
