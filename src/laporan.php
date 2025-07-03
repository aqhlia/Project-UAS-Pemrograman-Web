<?php
$page_title = 'Laporan Peminjaman & Pengembalian';
include 'includes/header.php';

// Filter
$tgl1 = $_GET['tgl1'] ?? date('Y-m-01');
$tgl2 = $_GET['tgl2'] ?? date('Y-m-d');
$status = $_GET['status'] ?? '';

$where = "WHERE tanggal_pinjam BETWEEN ? AND ?";
$params = [$tgl1, $tgl2];
if ($status) {
    $where .= " AND status=?";
    $params[] = $status;
}

$stmt = $pdo->prepare("SELECT p.*, a.nama, b.judul FROM peminjaman p
    JOIN anggota a ON p.anggota_id=a.id
    JOIN buku b ON p.buku_id=b.id
    $where
    ORDER BY p.tanggal_pinjam DESC");
$stmt->execute($params);
$data = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-chart-bar"></i> Laporan Peminjaman & Pengembalian</h2>
        <nav class="breadcrumb-custom">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Laporan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card-custom mb-4">
    <div class="card-header-custom"><i class="fas fa-filter"></i> Filter Laporan</div>
    <div class="card-body-custom">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label>Dari Tanggal</label>
                <input type="date" name="tgl1" value="<?= $tgl1 ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Sampai Tanggal</label>
                <input type="date" name="tgl2" value="<?= $tgl2 ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua</option>
                    <option value="dipinjam" <?= $status=='dipinjam'?'selected':'' ?>>Dipinjam</option>
                    <option value="dikembalikan" <?= $status=='dikembalikan'?'selected':'' ?>>Dikembalikan</option>
                    <option value="terlambat" <?= $status=='terlambat'?'selected':'' ?>>Terlambat</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary-custom" type="submit"><i class="fas fa-search"></i> Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-header-custom"><i class="fas fa-list"></i> Data Laporan</div>
    <div class="card-body-custom">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Anggota</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali Rencana</th>
                        <th>Tgl Kembali Aktual</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($data as $i => $p): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($p['nama']) ?></td>
                        <td><?= htmlspecialchars($p['judul']) ?></td>
                        <td><?= $p['tanggal_pinjam'] ?></td>
                        <td><?= $p['tanggal_kembali_rencana'] ?></td>
                        <td><?= $p['tanggal_kembali_aktual'] ?></td>
                        <td>
                            <span class="badge bg-<?= $p['status']=='terlambat'?'danger':($p['status']=='dipinjam'?'warning':'success') ?>">
                                <?= ucfirst($p['status']) ?>
                            </span>
                        </td>
                        <td><?= $p['denda'] ? 'Rp'.number_format($p['denda']) : '-' ?></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
