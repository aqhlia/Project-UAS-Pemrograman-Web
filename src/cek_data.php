<?php
$page_title = 'Cek Data Buku & Anggota';
include 'includes/header.php';

$search = $_GET['search'] ?? '';
$buku = [];
$anggota = [];

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM buku WHERE judul LIKE ? OR kode_buku LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
    $buku = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM anggota WHERE nama LIKE ? OR nomor_anggota LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
    $anggota = $stmt->fetchAll();
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-search"></i> Cek Data Buku & Anggota</h2>
        <nav class="breadcrumb-custom">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Cek Data</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card-custom mb-4">
    <div class="card-header-custom"><i class="fas fa-search"></i> Cari Data</div>
    <div class="card-body-custom">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Judul buku, kode buku, nama anggota, nomor anggota ..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary-custom w-100" type="submit"><i class="fas fa-search"></i> Cari</button>
            </div>
        </form>
    </div>
</div>

<?php if ($search): ?>
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card-custom">
            <div class="card-header-custom"><i class="fas fa-book"></i> Data Buku</div>
            <div class="card-body-custom">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($buku as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['kode_buku']) ?></td>
                                <td><?= htmlspecialchars($b['judul']) ?></td>
                                <td><?= $b['jumlah_tersedia'] ?>/<?= $b['jumlah_total'] ?></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                    <?php if (!$buku): ?><div class="text-muted">Tidak ada data buku.</div><?php endif;?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card-custom">
            <div class="card-header-custom"><i class="fas fa-id-card"></i> Data Anggota</div>
            <div class="card-body-custom">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No Anggota</th>
                                <th>Nama</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($anggota as $a): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['nomor_anggota']) ?></td>
                                <td><?= htmlspecialchars($a['nama']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $a['status']=='aktif'?'success':'secondary' ?>">
                                        <?= ucfirst($a['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                    <?php if (!$anggota): ?><div class="text-muted">Tidak ada data anggota.</div><?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
