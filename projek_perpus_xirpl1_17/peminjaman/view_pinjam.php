<?php
date_default_timezone_set("Asia/Jakarta");
$hari_ini = date("Y-m-d");

$current_sort   = $_GET['sort'] ?? '';
$current_status = $_GET['status_filter'] ?? '';
$search         = $_GET['search'] ?? '';
$filter_tanggal = $_GET['filter_tanggal'] ?? '';

$where = [];
$order = "";

/* ==========================
   SEARCH
========================== */
if (!empty($search)) {
    $safe = mysqli_real_escape_string($koneksi, $search);
    $where[] = "(s.nama_peminjam LIKE '%$safe%' 
                OR s.email_peminjam LIKE '%$safe%' 
                OR b.judul_buku LIKE '%$safe%')";
}

/* ==========================
   FILTER STATUS
========================== */
if (!empty($current_status)) {
    $safe_status = mysqli_real_escape_string($koneksi, $current_status);
    $where[] = "s.status = '$safe_status'";
}

/* ==========================
   FILTER TANGGAL
========================== */
if ($filter_tanggal == "akan_habis") {
    $where[] = "s.status = 'dipinjamkan' 
                AND s.tanggal_kembali BETWEEN '$hari_ini' 
                AND DATE_ADD('$hari_ini', INTERVAL 3 DAY)";
}

if ($filter_tanggal == "baru") {
    $where[] = "s.tanggal_pinjam = '$hari_ini'";
}

/* ==========================
   SORTING
========================== */
switch ($current_sort) {

    case 'id_asc':
        $order = "ORDER BY s.id_peminjam ASC";
        break;

    case 'id_desc':
        $order = "ORDER BY s.id_peminjam DESC";
        break;

    case 'tanggal_desc':
        $order = "ORDER BY s.tanggal_pinjam DESC";
        break;

    case 'tanggal_asc':
        $order = "ORDER BY s.tanggal_pinjam ASC";
        break;

    default:
        $order = "ORDER BY s.tanggal_pinjam DESC";
}

/* ==========================
   BUILD QUERY
========================== */
$where_sql = "";
if (!empty($where)) {
    $where_sql = "WHERE " . implode(" AND ", $where);
}

$query = "
    SELECT s.*, b.judul_buku
    FROM tbl_peminjaman s
    LEFT JOIN tbl_buku b ON s.id_buku = b.id_buku
    $where_sql
    $order
";

$data = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<head>
<meta charset="UTF-8">
<title>Data Peminjaman</title>

<style>
/* ===============================
   GLOBAL
=================================*/
body {
    font-family: 'Poppins', sans-serif;
    background: #ffffff;
    margin: 0;
    color: #4a1c1c;
}

/* ===============================
   HEADER MERAH DONKER
=================================*/
.dashboard-header {
    max-width: 1100px;
    margin: 30px auto 0;
    padding: 18px 40px;
    background: linear-gradient(135deg, #7f1d1d, #991b1b);
    border-radius: 50px;
    box-shadow: 0 8px 25px rgba(127,29,29,0.35);
    text-align: center;
    color: #fff;
    font-size: 20px;
    font-weight: 600;
}

/* ===============================
   CONTAINER
=================================*/
.dashboard-container {
    background: #fef2f2;
    margin: 30px auto;
    padding: 30px;
    width: 92%;
    max-width: 1100px;
    border-radius: 25px;
    border: 1px solid #fecaca;
    box-shadow: 0 15px 40px rgba(153,27,27,0.15);
}

/* ===============================
   FILTER CLEAN MODERN
=================================*/

.dashboard-filter {
    background: #fff5f5;
    padding: 25px;
    border-radius: 25px;
    margin-bottom: 25px;
    border: 1px solid #fca5a5;
}

/* SEARCH FULL WIDTH */
.search-input {
    width: 100%;
    padding: 14px 20px;
    border-radius: 40px;
    border: 1px solid #f87171;
    margin-bottom: 18px;
    font-size: 14px;
    outline: none;
}

.search-input:focus {
    border-color: #991b1b;
    box-shadow: 0 0 0 3px rgba(153,27,27,0.15);
}

/* ROW DROPDOWN */
.filter-row {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

/* SELECT */
.select-filter {
    min-width: 210px;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid #f87171;
    background: #fff;
    font-size: 14px;
}

/* BUTTON */
.btn-terapkan {
    background: #991b1b;
    color: white;
    border: none;
    padding: 10px 22px;
    border-radius: 40px;
    cursor: pointer;
    font-weight: 500;
    transition: 0.25s ease;
}

.btn-terapkan:hover {
    background: #7f1d1d;
    transform: translateY(-1px);
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
        align-items: stretch;
    }

    .select-filter,
    .btn-terapkan {
        width: 100%;
    }
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
    border: 1px solid #fecaca;
    text-align: center;
}

.dashboard-table th {
    background: #991b1b;
    color: white;
}

.dashboard-table tr:nth-child(even) {
    background: #fff1f1;
}

.dashboard-table tr:hover {
    background: #ffe4e4;
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
        padding: 15px;
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(153,27,27,0.15);
    }

    .dashboard-table td {
        border: none;
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
    }

    .dashboard-table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #991b1b;
    }
}

.select2-container--default .select2-selection--single {
    border-radius: 12px;
    border: 1px solid #f87171;
    height: 38px;
    padding: 5px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #7f1d1d;
}

.select2-container--default .select2-results__option--highlighted {
    background-color: #991b1b !important;
}
</style>
</head>

<body>

<header class="dashboard-header">
📚 Data Peminjaman Perpustakaan
</header>

<div class="dashboard-container">

<form method="GET" action="dashboard.php" class="dashboard-filter">

    <input type="hidden" name="page" value="peminjaman">

    <!-- SEARCH FULL WIDTH -->
    <input type="text" 
           name="search"
           class="search-input"
           placeholder="Cari nama / email / buku..."
           value="<?= htmlspecialchars($search); ?>">

    <!-- FILTER ROW -->
    <div class="filter-row">

        <select name="status_filter" class="select-filter">
            <option value="">Pilih status</option>
            <?php
            $status_list = [
                "dipinjamkan",
                "dikembalikan",
                "terlambat",
                "menunggu",
                "menunggu_konfirmasi_kembali",
                "ditolak"
            ];
            foreach ($status_list as $st) {
                $selected = ($current_status == $st) ? "selected" : "";
                echo "<option value='$st' $selected>" . ucfirst(str_replace("_"," ",$st)) . "</option>";
            }
            ?>
        </select>

        <select name="filter_tanggal" class="select-filter">
            <option value="">Pilih tanggal</option>
            <option value="akan_habis" <?= ($filter_tanggal=='akan_habis')?'selected':''; ?>>
                Akan Habis (3 Hari)
            </option>
            <option value="baru" <?= ($filter_tanggal=='baru')?'selected':''; ?>>
                Baru Hari Ini
            </option>
        </select>

        <select name="sort" class="select-filter">
            <option value="">Pilih urutan</option>

            <optgroup label="ID">
                <option value="id_desc" <?= ($current_sort=='id_desc')?'selected':''; ?>>ID Terbesar</option>
                <option value="id_asc" <?= ($current_sort=='id_asc')?'selected':''; ?>>ID Terkecil</option>
            </optgroup>

            <optgroup label="Tanggal Pinjam">
                <option value="tanggal_desc" <?= ($current_sort=='tanggal_desc')?'selected':''; ?>>Terbaru</option>
                <option value="tanggal_asc" <?= ($current_sort=='tanggal_asc')?'selected':''; ?>>Terlama</option>
            </optgroup>
        </select>

        <button type="submit" class="btn-terapkan">
            Terapkan
        </button>

    </div>
</form>

<table class="dashboard-table">
<thead>
<tr>
    <th>ID</th>
    <th>Judul Buku</th>
    <th>Nama</th>
    <th>Email</th>
    <th>No HP</th>
    <th>Tanggal Pinjam</th>
    <th>Tanggal Kembali</th>
    <th>Kembali Aktual</th>
    <th>Status</th>
</tr>
</thead>
<tbody>

<?php
if (mysqli_num_rows($data) == 0) {
    echo "<tr><td colspan='9'>Data tidak ditemukan</td></tr>";
} else {
    while ($row = mysqli_fetch_assoc($data)) {

        $dbStatus = strtolower(trim($row['status'] ?? ''));

        if ($dbStatus == 'dipinjamkan' && $hari_ini > $row['tanggal_kembali']) {
            $status = "Terlambat";
            $warna = "#dc2626";
        } else {
            $status = ucfirst(str_replace("_"," ",$dbStatus));
            $warna = "#991b1b";
        }
?>

<tr>
    <td data-label="ID"><?= (int)$row['id_peminjam']; ?></td>
    <td data-label="Judul"><?= htmlspecialchars($row['judul_buku'] ?? '-'); ?></td>
    <td data-label="Nama"><?= htmlspecialchars($row['nama_peminjam']); ?></td>
    <td data-label="Email"><?= htmlspecialchars($row['email_peminjam']); ?></td>
    <td data-label="No HP"><?= htmlspecialchars($row['notlp_peminjam']); ?></td>
    <td data-label="Tgl Pinjam"><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
    <td data-label="Tgl Kembali"><?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
    <td data-label="Kembali Aktual">
        <?= !empty($row['tanggal_kembali_aktual']) ? date('d-m-Y', strtotime($row['tanggal_kembali_aktual'])) : '-'; ?>
    </td>
    <td data-label="Status" style="color:<?= $warna ?>; font-weight:bold;">
        <?= $status; ?>
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

    $('.select-filter').select2({
        placeholder: "Pilih opsi",
        allowClear: true,
        width: 'resolve'
    });

});
</script>
</div>
</body>
</html>