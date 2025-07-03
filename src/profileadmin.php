<?php
require_once 'includes/function.php';
requireAdmin();
include 'includes/header.php';

// Ambil data admin dari database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT username, nama_lengkap, email FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Proses ganti password
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ganti_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $password_konfirmasi = $_POST['password_konfirmasi'];

    // Cek password lama
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? AND password = MD5(?)');
    $stmt->execute([$user_id, $password_lama]);
    if ($stmt->rowCount() === 0) {
        $error = 'Password lama salah!';
    } elseif ($password_baru !== $password_konfirmasi) {
        $error = 'Password baru dan konfirmasi tidak sama!';
    } else {
        $stmt = $pdo->prepare('UPDATE users SET password = MD5(?) WHERE id = ?');
        if ($stmt->execute([$password_baru, $user_id])) {
            $success = 'Password berhasil diganti!';
        } else {
            $error = 'Gagal mengganti password!';
        }
    }
}
?>

<div class="container mt-4">
    <h2>Profil Admin</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <div class="card mb-4" style="max-width: 500px;">
        <div class="card-body">
            <h5 class="card-title">Data Admin</h5>
            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($user['nama_lengkap']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>
    </div>
    <div class="card" style="max-width: 500px;">
        <div class="card-body">
            <h5 class="card-title">Ganti Password</h5>
            <form method="POST">
                <input type="hidden" name="ganti_password" value="1">
                <div class="mb-3">
                    <label>Password Lama</label>
                    <input type="password" name="password_lama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password Baru</label>
                    <input type="password" name="password_baru" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="password_konfirmasi" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Ganti Password</button>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?> 