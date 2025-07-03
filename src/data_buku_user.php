<?php
$page_title = 'Daftar Buku';
include 'includes/header.php';

// Jika bukan user, arahkan ke halaman admin
if (!isLoggedIn() || isAdmin()) {
    header('Location: data_buku_admin.php');
    exit;
}

// Ambil data buku (hanya tampilkan data tanpa form)
$search = $_GET['search'] ?? '';
$where = '';
$params = [];

if ($search) {
    $where = "WHERE b.judul LIKE ? OR b.kode_buku LIKE ? OR p.nama_penulis LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$stmt = $pdo->prepare("
    SELECT b.*, 
           k.nama_kategori, 
           r.nama_rak, 
           p.nama_penulis, 
           pb.nama_penerbit 
    FROM buku b
    LEFT JOIN kategori k ON b.kategori_id = k.id
    LEFT JOIN rak r ON b.rak_id = r.id
    LEFT JOIN penulis p ON b.penulis_id = p.id
    LEFT JOIN penerbit pb ON b.penerbit_id = pb.id
    $where
    ORDER BY b.tanggal_masuk DESC
");
$stmt->execute($params);
$books = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-book"></i> Daftar Buku</h2>
        <nav class="breadcrumb-custom">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Daftar Buku</li>
            </ol>
        </nav>
    </div>
    <div class="col-md-4">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control" placeholder="Cari buku..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary-custom ms-2">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <i class="fas fa-list"></i> Daftar Buku (<?= count($books) ?> buku)
    </div>
    <div class="card-body-custom">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $i => $book): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($book['kode_buku']) ?></td>
                        <td><?= htmlspecialchars($book['judul']) ?></td>
                        <td><?= htmlspecialchars($book['nama_penulis']) ?></td>
                        <td><?= htmlspecialchars($book['nama_kategori']) ?></td>
                        <td><?= $book['jumlah_tersedia'] ?>/<?= $book['jumlah_total'] ?></td>
                        <td>
                            <span class="badge bg-<?= $book['kondisi'] == 'baik' ? 'success' : ($book['kondisi'] == 'rusak' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($book['kondisi']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($books) == 0): ?>
                    <tr><td colspan="7" class="text-center">Data buku tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
