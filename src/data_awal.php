<?php
session_start();
require_once 'config/database.php';
require_once 'includes/function.php';

// Pastikan user sudah login, semua role bisa akses halaman ini
requireLogin();

$is_admin = isAdmin();

$page_title = 'Data Awal';
include 'includes/header.php';

$active_tab = $_GET['tab'] ?? 'kategori';

// Tangani form CRUD, hanya admin yang boleh
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $table = $_POST['table'] ?? '';
    $id    = $_POST['id'] ?? null;

    if (!$is_admin) {
        $_SESSION['error'] = "Anda tidak punya akses mengubah data!";
        header("Location: data_awal.php?tab={$table}");
        exit;
    }

    if ($action === 'add') {
        switch ($table) {
            case 'kategori':
                $kode = generateCode('KTG','kategori','kode_kategori');
                $nama = trim($_POST['nama_kategori']);
                $desc = trim($_POST['deskripsi']);
                $stmt = $pdo->prepare("INSERT INTO kategori (kode_kategori,nama_kategori,deskripsi) VALUES(?,?,?)");
                $stmt->execute([$kode,$nama,$desc]);
                break;
            case 'rak':
                $kode = trim($_POST['kode_rak']);
                $nama = trim($_POST['nama_rak']);
                $lokasi = trim($_POST['lokasi']);
                $kapasitas = (int)$_POST['kapasitas'];
                $stmt = $pdo->prepare("INSERT INTO rak (kode_rak,nama_rak,lokasi,kapasitas) VALUES(?,?,?,?)");
                $stmt->execute([$kode,$nama,$lokasi,$kapasitas]);
                break;
            case 'penulis':
                $kode = generateCode('PNS','penulis','kode_penulis');
                $nama = trim($_POST['nama_penulis']);
                $tempat = trim($_POST['tempat_lahir']);
                $tgl = $_POST['tanggal_lahir'];
                $email = trim($_POST['email']);
                $stmt = $pdo->prepare("INSERT INTO penulis (kode_penulis,nama_penulis,tempat_lahir,tanggal_lahir,email) VALUES(?,?,?,?,?)");
                $stmt->execute([$kode,$nama,$tempat,$tgl,$email]);
                break;
            case 'penerbit':
                $kode = generateCode('PNB','penerbit','kode_penerbit');
                $nama = trim($_POST['nama_penerbit']);
                $alamat = trim($_POST['alamat']);
                $email = trim($_POST['email']);
                $stmt = $pdo->prepare("INSERT INTO penerbit (kode_penerbit,nama_penerbit,alamat,email) VALUES(?,?,?,?)");
                $stmt->execute([$kode,$nama,$alamat,$email]);
                break;
        }
        $_SESSION['success'] = "Data berhasil ditambahkan!";
    }

    if ($action === 'edit') {
        switch ($table) {
            case 'kategori':
                $nama = trim($_POST['nama_kategori']);
                $desc = trim($_POST['deskripsi']);
                $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori=?, deskripsi=? WHERE id=?");
                $stmt->execute([$nama,$desc,$id]);
                break;
            case 'rak':
                $nama = trim($_POST['nama_rak']);
                $lokasi = trim($_POST['lokasi']);
                $kapasitas = (int)$_POST['kapasitas'];
                $stmt = $pdo->prepare("UPDATE rak SET nama_rak=?, lokasi=?, kapasitas=? WHERE id=?");
                $stmt->execute([$nama,$lokasi,$kapasitas,$id]);
                break;
            case 'penulis':
                $nama = trim($_POST['nama_penulis']);
                $tempat = trim($_POST['tempat_lahir']);
                $tgl = $_POST['tanggal_lahir'];
                $email = trim($_POST['email']);
                $stmt = $pdo->prepare("UPDATE penulis SET nama_penulis=?, tempat_lahir=?, tanggal_lahir=?, email=? WHERE id=?");
                $stmt->execute([$nama,$tempat,$tgl,$email,$id]);
                break;
            case 'penerbit':
                $nama = trim($_POST['nama_penerbit']);
                $alamat = trim($_POST['alamat']);
                $email = trim($_POST['email']);
                $stmt = $pdo->prepare("UPDATE penerbit SET nama_penerbit=?, alamat=?, email=? WHERE id=?");
                $stmt->execute([$nama,$alamat,$email,$id]);
                break;
        }
        $_SESSION['success'] = "Data berhasil diupdate!";
    }

    if ($action === 'delete') {
        $table = preg_replace('/[^a-z]/','',$table);
        $relasi_cek = [
            'penulis' => ['table'=>'buku', 'column'=>'penulis_id'],
            'penerbit' => ['table'=>'buku', 'column'=>'penerbit_id'],
            'kategori' => ['table'=>'buku', 'column'=>'kategori_id'],
            'rak'      => ['table'=>'buku', 'column'=>'rak_id']
        ];

        if (isset($relasi_cek[$table])) {
            $cek = $pdo->prepare("SELECT COUNT(*) FROM {$relasi_cek[$table]['table']} WHERE {$relasi_cek[$table]['column']}=?");
            $cek->execute([$id]);
            $count = $cek->fetchColumn();

            if ($count > 0) {
                $_SESSION['error'] = "Data tidak dapat dihapus karena masih digunakan!";
                header("Location: data_awal.php?tab={$table}");
                exit;
            }
        }

        $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id=?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Data berhasil dihapus!";
    }

    header("Location: data_awal.php?tab={$table}");
    exit;
}
?>

<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-database"></i> Data Awal</h2>
        <nav class="breadcrumb-custom">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Data Awal</li>
            </ol>
        </nav>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
<div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
<div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<ul class="nav nav-tabs mb-4">
    <?php foreach (['kategori','rak','penulis','penerbit'] as $tab): ?>
        <li class="nav-item">
            <a href="?tab=<?= $tab ?>" class="nav-link <?= $active_tab==$tab?'active':'' ?>">
                <i class="fas fa-<?= $tab=='kategori'? 'tags' : ($tab=='rak'? 'archive' : ($tab=='penulis'? 'user-edit' : 'building')) ?>"></i>
                <?= ucfirst($tab) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<?php
function renderTab($pdo, $table, $columns, $labels, $active_tab, $is_admin) {
    $edit = null;
    if ($is_admin && $active_tab == $table && isset($_GET['edit'])) {
        $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id=?");
        $stmt->execute([$_GET['edit']]);
        $edit = $stmt->fetch();
    }
    if ($active_tab == $table) {
        $stmt = $pdo->query("SELECT * FROM {$table} ORDER BY id DESC");
        $data = $stmt->fetchAll();
?>
<div class="row">
    <?php if ($is_admin): ?>
    <div class="col-lg-4 mb-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-plus"></i> <?= $edit?'Edit':'Tambah'?> <?= ucfirst($table) ?>
            </div>
            <div class="card-body-custom">
                <form method="POST">
                    <input type="hidden" name="action" value="<?= $edit?'edit':'add' ?>">
                    <input type="hidden" name="table" value="<?= $table ?>">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                    <?php endif; ?>
                    <?php foreach ($columns as $col => $type): ?>
                        <div class="mb-3">
                            <label class="form-label"><?= $labels[$col] ?></label>
                            <?php if ($type=='textarea'): ?>
                                <textarea name="<?= $col ?>" class="form-control"><?= htmlspecialchars($edit[$col] ?? '') ?></textarea>
                            <?php else: ?>
                                <input type="<?= $type ?>" name="<?= $col ?>" class="form-control" value="<?= htmlspecialchars($edit[$col] ?? '') ?>" required>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-save"></i> <?= $edit?'Update':'Simpan' ?>
                    </button>
                    <?php if ($edit): ?>
                        <a href="?tab=<?= $table ?>" class="btn btn-secondary">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="<?= $is_admin ? 'col-lg-8' : 'col-12' ?> mb-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-list"></i> Daftar <?= ucfirst($table) ?>
            </div>
            <div class="card-body-custom table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr><th>No</th>
                        <?php foreach ($columns as $col => $_): ?><th><?= $labels[$col] ?></th><?php endforeach; ?>
                        <?php if ($is_admin): ?><th>Aksi</th><?php endif; ?></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $i => $row): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <?php foreach ($columns as $col => $_): ?>
                                <td><?= nl2br(htmlspecialchars($row[$col])) ?></td>
                            <?php endforeach; ?>
                            <?php if ($is_admin): ?>
                            <td>
                                <a href="?tab=<?= $table ?>&edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="table" value="<?= $table ?>">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($data)): ?>
                            <tr><td colspan="<?= count($columns) + ($is_admin ? 2 : 1) ?>" class="text-center text-muted">Belum ada data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
    }
}

renderTab($pdo, 'kategori',
    ['kode_kategori'=>'text','nama_kategori'=>'text','deskripsi'=>'textarea'],
    ['kode_kategori'=>'Kode','nama_kategori'=>'Nama','deskripsi'=>'Deskripsi'],
    $active_tab,
    $is_admin
);
renderTab($pdo, 'rak',
    ['kode_rak'=>'text','nama_rak'=>'text','lokasi'=>'text','kapasitas'=>'number'],
    ['kode_rak'=>'Kode','nama_rak'=>'Nama','lokasi'=>'Lokasi','kapasitas'=>'Kapasitas'],
    $active_tab,
    $is_admin
);
renderTab($pdo, 'penulis',
    ['kode_penulis'=>'text','nama_penulis'=>'text','tempat_lahir'=>'text','tanggal_lahir'=>'date','email'=>'textarea'],
    ['kode_penulis'=>'Kode','nama_penulis'=>'Nama','tempat_lahir'=>'Tempat','tanggal_lahir'=>'Tanggal Lahir','email'=>'Email'],
    $active_tab,
    $is_admin
);
renderTab($pdo, 'penerbit',
    ['kode_penerbit'=>'text','nama_penerbit'=>'text','alamat'=>'textarea','email'=>'text'],
    ['kode_penerbit'=>'Kode','nama_penerbit'=>'Nama','alamat'=>'Alamat','email'=>'Email'],
    $active_tab,
    $is_admin
);
?>

<?php include 'includes/footer.php'; ?>
