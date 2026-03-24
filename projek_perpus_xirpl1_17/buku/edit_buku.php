<?php
/* ===== VALIDASI ID ===== */
if (!isset($_GET['id_buku']) || !is_numeric($_GET['id_buku'])) {
    die("ID buku tidak valid");
}

$id_buku = (int) $_GET['id_buku'];

/* ===== AMBIL DATA BUKU (AMAN) ===== */
$stmt = mysqli_prepare($koneksi, "SELECT * FROM tbl_buku WHERE id_buku = ?");
mysqli_stmt_bind_param($stmt, "i", $id_buku);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row) {
    die("Data buku tidak ditemukan");
}
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
    <title>Tambah Buku</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* BODY PUTIH */
        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
            color: #4b2e2e;
            min-height: 100vh;
        }

        /* HEADER - COKELAT KAYU */
        .dashboard-header {
            max-width: 950px;
            margin: 30px auto 0;
            padding: 18px 40px;

            background: linear-gradient(135deg, #6b3e26, #8b5e3c);
            border-radius: 50px;
            box-shadow: 0 8px 25px rgba(107, 62, 38, 0.3);

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: 600;
            color: #fff8f0;
            letter-spacing: 1px;
        }

        /* MAIN */
        .dashboard-main {
            display: flex;
            justify-content: center;
            padding: 40px 20px 80px;
        }

        /* CARD - WARNA KREM */
        .card {
            width: 100%;
            max-width: 950px;
            background: #f5e6d3;
            padding: 50px;
            border-radius: 25px;
            border: 1px solid #e0c3a3;
            box-shadow: 0 15px 40px rgba(139, 94, 60, 0.15);
        }

        .card h2 {
            text-align: center;
            margin-bottom: 8px;
            font-weight: 600;
            color: #5a3825;
        }

        .card p {
            text-align: center;
            font-size: 14px;
            margin-bottom: 40px;
            color: #7a5a45;
        }

        /* FORM */
        form {
            width: 100%;
        }

        /* GRID */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: #5a3825;
        }

        /* INPUT */
        input,
        textarea,
        select {
            width: 100%;
            padding: 13px 15px;
            background: #fffdf9;
            border: 1px solid #d2b48c;
            border-radius: 12px;
            color: #4b2e2e;
            font-size: 14px;
            transition: 0.3s ease;
        }

        textarea {
            resize: none;
            min-height: 120px;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #8b5e3c;
            box-shadow: 0 0 8px rgba(139, 94, 60, 0.3);
        }

        /* FULL WIDTH */
        .form-group.full {
            grid-column: span 2;
        }

        /* BUTTON */
        .button-group {
            display: flex;
            gap: 25px;
            margin-top: 40px;
        }

        .btn-primary,
        .btn-secondary {
            flex: 1;
            padding: 14px;
            border-radius: 50px;
            font-weight: 600;
            text-align: center;
            transition: 0.3s ease;
        }

        /* PRIMARY - COKELAT */
        .btn-primary {
            border: none;
            background: linear-gradient(to right, #6b3e26, #8b5e3c);
            color: white;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(107, 62, 38, 0.4);
        }

        /* SECONDARY */
        .btn-secondary {
            border: 1px solid #8b5e3c;
            text-decoration: none;
            color: #6b3e26;
        }

        .btn-secondary:hover {
            background: #8b5e3c;
            color: white;
            transform: translateY(-3px);
        }

        .select2-container--default .select2-selection--single {
            height: 45px;
            border-radius: 12px;
            border: 1px solid #d2b48c;
            background: #fffdf9;
            padding: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #4b2e2e;
            line-height: 28px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: span 1;
            }

            .card {
                padding: 35px 25px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<script>
    $(document).ready(function () {
        $('.select-search').select2({
            placeholder: "Ketik untuk mencari...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<body>

    <!-- DASHBOARD HEADER -->
    <header class="dashboard-header">
        <div class="logo">📚 PERPUSTAKAAN DIGITAL</div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="dashboard-main">
        <div class="card">
            <h2>Edit Buku</h2>
            <p>Ubah informasi buku dengan detail lengkap</p>

            <form method="POST" action="dashboard.php?page=simpan_edit_buku">
                <div class="form-row">
                    <div class="form-group">
                        <label>ID Buku</label>
                        <input type="text" name="id_buku" value="<?= (int) $row['id_buku']; ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" name="judul_buku" required
                            value="<?= htmlspecialchars($row['judul_buku']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Author Buku</label>
                    <input type="text" name="author_buku" required
                        value="<?= htmlspecialchars($row['author_buku']); ?>">
                </div>
                <div class="form-group">
                    <label>Sinopsis Buku</label>
                    <textarea name="sinopsis_buku"><?= htmlspecialchars($row['sinopsis_buku']); ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Jumlah Halaman</label>
                        <input type="number" name="jumlah_halaman" value="<?= (int) $row['jumlah_halaman']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Buku</label>
                        <input type="number" name="jumlah_buku" value="<?= (int) $row['jumlah_buku']; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" class="select-search" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php
                            $kategori = mysqli_query($koneksi, "SELECT id_kategori, nama_kategori FROM tbl_kategori");
                            while ($k = mysqli_fetch_assoc($kategori)) {
                                $selected = ($row['id_kategori'] == $k['id_kategori']) ? 'selected' : '';
                                echo "<option value='{$k['id_kategori']}' $selected>" . htmlspecialchars($k['nama_kategori']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Penerbit</label>
                        <select name="id_penerbit" class="select-search" required>
                            <option value="">-- Pilih Penerbit --</option>
                            <?php
                            $penerbit = mysqli_query($koneksi, "SELECT id_penerbit, nama_penerbit FROM tbl_penerbit");
                            while ($p = mysqli_fetch_assoc($penerbit)) {
                                $selected = ($row['id_penerbit'] == $p['id_penerbit']) ? 'selected' : '';
                                echo "<option value='{$p['id_penerbit']}' $selected>" . htmlspecialchars($p['nama_penerbit']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group"></div>
                <label>Tahun Terbit</label>
                <input type="number" name="tahun_terbit" value="<?= (int) $row['tahun_terbit']; ?>">

        <div class="button-group">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="dashboard.php?page=buku" class="btn-secondary">Kembali</a>
        </div>

        </form>
        </div>
    </main>

</body>

</html>