<?php
$page_title = 'Peminjaman Buku';
include 'includes/header.php';

// Ambil data anggota & buku
$anggota = $pdo->query("SELECT * FROM anggota WHERE status='aktif' ORDER BY nama")->fetchAll();
$buku = $pdo->query("SELECT * FROM buku WHERE jumlah_tersedia > 0 ORDER BY judul")->fetchAll();

// Handle CRUD
if ($_POST) {
    $action = $_POST['action'] ?? '';
    if ($action == 'add') {
        $kode_pinjam = generateCode('PJ', 'peminjaman', 'kode_pinjam');
        $anggota_id = $_POST['anggota_id'];
        $buku_id = $_POST['buku_id'];
        $tanggal_pinjam = $_POST['tanggal_pinjam'];
        $tanggal_kembali_rencana = $_POST['tanggal_kembali_rencana'];
        $lama_pinjam = $_POST['lama_pinjam'];
        $petugas_pinjam = $_SESSION['username'];

        // Insert peminjaman
        $stmt = $pdo->prepare("INSERT INTO peminjaman (kode_pinjam, anggota_id, buku_id, tanggal_pinjam, tanggal_kembali_rencana, lama_pinjam, petugas_pinjam) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$kode_pinjam, $anggota_id, $buku_id, $tanggal_pinjam, $tanggal_kembali_rencana, $lama_pinjam, $petugas_pinjam])) {
            // Update stok buku
            $pdo->prepare("UPDATE buku SET jumlah_tersedia = jumlah_tersedia - 1 WHERE id=?")->execute([$buku_id]);
            $success = "Peminjaman berhasil!";
        }
    }
    if ($action == 'delete') {
        $id = $_POST['id'];
        // Kembalikan stok buku
        $buku_id = $pdo->query("SELECT buku_id FROM peminjaman WHERE id=$id")->fetchColumn();
        $pdo->prepare("UPDATE buku SET jumlah_tersedia = jumlah_tersedia + 1 WHERE id=?")->execute([$buku_id]);
        // Hapus peminjaman
        $pdo->prepare("DELETE FROM peminjaman WHERE id=?")->execute([$id]);
        $success = "Data peminjaman dihapus!";
    }
}

// Tabel peminjaman aktif
$stmt = $pdo->query("SELECT p.*, a.nama, b.judul FROM peminjaman p 
    JOIN anggota a ON p.anggota_id=a.id 
    JOIN buku b ON p.buku_id=b.id 
    WHERE p.status='dipinjam' ORDER BY p.tanggal_pinjam DESC");
$pinjam = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-hand-holding"></i> Peminjaman Buku</h2>
        <nav class="breadcrumb-custom">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Peminjaman</li>
            </ol>
        </nav>
    </div>
</div>

<?php if (isset($success)): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card-custom">
            <div class="card-header-custom"><i class="fas fa-plus"></i> Tambah Peminjaman</div>
            <div class="card-body-custom">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label>Anggota</label>
                        <select name="anggota_id" class="form-control form-control-custom" required>
                            <option value="">Pilih Anggota</option>
                            <?php foreach($anggota as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nama']) ?> (<?= $a['nomor_anggota'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Buku</label>
                        <select name="buku_id" class="form-control form-control-custom" required>
                            <option value="">Pilih Buku</option>
                            <?php foreach($buku as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['judul']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" class="form-control form-control-custom" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Lama Pinjam (hari)</label>
                        <input type="number" name="lama_pinjam" class="form-control form-control-custom" value="7" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal Kembali (rencana)</label>
                        <input type="date" name="tanggal_kembali_rencana" class="form-control form-control-custom" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-4">
        <div class="card-custom">
            <div class="card-header-custom"><i class="fas fa-list"></i> Daftar Peminjaman Aktif</div>
            <div class="card-body-custom">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($pinjam as $i => $p): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= htmlspecialchars($p['nama']) ?></td>
                                <td><?= htmlspecialchars($p['judul']) ?></td>
                                <td><?= $p['tanggal_pinjam'] ?></td>
                                <td><?= $p['tanggal_kembali_rencana'] ?></td>
                                <td>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>