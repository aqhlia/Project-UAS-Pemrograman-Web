<?php
session_start();
require_once 'config/database.php'; // sesuaikan dengan struktur folder
require_once 'includes/function.php';

requireLogin();

require_once 'includes/header.php'; // dipanggil setelah fungsi tersedia

// Statistik
$stats = [];
$stats['total_buku'] = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$stats['total_anggota'] = $pdo->query("SELECT COUNT(*) FROM anggota WHERE status = 'aktif'")->fetchColumn();
$stats['total_pinjam'] = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn();
$stats['total_terlambat'] = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'terlambat'")->fetchColumn();

// Form kunjungan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_kunjungan'])) {
    $nama_anggota = trim($_POST['nama_anggota'] ?? '');
    $keperluan = $_POST['keperluan'] ?? '';

    // Cek apakah nama sudah terdaftar sebagai anggota
    $stmt = $pdo->prepare("SELECT * FROM anggota WHERE nama = ?");
    $stmt->execute([$nama_anggota]);
    $anggota = $stmt->fetch();

    if ($anggota) {
        // Jika sudah ada, ambil nomor_anggota yang lama
        $nomor_anggota = $anggota['nomor_anggota'];
    } else {
        // Generate nomor_anggota otomatis
        $stmt = $pdo->query("SELECT nomor_anggota FROM anggota ORDER BY nomor_anggota DESC LIMIT 1");
        $last = $stmt->fetchColumn();

        if ($last) {
            $lastNumber = (int)substr($last, 3); // Ambil angka setelah 'AGT'
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $nomor_anggota = 'AGT' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Simpan anggota baru
        $stmt = $pdo->prepare("INSERT INTO anggota (nomor_anggota, nama) VALUES (?, ?)");
        $stmt->execute([$nomor_anggota, $nama_anggota]);
    }

    // Simpan kunjungan
    $stmt = $pdo->prepare("INSERT INTO kunjungan (nomor_anggota, nama_pengunjung, keperluan, tanggal_kunjungan, waktu_masuk) VALUES (?, ?, ?, CURDATE(), CURTIME())");
    $stmt->execute([$nomor_anggota, $nama_anggota, $keperluan]);

    echo "<script>alert('Kunjungan berhasil disimpan dengan nomor anggota $nomor_anggota'); window.location='dashboard.php';</script>";
}
?>


<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
            <nav class="breadcrumb-custom">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><i class="fas fa-home"></i> Home</li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Form Kunjungan -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom h-100">
                <div class="card-header card-header-custom">
                    <i class="fas fa-clipboard-list"></i> Input Data Kunjungan
                </div>
                <div class="card-body card-body-custom">
                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" name="nomor_anggota" class="form-control form-control-custom" placeholder="Nomor Anggota" required>
                        </div>
                        <div class="mb-3">
                            <textarea name="keperluan" class="form-control form-control-custom" placeholder="Keperluan Apa?" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="submit_kunjungan" class="btn btn-custom btn-primary-custom">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Awal -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom h-100">
                <div class="card-header card-header-custom bg-success text-white">
                    <i class="fas fa-database"></i> Data Awal
                </div>
                <div class="card-body card-body-custom d-flex flex-wrap gap-2">
                    <a href="data_awal.php?tab=kategori" class="btn btn-outline-dark w-100">
                        <i class="fas fa-tags"></i> Kategori
                    </a>
                    <a href="data_awal.php?tab=rak" class="btn btn-outline-dark w-100">
                        <i class="fas fa-archive"></i> Rak
                    </a>
                    <a href="data_awal.php?tab=penulis" class="btn btn-outline-dark w-100">
                        <i class="fas fa-user-edit"></i> Penulis
                    </a>
                    <a href="data_awal.php?tab=penerbit" class="btn btn-outline-dark w-100">
                        <i class="fas fa-building"></i> Penerbit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Arus Buku -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom h-100">
                <div class="card-header card-header-custom bg-danger text-white">
                    <i class="fas fa-exchange-alt"></i> Arus Buku
                </div>
                <div class="card-body card-body-custom d-flex flex-wrap gap-2">
                    <a href="peminjaman.php" class="btn btn-outline-dark w-100">
                        <i class="fas fa-hand-holding"></i> Peminjaman
                    </a>
                    <a href="pengembalian.php" class="btn btn-outline-dark w-100">
                        <i class="fas fa-undo"></i> Pengembalian
                    </a>
                    <a href="laporan.php" class="btn btn-outline-dark w-100">
                        <i class="fas fa-chart-bar"></i> Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- Buku dan Anggota -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom h-100">
                <div class="card-header card-header-custom bg-warning text-white">
                    <i class="fas fa-users"></i> Data Buku dan Anggota
                </div>
                <div class="card-body card-body-custom d-flex flex-wrap gap-2">
                    <a href="keanggotaan.php" class="btn btn-outline-dark w-100">
                        <i class="fas fa-users"></i> Anggota
                    </a>
                    <a href="data_buku.php" class="btn btn-outline-dark w-100">
                        <i class="fas fa-book"></i> Data Buku
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number"><?= $stats['total_buku'] ?></div>
                <div class="stats-label"><i class="fas fa-book"></i> Total Buku</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number"><?= $stats['total_anggota'] ?></div>
                <div class="stats-label"><i class="fas fa-users"></i> Anggota Aktif</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number"><?= $stats['total_pinjam'] ?></div>
                <div class="stats-label"><i class="fas fa-hand-holding"></i> Sedang Dipinjam</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number"><?= $stats['total_terlambat'] ?></div>
                <div class="stats-label"><i class="fas fa-exclamation-triangle"></i> Terlambat</div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
