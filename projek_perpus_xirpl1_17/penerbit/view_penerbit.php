<?php
// ==========================
// AMBIL PARAMETER
// ==========================
$current_sort = $_GET['sort'] ?? '';
$search       = $_GET['search'] ?? '';

$where = "";
$sort  = "";

// ==========================
// SEARCH
// ==========================
if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($koneksi, $search);
    $where = "WHERE nama_penerbit LIKE '%$search_safe%' 
              OR nama_sales LIKE '%$search_safe%'";
}

// ==========================
// SORTING
// ==========================
switch ($current_sort) {

    case 'id_asc':
        $sort = "ORDER BY id_penerbit ASC";
        break;

    case 'id_desc':
        $sort = "ORDER BY id_penerbit DESC";
        break;

    case 'nama_asc':
        $sort = "ORDER BY nama_penerbit ASC";
        break;

    case 'nama_desc':
        $sort = "ORDER BY nama_penerbit DESC";
        break;

    default:
        $sort = "ORDER BY id_penerbit DESC"; // default terbaru
}

// ==========================
// QUERY
// ==========================
$data = mysqli_query($koneksi, "
    SELECT * FROM tbl_penerbit
    $where
    $sort
");
?>

<!DOCTYPE html>
<html lang="id">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<head>
    <meta charset="UTF-8">
    <title>Data Penerbit</title>

    <!-- COPY STYLE DARI view_kategori.php -->
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    background: #ffffff;
    margin: 0;
    color: #1f2937;
}

/* ================= HEADER ================= */
.dashboard-header {
    max-width: 1100px;
    margin: 30px auto 0;
    padding: 18px 40px;
    background: linear-gradient(135deg, #166534, #15803d); /* hijau */
    border-radius: 50px;
    box-shadow: 0 8px 25px rgba(22,101,52,0.35);
    text-align: center;
    color: #f0fdf4;
    font-size: 20px;
    font-weight: 600;
}

/* ================= CONTAINER ================= */
.dashboard-container {
    background: #f0fdf4; /* hijau sangat lembut */
    margin: 30px auto;
    padding: 30px;
    width: 92%;
    max-width: 1100px;
    border-radius: 25px;
    border: 1px solid #bbf7d0;
    box-shadow: 0 15px 40px rgba(22,101,52,0.20);
}

.dashboard-title {
    text-align: center;
    color: #166534;
    margin-bottom: 20px;
}

/* ================= ADD BUTTON ================= */
.dashboard-add-btn {
    display: inline-block;
    background: linear-gradient(to right, #166534, #15803d);
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
    box-shadow: 0 10px 25px rgba(22,101,52,0.5);
}

/* ================= FILTER ================= */
.dashboard-filter {
    background: #ecfdf5;
    padding: 15px;
    border-radius: 20px;
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    border: 1px solid #bbf7d0;
}

.dashboard-filter input,
.dashboard-filter select {
    padding: 8px 12px;
    border-radius: 12px;
    border: 1px solid #86efac;
    background: #f9fffb;
    color: #166534;
}

.dashboard-filter button {
    background: #166534;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 30px;
    cursor: pointer;
}

.dashboard-filter button:hover {
    background: #14532d;
}

/* ================= TABLE ================= */
.dashboard-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.dashboard-table th,
.dashboard-table td {
    padding: 10px;
    border: 1px solid #bbf7d0;
    text-align: center;
}

.dashboard-table th {
    background: #166534;
    color: white;
}

.dashboard-table tr:nth-child(even) {
    background: #f0fdf4;
}

.dashboard-table tr:hover {
    background: #dcfce7;
}

/* ================= ACTION ================= */
.dashboard-action {
    display: flex;
    justify-content: center;
    gap: 8px;
}

.btn-edit {
    background: #16a34a;
    color: white;
    padding: 6px 14px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 13px;
}

.btn-edit:hover {
    background: #15803d;
}

.btn-delete {
    background: #14532d;
    color: white;
    padding: 6px 14px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 13px;
}

.btn-delete:hover {
    background: #052e16;
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
        box-shadow: 0 8px 20px rgba(22,101,52,0.15); /* shadow hijau */
        border: 1px solid #bbf7d0; /* border hijau lembut */
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
        color: #166534; /* hijau utama */
    }
}
</style>
</head>

<body>

<header class="dashboard-header">
📚 Data Perpustakaan
</header>

<div class="dashboard-container">

<h2 class="dashboard-title">Data Penerbit Buku</h2>

<a href="dashboard.php?page=tambah_penerbit" class="dashboard-add-btn">
+ Tambah Penerbit
</a>

<form class="dashboard-filter" method="GET" action="dashboard.php">
    <input type="hidden" name="page" value="penerbit">

    <input type="text" 
           name="search"
           placeholder="Cari penerbit / sales..."
           value="<?= htmlspecialchars($search); ?>">

    <select name="sort" class="select-sort" style="width:200px;">
        <option value="">-- Urutkan --</option>
        <option value="id_desc" <?= ($current_sort=='id_desc')?'selected':''; ?>>ID Terbesar</option>
        <option value="id_asc" <?= ($current_sort=='id_asc')?'selected':''; ?>>ID Terkecil</option>
        <option value="nama_asc" <?= ($current_sort=='nama_asc')?'selected':''; ?>>Nama A-Z</option>
        <option value="nama_desc" <?= ($current_sort=='nama_desc')?'selected':''; ?>>Nama Z-A</option>
    </select>

    <button type="submit">Terapkan</button>
</form>

<table class="dashboard-table">
<thead>
<tr>
    <th>ID</th>
    <th>Nama Penerbit</th>
    <th>No Telp Penerbit</th>
    <th>Nama Sales</th>
    <th>No Telp Sales</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>

<?php
if (mysqli_num_rows($data) == 0) {
    echo "<tr><td colspan='6'>Data tidak ditemukan</td></tr>";
} else {
    while ($row = mysqli_fetch_assoc($data)) {
?>

<tr>
<td><?= $row['id_penerbit']; ?></td>
<td><?= htmlspecialchars($row['nama_penerbit']); ?></td>
<td><?= htmlspecialchars($row['notlp_penerbit']); ?></td>
<td><?= htmlspecialchars($row['nama_sales']); ?></td>
<td><?= htmlspecialchars($row['notlp_sales']); ?></td>
<td class="dashboard-action">
    <a href="dashboard.php?page=edit_penerbit&id_penerbit=<?= $row['id_penerbit']; ?>" class="btn-edit">Edit</a>
    <a href="dashboard.php?page=hapus_penerbit&id_penerbit=<?= $row['id_penerbit']; ?>" 
       onclick="return confirm('Yakin ingin menghapus data ini?')" 
       class="btn-delete">Hapus</a>
</td>
</tr>

<?php } } ?>

</tbody>
</table>

<script>
$(document).ready(function() {
    $('.select-sort').select2({
        placeholder: "Pilih urutan",
        allowClear: true
    });
});
</script>

</div>
</body>
</html>