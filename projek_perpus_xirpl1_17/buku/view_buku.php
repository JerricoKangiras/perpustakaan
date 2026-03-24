<?php
// ==========================
// AMBIL PARAMETER
// ==========================
$current_sort = $_GET['sort'] ?? '';
$search       = $_GET['search'] ?? '';
$current_kategori = $_GET['kategori'] ?? '';
$current_penerbit = $_GET['penerbit'] ??'';

$where = "";
$sort  = "";

$where_conditions = [];

$where_conditions = [];

// ==========================
// SEARCH
// ==========================
if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($koneksi, $search);
    $where_conditions[] = "(s.judul_buku LIKE '%$search_safe%' 
                            OR s.author_buku LIKE '%$search_safe%')";
}

// ==========================
// FILTER KATEGORI
// ==========================
if (!empty($current_kategori)) {
    $kategori_safe = (int)$current_kategori;
    $where_conditions[] = "s.id_kategori = $kategori_safe";
}

// ==========================
// FILTER PENERBIT
// ==========================
if (!empty($current_penerbit)) {
    $penerbit_safe = (int)$current_penerbit;
    $where_conditions[] = "s.id_penerbit = $penerbit_safe";
}

// ==========================
// GABUNGKAN SEMUA KONDISI
// ==========================
$where = "";
if (!empty($where_conditions)) {
    $where = "WHERE " . implode(" AND ", $where_conditions);
}

// ==========================
// SORTING
// ==========================
switch ($current_sort) {

    // SORT JUDUL
    case 'judul_asc':
        $sort = "ORDER BY s.judul_buku ASC";
        break;
    case 'judul_desc':
        $sort = "ORDER BY s.judul_buku DESC";
        break;

    // SORT TAHUN
    case 'tahun_desc':
        $sort = "ORDER BY s.tahun_terbit DESC";
        break;
    case 'tahun_asc':
        $sort = "ORDER BY s.tahun_terbit ASC";
        break;

    // SORT ID
    case 'id_asc':
        $sort = "ORDER BY s.id_buku ASC";
        break;
    case 'id_desc':
        $sort = "ORDER BY s.id_buku DESC";
        break;

    // SORT STOK TERSEDIA
    case 'stok_asc':
        $sort = "ORDER BY s.jumlah_buku ASC";
        break;
        
    case "stok_desc":
        $sort = "ORDER BY s.jumlah_buku DESC";
        break;

    // SORT HALAMAN BUKU
    case 'halaman_asc':
        $sort = "ORDER BY s.jumlah_halaman ASC";
        break;
        
    case "halaman_desc":
        $sort = "ORDER BY s.jumlah_halaman DESC";
        break;    

    default:
        $sort = "ORDER BY s.id_buku DESC"; // default terbaru masuk
}

// ==========================
// QUERY
// ==========================
$data_kategori = mysqli_query($koneksi, "SELECT * FROM tbl_kategori ORDER BY nama_kategori ASC");
$data_penerbit = mysqli_query($koneksi, "SELECT * FROM tbl_penerbit ORDER BY nama_penerbit ASC");
$data = mysqli_query($koneksi, "
    SELECT s.*, 
           k.nama_kategori AS nama_kategori, 
           p.nama_penerbit 
    FROM tbl_buku s 
    LEFT JOIN tbl_kategori k ON s.id_kategori = k.id_kategori 
    LEFT JOIN tbl_penerbit p ON s.id_penerbit = p.id_penerbit
    $where
    $sort
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <meta charset="UTF-8">
    <title>Data Perpustakaan</title>
    <style>
/* ===============================
   GLOBAL
=================================*/
body {
    font-family: 'Poppins', sans-serif;
    background: #ffffff;
    margin: 0;
    color: #4b2e2e;
}

/* ===============================
   HEADER
=================================*/
.dashboard-header {
    max-width: 1100px;
    margin: 30px auto 0;
    padding: 18px 40px;
    background: linear-gradient(135deg, #6b3e26, #8b5e3c);
    border-radius: 50px;
    box-shadow: 0 8px 25px rgba(107,62,38,0.3);
    text-align: center;
    color: #fff8f0;
    font-size: 20px;
    font-weight: 600;
}

/* ===============================
   CONTAINER CARD
=================================*/
.dashboard-container {
    background: #f5e6d3;
    margin: 30px auto;
    padding: 30px;
    width: 92%;
    max-width: 1100px;
    border-radius: 25px;
    border: 1px solid #e0c3a3;
    box-shadow: 0 15px 40px rgba(139,94,60,0.15);
}

.dashboard-title {
    text-align: center;
    color: #5a3825;
    margin-bottom: 20px;
}

/* ===============================
   ADD BUTTON
=================================*/
.dashboard-add-btn {
    display: inline-block;
    background: linear-gradient(to right, #6b3e26, #8b5e3c);
    color: white;
    padding: 10px 18px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 20px;
    transition: 0.3s;
}

.dashboard-add-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(107,62,38,0.3);
}

/* ===============================
   FILTER SECTION
=================================*/
.dashboard-filter {
    background: #fffaf4;
    padding: 15px;
    border-radius: 20px;
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    border: 1px solid #ead7c2;
}

.dashboard-filter input,
.dashboard-filter select {
    padding: 8px 12px;
    border-radius: 12px;
    border: 1px solid #d2b48c;
    background: #fffdf9;
    color: #4b2e2e;
}

.dashboard-filter button {
    background: #8b5e3c;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 30px;
    cursor: pointer;
}

.dashboard-filter button:hover {
    background: #6b3e26;
}

/* ===============================
   TABLE
=================================*/
.dashboard-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.dashboard-table th,
.dashboard-table td {
    padding: 10px;
    border: 1px solid #e6cdb4;
    text-align: center;
}

.dashboard-table th {
    background: #8b5e3c;
    color: white;
}

.dashboard-table tr:nth-child(even) {
    background: #fff3e6;
}

.dashboard-table tr:hover {
    background: #fde7d3;
}

/* ===============================
   ACTION BUTTONS
=================================*/
.dashboard-action {
    display: flex;
    justify-content: center;
    gap: 8px;
}

.btn-edit {
    background: #c97b3a;
    color: white;
    padding: 6px 14px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 13px;
    transition: 0.3s;
}

.btn-edit:hover {
    background: #a45c24;
}

.btn-delete {
    background: #a94442;
    color: white;
    padding: 6px 14px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 13px;
    transition: 0.3s;
}

.btn-delete:hover {
    background: #7a2f2f;
}

/* ===============================
   RESPONSIVE
=================================*/
@media (max-width: 768px) {

    .dashboard-filter {
        flex-direction: column;
        align-items: stretch;
    }

    .dashboard-table,
    .dashboard-table thead,
    .dashboard-table tbody,
    .dashboard-table th,
    .dashboard-table td,
    .dashboard-table tr {
        display: block;
    }

    .dashboard-table tr {
        margin-bottom: 15px;
        border-radius: 15px;
        padding: 10px;
        background: #fff;
    }

    .dashboard-table td {
        border: none;
        display: flex;
        justify-content: space-between;
    }

    .dashboard-table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6b3e26;
    }
}
    </style>
</head>

<body>
   <header class="dashboard-header">
📚 Data Perpustakaan
</header>

   <div class="dashboard-container">

    <h2 class="dashboard-title">Daftar Buku Perpustakaan</h2>

    <div class="dashboard-topbar">
        <a href="dashboard.php?page=tambah_buku" 
           class="dashboard-add-btn">
           + Tambah Buku
        </a>
    </div>

    <!-- FORM SEARCH + SORT -->
    <form class="dashboard-filter" method="GET" action="dashboard.php">
        
        <input type="hidden" name="page" value="buku">

        <!-- SEARCH -->
        <input type="text" 
               name="search" 
               placeholder="Cari judul / penulis..." 
               value="<?= htmlspecialchars($search); ?>"
               style="padding:6px;">

       <!-- FILTER KATEGORI (BISA DIKETIK) -->
   <select name="kategori" class="select-kategori" style="width:200px;">
        <option value="">-- Semua Kategori --</option>
        <?php while ($kat = mysqli_fetch_assoc($data_kategori)) { ?>
            <option value="<?= $kat['id_kategori']; ?>"
                <?= ($current_kategori == $kat['id_kategori']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($kat['nama_kategori']); ?>
            </option>
        <?php } ?>
    </select>

 <!-- FILTER PENERBIT -->
<select name="penerbit" class="select-penerbit" style="width:200px;">
    <option value="">-- Semua Penerbit --</option>
    <?php while ($penerbit = mysqli_fetch_assoc($data_penerbit)) { ?>
        <option value="<?= $penerbit['id_penerbit']; ?>"
            <?= ($current_penerbit == $penerbit['id_penerbit']) ? 'selected' : ''; ?>>
            <?= htmlspecialchars($penerbit['nama_penerbit']); ?>
        </option>
    <?php } ?>
</select>

        <!-- SORT -->
       <select name="sort" class="select-sort" style="width:200px;">
            <option value="">-- Urutkan --</option>

            <optgroup label="Berdasarkan ID">
                <option value="id_desc" <?= ($current_sort=='id_desc')?'selected':''; ?>>ID Terbesar</option>
                <option value="id_asc" <?= ($current_sort=='id_asc')?'selected':''; ?>>ID Terkecil</option>
            </optgroup>

            <optgroup label="Berdasarkan Judul">
                <option value="judul_asc" <?= ($current_sort=='judul_asc')?'selected':''; ?>>Judul A-Z</option>
                <option value="judul_desc" <?= ($current_sort=='judul_desc')?'selected':''; ?>>Judul Z-A</option>
            </optgroup>

            <optgroup label="Berdasarkan Jumlah Halaman">
                <option value="halaman_asc" <?= ($current_sort=='halaman_asc')?'selected':''; ?>>Jumlah Halaman Tersedikit</option>
                <option value="halaman_desc" <?= ($current_sort=='halaman_desc')?'selected':''; ?>>Jumlah Halaman Terbanyak</option>
            </optgroup>

            <optgroup label="Berdasarkan Stok Tersedia">
                    <option value="stok_asc" <?= ($current_sort=='stok_asc')?'selected':''; ?>>Stok Tersedia Tersedikit</option>
                    <option value="stok_desc" <?= ($current_sort=='stok_desc')?'selected':''; ?>>Stok Tersedia Terbanyak</option>
            </optgroup>

            <optgroup label="Berdasarkan Tahun">
                <option value="tahun_desc" <?= ($current_sort=='tahun_desc')?'selected':''; ?>>Tahun Terbaru</option>
                <option value="tahun_asc" <?= ($current_sort=='tahun_asc')?'selected':''; ?>>Tahun Terlama</option>
            </optgroup>

        </select>

        <button type="submit" style="padding:6px 12px;">Terapkan</button>
    </form>

   <table class="dashboard-table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Kategori</th>
                <th>Penerbit</th>
                <th>Tahun</th>
                <th>Jumlah Halaman</th>
                <th>Stok Tersedia</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($data) == 0) {
                echo "<tr><td colspan='8' style='text-align:center;'>Data tidak ditemukan</td></tr>";
            } else {
                $no = 1;
                while ($row = mysqli_fetch_assoc($data)) {
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= (int)$row['id_buku']; ?></td>
                <td><?= htmlspecialchars($row['judul_buku']); ?></td>
                <td><?= htmlspecialchars($row['author_buku']); ?></td>
                <td><?= htmlspecialchars($row['nama_kategori'] ?? '-'); ?></td>
                <td><?= htmlspecialchars($row['nama_penerbit'] ?? '-'); ?></td>
                <td><?= (int)$row['tahun_terbit']; ?></td>
                <td><?= (int)$row['jumlah_halaman']; ?></td>
                <td><?= (int)$row['jumlah_buku']; ?></td>
               <td>
    <div class="dashboard-action">
        <a href="dashboard.php?page=edit_buku&id_buku=<?= $row['id_buku']; ?>" 
           class="btn-edit">Edit</a>

        <a href="dashboard.php?page=hapus_buku&id_buku=<?= $row['id_buku']; ?>"
           class="btn-delete"
           onclick="return confirm('Yakin ingin menghapus data ini?')">
           Hapus
        </a>
    </div>
</td>
            </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>

<script>
$(document).ready(function() {
    $('.select-kategori').select2({
        placeholder: "Pilih kategori",
        allowClear: true
    });

    $('.select-penerbit').select2({
        placeholder: "Pilih penerbit",
        allowClear: true
    });

    $('.select-sort').select2({
        placeholder: "Pilih urutan",
        allowClear: true
    });
});
</script>

</div>
</body>

</html>