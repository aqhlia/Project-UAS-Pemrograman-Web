<?php
session_start();
$page_title = 'Data Rak';
include 'data_awal.php';
requireAdmin();

// Tangani aksi CRUD
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = $_POST['action'] ?? '';
    $post_id = $_POST['id'] ?? null;
    $nama_rak = trim($_POST['nama_rak'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $kapasitas = (int) ($_POST['kapasitas'] ?? 0);

    if ($post_action === 'add') {
        $kode_rak = 'RAK' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO rak (kode_rak, nama_rak, lokasi, kapasitas) VALUES (?, ?, ?, ?)");
        $stmt->execute([$kode_rak, $nama_rak, $lokasi, $kapasitas]);
        $_SESSION['success'] = "Rak berhasil ditambahkan.";
        header('Location: rak.php');
        exit;
    }

    if ($post_action === 'edit' && $post_id) {
        $stmt = $pdo->prepare("UPDATE rak SET nama_rak=?, lokasi=?, kapasitas=? WHERE id=?");
        $stmt->execute([$nama_rak, $lokasi, $kapasitas, $post_id]);
        $_SESSION['success'] = "Rak berhasil diupdate.";
        header('Location: rak.php');
        exit;
    }

    if ($post_action === 'delete' && $post_id) {
        $stmt = $pdo->prepare("DELETE FROM rak WHERE id=?");
        $stmt->execute([$post_id]);
        $_SESSION['success'] = "Rak berhasil dihapus.";
        header('Location: rak.php');
        exit;
    }
}

// Jika edit, ambil data rak
$edit_data = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM rak WHERE id=?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

// Ambil semua rak
$stmt = $pdo->query("SELECT * FROM rak ORDER BY nama_rak");
$rak = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Data Rak</h2>
        <?php if (!$edit_data): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahRakModal">
            <i class="fas fa-plus"></i> Tambah Rak
        </button>
        <?php else: ?>
        <a href="rak.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
<div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if ($edit_data): ?>
<div class="card mb-4">
    <div class="card-header">Edit Rak</div>
    <div class="card-body">
        <form method="POST" action="rak.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Nama Rak</label>
                <input type="text" name="nama_rak" class="form-control" value="<?= htmlspecialchars($edit_data['nama_rak']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Lokasi</label>
                <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($edit_data['lokasi']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Kapasitas</label>
                <input type="number" name="kapasitas" class="form-control" value="<?= $edit_data['kapasitas'] ?>">
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update</button>
            <a href="rak.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php else: ?>
<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Rak</th>
                <th>Nama Rak</th>
                <th>Lokasi</th>
                <th>Kapasitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($rak) > 0): ?>
            <?php foreach ($rak as $i => $r): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($r['kode_rak']) ?></td>
                <td><?= htmlspecialchars($r['nama_rak']) ?></td>
                <td><?= htmlspecialchars($r['lokasi']) ?></td>
                <td><?= $r['kapasitas'] ?></td>
                <td>
                    <a href="rak.php?action=edit&id=<?= $r['id'] ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form method="POST" action="rak.php" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus rak ini?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">Belum ada data rak.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Rak -->
<div class="modal fade" id="tambahRakModal" tabindex="-1" aria-labelledby="tambahRakModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="rak.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rak Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Nama Rak</label>
                        <input type="text" class="form-control" name="nama_rak" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" class="form-control" name="lokasi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" class="form-control" name="kapasitas">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'data_awal.php'; ?>
