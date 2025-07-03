<?php
require_once 'config/database.php';
require_once 'includes/function.php';

// ⛔ Cek login sebelum output HTML
requireLogin();

// Set judul halaman
$page_title = 'Data Buku';

// Cek apakah user adalah admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

include 'includes/header.php';

// Ambil data kategori & rak
$kategori = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
$rak = $pdo->query("SELECT * FROM rak ORDER BY nama_rak")->fetchAll();


// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    $jumlah = count($_POST['kode_buku']);
    for ($i = 0; $i < $jumlah; $i++) {
        $kode_buku = $_POST['kode_buku'][$i];
        $judul = $_POST['judul'][$i];
        $jumlah_total = $_POST['jumlah_total'][$i];
        $jumlah_tersedia = $_POST['jumlah_tersedia'][$i];
        $kondisi = $_POST['kondisi'][$i];
        $tanggal_masuk = $_POST['tanggal_masuk'][$i];
        $kategori_id = $_POST['kategori_id'][$i];
        $rak_id = $_POST['rak_id'][$i];

        // Penulis
        $nama_penulis = trim($_POST['nama_penulis'][$i]);
        $stmt = $pdo->prepare("SELECT id FROM penulis WHERE nama_penulis = ?");
        $stmt->execute([$nama_penulis]);
        $penulis = $stmt->fetch();

        if (!$penulis) {
            $stmt = $pdo->query("SELECT MAX(RIGHT(kode_penulis, 4)) AS max_kode FROM penulis");
            $lastCode = $stmt->fetchColumn();
            $nextNumber = str_pad(((int)$lastCode) + 1, 4, '0', STR_PAD_LEFT);
            $kode_penulis = "PNS$nextNumber";

            $stmt = $pdo->prepare("INSERT INTO penulis (kode_penulis, nama_penulis) VALUES (?, ?)");
            $stmt->execute([$kode_penulis, $nama_penulis]);
            $penulis_id = $pdo->lastInsertId();
        } else {
            $penulis_id = $penulis['id'];
        }

        // Penerbit
        $nama_penerbit = trim($_POST['nama_penerbit'][$i]);
        $stmt = $pdo->prepare("SELECT * FROM penerbit WHERE nama_penerbit = ?");
        $stmt->execute([$nama_penerbit]);
        $penerbit = $stmt->fetch();

        if (!$penerbit) {
            $stmt = $pdo->query("SELECT MAX(RIGHT(kode_penerbit, 4)) AS max_kode FROM penerbit");
            $lastCode = $stmt->fetchColumn();
            $nextNumber = str_pad(((int)$lastCode) + 1, 4, '0', STR_PAD_LEFT);
            $kode_penerbit = "PNB$nextNumber";

            $stmt = $pdo->prepare("INSERT INTO penerbit (kode_penerbit, nama_penerbit) VALUES (?, ?)");
            $stmt->execute([$kode_penerbit, $nama_penerbit]);
            $penerbit_id = $pdo->lastInsertId();
        } else {
            $penerbit_id = $penerbit['id'];
        }

        // Simpan buku
        $stmt = $pdo->prepare("INSERT INTO buku 
            (kode_buku, judul, jumlah_total, jumlah_tersedia, kondisi, tanggal_masuk, kategori_id, rak_id, penulis_id, penerbit_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$kode_buku, $judul, $jumlah_total, $jumlah_tersedia, $kondisi, $tanggal_masuk, $kategori_id, $rak_id, $penulis_id, $penerbit_id]);
    }

    $success = "Semua data buku berhasil ditambahkan.";
}

// Ambil semua data buku
$stmt = $pdo->query("
    SELECT b.*, k.nama_kategori, r.nama_rak, p.nama_penulis, pb.nama_penerbit 
    FROM buku b
    LEFT JOIN kategori k ON b.kategori_id = k.id
    LEFT JOIN rak r ON b.rak_id = r.id
    LEFT JOIN penulis p ON b.penulis_id = p.id
    LEFT JOIN penerbit pb ON b.penerbit_id = pb.id
    ORDER BY b.tanggal_masuk DESC
");
$books = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-book"></i> Data Buku</h2>
    </div>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<div class="row">
    <?php if ($isAdmin): ?>
    <div class="col-lg-5 mb-4">
        <div class="card-custom">
            <div class="card-header-custom"><i class="fas fa-plus"></i> Tambah Buku</div>
            <div class="card-body-custom">
                <form method="POST" id="formBuku">
                    <div id="buku-container">
                        <div class="buku-form border p-3 mb-3 rounded shadow-sm">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label>Kode Buku</label>
                                    <input type="text" name="kode_buku[]" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Judul Buku</label>
                                    <input type="text" name="judul[]" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Penulis</label>
                                    <input type="text" name="nama_penulis[]" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Penerbit</label>
                                    <input type="text" name="nama_penerbit[]" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Kategori</label>
                                    <select name="kategori_id[]" class="form-control" required>
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach ($kategori as $k): ?>
                                            <option value="<?= $k['id'] ?>"><?= $k['nama_kategori'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Rak</label>
                                    <select name="rak_id[]" class="form-control" required>
                                        <option value="">Pilih Rak</option>
                                        <?php foreach ($rak as $r): ?>
                                            <option value="<?= $r['id'] ?>"><?= $r['nama_rak'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Jumlah Total</label>
                                    <input type="number" name="jumlah_total[]" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Jumlah Tersedia</label>
                                    <input type="number" name="jumlah_tersedia[]" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Kondisi</label>
                                    <select name="kondisi[]" class="form-control" required>
                                        <option value="baik">Baik</option>
                                        <option value="rusak">Rusak</option>
                                        <option value="hilang">Hilang</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Tanggal Masuk</label>
                                    <input type="date" name="tanggal_masuk[]" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="tambahBaris()">+ Tambah Baris</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Semua</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-lg-<?= $isAdmin ? '7' : '12' ?> mb-4">
        <div class="card-custom">
            <div class="card-header-custom"><i class="fas fa-list"></i> Daftar Buku</div>
            <div class="card-body-custom">
                <div class="table-responsive">
                    <table class="table table-custom table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Penerbit</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Kondisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $i => $b): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($b['kode_buku']) ?></td>
                                <td><?= htmlspecialchars($b['judul']) ?></td>
                                <td><?= htmlspecialchars($b['nama_penulis']) ?></td>
                                <td><?= htmlspecialchars($b['nama_penerbit']) ?></td>
                                <td><?= htmlspecialchars($b['nama_kategori']) ?></td>
                                <td><?= $b['jumlah_tersedia'] ?>/<?= $b['jumlah_total'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $b['kondisi'] == 'baik' ? 'success' : ($b['kondisi'] == 'rusak' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($b['kondisi']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (count($books) == 0): ?>
                            <tr><td colspan="8" class="text-center">Belum ada data buku.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function tambahBaris() {
    const container = document.getElementById('buku-container');
    const clone = container.firstElementChild.cloneNode(true);
    const inputs = clone.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.value = '';
    });
    container.appendChild(clone);
}
</script>

<?php include 'includes/footer.php'; ?>
