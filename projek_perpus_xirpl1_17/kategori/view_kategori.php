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
    $where = "WHERE nama_kategori LIKE '%$search_safe%'";
}

// ==========================
// SORTING
// ==========================
switch ($current_sort) {

    case 'id_asc':
        $sort = "ORDER BY id_kategori ASC";
        break;

    case 'id_desc':
        $sort = "ORDER BY id_kategori DESC";
        break;

    case 'nama_asc':
        $sort = "ORDER BY nama_kategori ASC";
        break;

    case 'nama_desc':
        $sort = "ORDER BY nama_kategori DESC";
        break;

    default:
        $sort = "ORDER BY id_kategori DESC"; // default terbaru
}

// ==========================
// QUERY
// ==========================
$data = mysqli_query($koneksi, "
    SELECT * FROM tbl_kategori
    $where
    $sort
");
?>
<!DOCTYPE html>
<html lang="id">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<head>
    <meta charset="UTF-8">
    <title>Data Kategori</title>
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    background: #ffffff;
    margin: 0;
    color: #1e293b;
}

/* ===============================
   HEADER
=================================*/
.dashboard-header {
    max-width: 1100px;
    margin: 30px auto 0;
    padding: 18px 40px;
    background: linear-gradient(135deg, #1e3a8a, #1e40af);
    border-radius: 50px;
    box-shadow: 0 8px 25px rgba(30,58,138,0.35);
    text-align: center;
    color: #fff8f0;
    font-size: 20px;
    font-weight: 600;
}

/* ===============================
   CONTAINER CARD
=================================*/
.dashboard-container {
    background: #eff6ff;
    margin: 30px auto;
    padding: 30px;
    width: 92%;
    max-width: 1100px;
    border-radius: 25px;
    border: 1px solid #bfdbfe;
    box-shadow: 0 15px 40px rgba(30,58,138,0.20);
}

.dashboard-title {
    text-align: center;
    color: #1e3a8a;
    margin-bottom: 20px;
}

/* ===============================
   ADD BUTTON
=================================*/
.dashboard-add-btn {
    display: inline-block;
    background: linear-gradient(to right, #1e3a8a, #1e40af);
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
    box-shadow: 0 10px 25px rgba(30,58,138,0.5);
}

/* ===============================
   FILTER SECTION
=================================*/
.dashboard-filter {
    background: #f0f7ff;
    padding: 15px;
    border-radius: 20px;
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    border: 1px solid #c7d2fe;
}

.dashboard-filter input,
.dashboard-filter select {
    padding: 8px 12px;
    border-radius: 12px;
    border: 1px solid #93c5fd;
    background: #f9fbff;
    color: #1e3a8a;
}

.dashboard-filter button {
    background: #1e3a8a;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 30px;
    cursor: pointer;
}

.dashboard-filter button:hover {
    background: #172554;
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
    border: 1px solid #bfdbfe; /* biru lembut */
    text-align: center;
}

.dashboard-table th {
   background: #1e3a8a;
    color: white;
}

.dashboard-table tr:nth-child(even) {
    background: #f0f7ff;
}

.dashboard-table tr:hover {
    background: #dbeafe;
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
    background: #2563eb;
    color: white;
    padding: 6px 14px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 13px;
    transition: 0.3s;
}

.btn-edit:hover {
    background: #1e40af;
}

.btn-delete {
    background: #172554;
    color: white;
    padding: 6px 14px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 13px;
    transition: 0.3s;
}

.btn-delete:hover {
    background: #0f172a;
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
        background: #ffffff; /* tetap putih */
        box-shadow: 0 8px 20px rgba(30,58,138,0.15); /* shadow biru */
        border: 1px solid #bfdbfe;
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
        color: #1e3a8a; /* biru utama */
    }
}
    </style>
</head>

<body>
   <header class="dashboard-header">
📚 Data Perpustakaan
</header>

   <div class="dashboard-container">

    <h2 class="dashboard-title">Daftar Kategori Buku</h2>

    <div class="dashboard-topbar">
        <a href="dashboard.php?page=tambah_kategori" 
           class="dashboard-add-btn">
           + Tambah Kategori
        </a>
    </div>

    <!-- FORM SEARCH + SORT -->
    <form class="dashboard-filter" method="GET" action="dashboard.php">
        
        <input type="hidden" name="page" value="kategori">

        <!-- SEARCH -->
        <input type="text" 
               name="search"
               placeholder="Cari nama kategori..."
               value="<?= htmlspecialchars($search); ?>"
               style="padding:6px;">

        <!-- SORT -->
        <select name="sort" class="select-sort" style="width:200px;">
            <option value="">-- Urutkan --</option>

            <optgroup label="Berdasarkan ID">
                <option value="id_desc" <?= ($current_sort=='id_desc')?'selected':''; ?>>
                    ID Terbesar
                </option>
                <option value="id_asc" <?= ($current_sort=='id_asc')?'selected':''; ?>>
                    ID Terkecil
                </option>
            </optgroup>

            <optgroup label="Berdasarkan Nama">
                <option value="nama_asc" <?= ($current_sort=='nama_asc')?'selected':''; ?>>
                    Nama A-Z
                </option>
                <option value="nama_desc" <?= ($current_sort=='nama_desc')?'selected':''; ?>>
                    Nama Z-A
                </option>
            </optgroup>
        </select>

        <button type="submit" style="padding:6px 12px;">Terapkan</button>
    </form>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>ID Kategori</th>
                <th>Nama Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

        <?php
        if (mysqli_num_rows($data) == 0) {
            echo "<tr><td colspan='3' style='text-align:center;'>Data tidak ditemukan</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($data)) {
        ?>

            <tr>
                <td data-label="ID Kategori">
                    <?= (int)$row['id_kategori']; ?>
                </td>

                <td data-label="Nama Kategori">
                    <?= htmlspecialchars($row['nama_kategori']); ?>
                </td>

                <td class="dashboard-action" data-label="Aksi">
                    <a href="dashboard.php?page=edit_kategori&id_kategori=<?= $row['id_kategori']; ?>"
                       class="btn-edit">
                        Edit
                    </a>
                    <a href="dashboard.php?page=hapus_kategori&id_kategori=<?= $row['id_kategori']; ?>"
                       onclick="return confirm('Yakin ingin menghapus data ini?')"
                       class="btn-delete">
                        Hapus
                    </a>
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
    $('.select-sort').select2({
        placeholder: "Pilih urutan",
        allowClear: true
    });
});
</script>
</div>
</body>

</html>