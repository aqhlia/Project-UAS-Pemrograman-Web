<?php
$page_title = 'Pengembalian Buku';
include 'includes/header.php';

// Proses pengembalian
if ($_POST) {
    $action = $_POST['action'] ?? '';
    if ($action == 'return') {
        $id = $_POST['id'];
        $petugas_kembali = $_SESSION['username'];
        $tgl_kembali = date('Y-m-d');
        // Ambil data pinjam
        $data = $pdo->query("SELECT * FROM peminjaman WHERE id=$id")->fetch();
        // Hitung denda
        $terlambat = (strtotime($tgl_kembali) > strtotime($data['tanggal_kembali_rencana']));
        $denda = $terlambat ? (intval((strtotime($tgl_kembali) - strtotime($data['tanggal_kembali_rencana'])) / 86400) * 1000) : 0;
        $status = $terlambat ? 'terlambat' : 'dikembalikan';
        // Update peminjaman
        $pdo->prepare("UPDATE peminjaman SET tanggal_kembali_aktual=?, status=?, denda=?, petugas_kembali=? WHERE id=?")
            ->execute([$tgl_kembali, $status, $denda, $petugas_kembali, $id]);
        // Update stok buku
        $pdo->prepare("UPDATE buku SET jumlah_tersedia = jumlah_tersedia + 1 WHERE id=?")->execute([$data['buku_id']]);
        $success = "Pengembalian berhasil!";
    }
    if ($action == 'delete') {
        $id = $_POST['id'];
        $pdo->prepare("DELETE FROM peminjaman WHERE id=?")->execute([$id]);
        $success = "Data pengembalian dihapus!";
    }
}

// Daftar pengembalian
$stmt = $pdo->query("SELECT p.*, a.nama, b.judul FROM peminjaman p 
    JOIN anggota a ON p.anggota_id=a.id 
    JOIN buku b ON p.buku_id=b.id 
    WHERE p.status IN ('dikembalikan','terlambat') ORDER BY p.tanggal_kembali_aktual DESC");
$data = $stmt->fetchAll();

// Daftar pinjam yang belum kembali
$stmt = $pdo->query("SELECT p.*, a.nama, b.judul FROM peminjaman p 
    JOIN anggota a ON p.anggota_id=a.id 
    JOIN buku b ON p.buku_id=b.id 
    WHERE p.status='dipinjam' ORDER BY p.tanggal_pinjam DESC");
$pinjam = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-undo"></i> Pengembalian Buku</h2>
        <nav class="breadcrumb-custom">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Pengembalian</li>
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
            <div class="card-header-custom"><i class="fas fa-undo"></i> Proses Pengembalian</div>
            <div class="card-body-custom">
                <form method="POST">
                    <input type="hidden" name="action" value="return">
                    <div class="mb-3">
                        <label>Buku yang dipinjam</label>
                        <select name="id" class="form-control form-control-custom" required>
                            <option value="">Pilih</option>
                            <?php foreach($pinjam as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['nama']) ?> - <?= htmlspecialchars($p['judul']) ?> (<?= $p['tanggal_pinjam'] ?>)
                            </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success-custom"><i class="fas fa-check"></i> Kembalikan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-4">
        <div class="card-custom">
            <div class="card-header-custom"><i class="fas fa-list"></i> Riwayat Pengembalian</div>
            <div class="card-body-custom">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Denda</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data as $i => $p): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= htmlspecialchars($p['nama']) ?></td>
                                <td><?= htmlspecialchars($p['judul']) ?></td>
                                <td><?= $p['tanggal_pinjam'] ?></td>
                                <td><?= $p['tanggal_kembali_aktual'] ?></td>
                                <td><?= $p['denda'] ? 'Rp'.number_format($p['denda']) : '-' ?></td>
                                <td>
                                    <span class="badge bg-<?= $p['status']=='terlambat'?'danger':'success' ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>
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
