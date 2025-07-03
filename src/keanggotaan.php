<?php 
$page_title = 'Keanggotaan';
include 'includes/header.php';

// Handle form submissions hanya untuk admin
if ($_POST && $_SESSION['role'] == 'admin') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $nomor_anggota = generateCode('AGT', 'anggota', 'nomor_anggota');
        $nama = trim($_POST['nama']);
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tempat_lahir = trim($_POST['tempat_lahir']);
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $alamat = trim($_POST['alamat']);
        $telepon = trim($_POST['telepon']);
        $email = trim($_POST['email']);
        $pekerjaan = trim($_POST['pekerjaan']);
        $tanggal_daftar = $_POST['tanggal_daftar'];
        $tanggal_expired = date('Y-m-d', strtotime($tanggal_daftar . ' +1 year'));

        $stmt = $pdo->prepare("INSERT INTO anggota (nomor_anggota, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, telepon, email, pekerjaan, tanggal_daftar, tanggal_expired) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$nomor_anggota, $nama, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $alamat, $telepon, $email, $pekerjaan, $tanggal_daftar, $tanggal_expired])) {
            $success = "Anggota berhasil ditambahkan!";
        }
    }

    if ($action == 'edit') {
        $id = $_POST['id'];
        $nama = trim($_POST['nama']);
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tempat_lahir = trim($_POST['tempat_lahir']);
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $alamat = trim($_POST['alamat']);
        $telepon = trim($_POST['telepon']);
        $email = trim($_POST['email']);
        $pekerjaan = trim($_POST['pekerjaan']);
        $status = $_POST['status'];

        $stmt = $pdo->prepare("UPDATE anggota SET nama=?, jenis_kelamin=?, tempat_lahir=?, tanggal_lahir=?, alamat=?, telepon=?, email=?, pekerjaan=?, status=? WHERE id=?");
        $stmt->execute([$nama, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $alamat, $telepon, $email, $pekerjaan, $status, $id]);
        $success = "Data anggota berhasil diupdate!";
    }

    if ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM anggota WHERE id=?");
        $stmt->execute([$id]);
        $success = "Anggota berhasil dihapus!";
    }
}

// Get edit data
$edit_data = null;
if (isset($_GET['edit']) && $_SESSION['role'] == 'admin') {
    $stmt = $pdo->prepare("SELECT * FROM anggota WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}

// Get members list
$search = $_GET['search'] ?? '';
$where = '';
$params = [];

if ($search) {
    $where = "WHERE nama LIKE ? OR nomor_anggota LIKE ? OR telepon LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$stmt = $pdo->prepare("SELECT * FROM anggota $where ORDER BY tanggal_daftar DESC");
$stmt->execute($params);
$members = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-id-card"></i> Keanggotaan</h2>
        <nav class="breadcrumb-custom">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Keanggotaan</li>
            </ol>
        </nav>
    </div>
    <div class="col-md-4">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control" placeholder="Cari anggota..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary-custom ms-2">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<?php if (isset($success)): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
<?php endif; ?>

<div class="row">
    <?php if ($_SESSION['role'] == 'admin'): ?>
    <div class="col-lg-4 mb-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-user-plus"></i> <?= $edit_data ? 'Edit' : 'Tambah' ?> Anggota
            </div>
            <div class="card-body-custom">
                <form method="POST">
                    <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'add' ?>">
                    <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control form-control-custom" 
                            value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>" required>
                    </div>

                    <?php if (!$edit_data): ?>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control form-control-custom" required>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control form-control-custom" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" <?= ($edit_data['jenis_kelamin'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= ($edit_data['jenis_kelamin'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control form-control-custom" 
                                value="<?= htmlspecialchars($edit_data['tempat_lahir'] ?? '') ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control form-control-custom" 
                                value="<?= $edit_data['tanggal_lahir'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control form-control-custom" rows="3"><?= htmlspecialchars($edit_data['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control form-control-custom" 
                                value="<?= htmlspecialchars($edit_data['telepon'] ?? '') ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control form-control-custom" 
                                value="<?= htmlspecialchars($edit_data['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control form-control-custom" 
                            value="<?= htmlspecialchars($edit_data['pekerjaan'] ?? '') ?>">
                    </div>

                    <?php if (!$edit_data): ?>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Daftar</label>
                        <input type="date" name="tanggal_daftar" class="form-control form-control-custom" 
                            value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control form-control-custom" required>
                            <option value="aktif" <?= ($edit_data['status'] ?? '') == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= ($edit_data['status'] ?? '') == 'nonaktif' ? 'selected' : '' ?>>Non-Aktif</option>
                            <option value="suspend" <?= ($edit_data['status'] ?? '') == 'suspend' ? 'selected' : '' ?>>Suspend</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-save"></i> <?= $edit_data ? 'Update' : 'Simpan' ?>
                    </button>
                    <?php if ($edit_data): ?>
                    <a href="keanggotaan.php" class="btn btn-secondary">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-lg-<?= $_SESSION['role'] == 'admin' ? '8' : '12' ?> mb-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-list"></i> Daftar Anggota (<?= count($members) ?> orang)
            </div>
            <div class="card-body-custom">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Anggota</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Telepon</th>
                                <th>Tgl Daftar</th>
                                <th>Status</th>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                <th>Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $i => $member): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($member['nomor_anggota']) ?></td>
                                <td><?= htmlspecialchars($member['nama']) ?></td>
                                <td><?= $member['jenis_kelamin'] ?></td>
                                <td><?= htmlspecialchars($member['telepon']) ?></td>
                                <td><?= date('d/m/Y', strtotime($member['tanggal_daftar'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $member['status'] == 'aktif' ? 'success' : ($member['status'] == 'suspend' ? 'warning' : 'secondary') ?>">
                                        <?= ucfirst($member['status']) ?>
                                    </span>
                                </td>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                <td>
                                    <a href="?edit=<?= $member['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus anggota ini?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
