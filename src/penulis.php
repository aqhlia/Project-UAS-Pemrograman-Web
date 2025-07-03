session_start();
<?php
$page_title = 'Data Penulis';
include 'includes/header.php';
requireAdmin();

// Tangani aksi CRUD
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = $_POST['action'] ?? '';
    $post_id = $_POST['id'] ?? null;
    $nama_penulis = trim($_POST['nama_penulis'] ?? '');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $email = trim($_POST['email'] ?? '');

    if ($post_action === 'add') {
        // Generate kode_penulis unik (contoh sederhana)
        $kode_penulis = 'PNS' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare("INSERT INTO penulis (kode_penulis, nama_penulis, tempat_lahir, tanggal_lahir, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$kode_penulis, $nama_penulis, $tempat_lahir, $tanggal_lahir, $email]);
        $_SESSION['success'] = "Penulis berhasil ditambahkan.";
        header('Location: penulis.php');
        exit;
    }

    if ($post_action === 'edit' && $post_id) {
        $stmt = $pdo->prepare("UPDATE penulis SET nama_penulis=?, tempat_lahir=?, tanggal_lahir=?, email=? WHERE id=?");
        $stmt->execute([$nama_penulis, $tempat_lahir, $tanggal_lahir, $email, $post_id]);
        $_SESSION['success'] = "Penulis berhasil diupdate.";
        header('Location: penulis.php');
        exit;
    }

    if ($post_action === 'delete' && $post_id) {
        $stmt = $pdo->prepare("DELETE FROM penulis WHERE id=?");
        $stmt->execute([$post_id]);
        $_SESSION['success'] = "Penulis berhasil dihapus.";
        header('Location: penulis.php');
        exit;
    }
}

// Jika edit, ambil data penulis
$edit_data = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM penulis WHERE id=?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

// Ambil semua data penulis
$stmt = $pdo->query("SELECT * FROM penulis ORDER BY nama_penulis");
$penulis = $stmt->fetchAll();

?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Data Penulis</h2>
        <?php if (!$edit_data): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPenulisModal">
            <i class="fas fa-plus"></i> Tambah Penulis
        </button>
        <?php else: ?>
        <a href="penulis.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
<div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if ($edit_data): ?>
<!-- Form Edit Penulis -->
<div class="card mb-4">
    <div class="card-header">Edit Penulis</div>
    <div class="card-body">
        <form method="POST" action="penulis.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Nama Penulis</label>
                <input type="text" name="nama_penulis" class="form-control" value="<?= htmlspecialchars($edit_data['nama_penulis']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" value="<?= htmlspecialchars($edit_data['tempat_lahir']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="<?= $edit_data['tanggal_lahir'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <textarea name="email" class="form-control" rows="4"><?= htmlspecialchars($edit_data['email']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update</button>
            <a href="penulis.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php else: ?>
<!-- Tabel Daftar Penulis -->
<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Penulis</th>
                <th>Nama Penulis</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($penulis) > 0): ?>
                <?php foreach ($penulis as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($p['kode_penulis']) ?></td>
                    <td><?= htmlspecialchars($p['nama_penulis']) ?></td>
                    <td><?= htmlspecialchars($p['tempat_lahir']) ?></td>
                    <td><?= htmlspecialchars($p['tanggal_lahir']) ?></td>
                    <td><?= nl2br(htmlspecialchars($p['email'])) ?></td>
                    <td>
                        <a href="penulis.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form method="POST" action="penulis.php" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus penulis ini?');">
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
                <tr><td colspan="7" class="text-center">Belum ada data penulis.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Penulis -->
<div class="modal fade" id="tambahPenulisModal" tabindex="-1" aria-labelledby="tambahPenulisModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="penulis.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPenulisModalLabel">Tambah Penulis Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="nama_penulis" class="form-label">Nama Penulis</label>
                        <input type="text" class="form-control" id="nama_penulis" name="nama_penulis" required>
                    </div>
                    <div class="mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <textarea class="form-control" id="email" name="email" rows="3"></textarea>
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

<?php
include 'includes/footer.php';
?>
