<?php
session_start();
$page_title = 'Data Penerbit';
include 'includes/header.php';
requireAdmin();

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = $_POST['action'] ?? '';
    $post_id = $_POST['id'] ?? null;
    $nama_penerbit = trim($_POST['nama_penerbit'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($post_action === 'add') {
        $kode_penerbit = 'PNB' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO penerbit (kode_penerbit, nama_penerbit, alamat, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$kode_penerbit, $nama_penerbit, $alamat, $email]);
        $_SESSION['success'] = "Penerbit berhasil ditambahkan.";
        header('Location: penerbit.php');
        exit;
    }

    if ($post_action === 'edit' && $post_id) {
        $stmt = $pdo->prepare("UPDATE penerbit SET nama_penerbit=?, alamat=?, email=? WHERE id=?");
        $stmt->execute([$nama_penerbit, $alamat, $email, $post_id]);
        $_SESSION['success'] = "Penerbit berhasil diupdate.";
        header('Location: penerbit.php');
        exit;
    }

    if ($post_action === 'delete' && $post_id) {
        $stmt = $pdo->prepare("DELETE FROM penerbit WHERE id=?");
        $stmt->execute([$post_id]);
        $_SESSION['success'] = "Penerbit berhasil dihapus.";
        header('Location: penerbit.php');
        exit;
    }
}

$edit_data = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM penerbit WHERE id=?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

$stmt = $pdo->query("SELECT * FROM penerbit ORDER BY nama_penerbit");
$penerbits = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Data Penerbit</h2>
        <?php if (!$edit_data): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPenerbitModal">
            <i class="fas fa-plus"></i> Tambah Penerbit
        </button>
        <?php else: ?>
        <a href="penerbit.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
<div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if ($edit_data): ?>
<!-- Form Edit Penerbit -->
<div class="card mb-4">
    <div class="card-header">Edit Penerbit</div>
    <div class="card-body">
        <form method="POST" action="penerbit.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Nama Penerbit</label>
                <input type="text" name="nama_penerbit" class="form-control" value="<?= htmlspecialchars($edit_data['nama_penerbit']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($edit_data['alamat']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($edit_data['email']) ?>">
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update</button>
            <a href="penerbit.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php else: ?>
<!-- Tabel Daftar Penerbit -->
<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Penerbit</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($penerbits) > 0): ?>
                <?php foreach ($penerbits as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($p['kode_penerbit']) ?></td>
                    <td><?= htmlspecialchars($p['nama_penerbit']) ?></td>
                    <td><?= htmlspecialchars($p['alamat']) ?></td>
                    <td><?= htmlspecialchars($p['email']) ?></td>
                    <td>
                        <a href="penerbit.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form method="POST" action="penerbit.php" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus penerbit ini?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Belum ada data penerbit.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Penerbit -->
<div class="modal fade" id="tambahPenerbitModal" tabindex="-1" aria-labelledby="tambahPenerbitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="penerbit.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPenerbitModalLabel">Tambah Penerbit Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="nama_penerbit" class="form-label">Nama Penerbit</label>
                        <input type="text" class="form-control" id="nama_penerbit" name="nama_penerbit" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
