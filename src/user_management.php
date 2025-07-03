<?php 
$page_title = 'User Management';
include 'includes/header.php';
requireAdmin();

// Handle form submissions
if ($_POST) {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $role = $_POST['role'];
        $nama_lengkap = trim($_POST['nama_lengkap']);
        $email = trim($_POST['email']);

        // Validasi sederhana user sudah ada
        $cek = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $cek->execute([$username]);
        if ($cek->fetchColumn() > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, nama_lengkap, email, status, created_at) VALUES (?, MD5(?), ?, ?, ?, 'aktif', NOW())");
            if ($stmt->execute([$username, $password, $role, $nama_lengkap, $email])) {
                $success = "User berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan user!";
            }
        }
    }

    if ($action == 'edit') {
        $id = $_POST['id'];
        $username = trim($_POST['username']);
        $role = $_POST['role'];
        $nama_lengkap = trim($_POST['nama_lengkap']);
        $email = trim($_POST['email']);
        $status = $_POST['status'];

        if (!empty($_POST['password'])) {
            $stmt = $pdo->prepare("UPDATE users SET username=?, password=MD5(?), role=?, nama_lengkap=?, email=?, status=? WHERE id=?");
            $stmt->execute([$username, $_POST['password'], $role, $nama_lengkap, $email, $status, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username=?, role=?, nama_lengkap=?, email=?, status=? WHERE id=?");
            $stmt->execute([$username, $role, $nama_lengkap, $email, $status, $id]);
        }
        $success = "User berhasil diupdate!";
    }

    if ($action == 'delete') {
        $id = $_POST['id'];

        // Mencegah user menghapus dirinya sendiri
        if ($id != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
            $stmt->execute([$id]);
            $success = "User berhasil dihapus!";
        } else {
            $error = "Tidak dapat menghapus user yang sedang login!";
        }
    }
}

// Ambil data edit jika ada
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}

// Ambil seluruh data user
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!-- TAMPILAN DEPAN -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-users-cog"></i> User Management</h2>
        <nav class="breadcrumb-custom">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">User Management</li>
            </ol>
        </nav>
    </div>
</div>

<?php if (isset($success)): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-user-plus"></i> <?= $edit_data ? 'Edit User' : 'Tambah User' ?>
            </div>
            <div class="card-body-custom">
                <form method="POST">
                    <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'add' ?>">
                    <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control form-control-custom" 
                               value="<?= $edit_data['username'] ?? '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password <?= $edit_data ? '(kosongkan jika tidak diubah)' : '' ?></label>
                        <input type="password" name="password" class="form-control form-control-custom" 
                               <?= !$edit_data ? 'required' : '' ?>>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-control form-control-custom" required>
                            <option value="admin" <?= ($edit_data['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="user" <?= ($edit_data['role'] ?? '') == 'user' ? 'selected' : '' ?>>User</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control form-control-custom" 
                               value="<?= $edit_data['nama_lengkap'] ?? '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control form-control-custom" 
                               value="<?= $edit_data['email'] ?? '' ?>">
                    </div>

                    <?php if ($edit_data): ?>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control form-control-custom" required>
                            <option value="aktif" <?= $edit_data['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= $edit_data['status'] == 'nonaktif' ? 'selected' : '' ?>>Non-Aktif</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-save"></i> <?= $edit_data ? 'Update' : 'Simpan' ?>
                    </button>
                    <?php if ($edit_data): ?>
                    <a href="user_management.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-list"></i> Daftar User
            </div>
            <div class="card-body-custom">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $i => $user): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : 'primary' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $user['status'] == 'aktif' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?edit=<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($users)): ?>
                            <tr><td colspan="6" class="text-center text-muted">Belum ada user.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
