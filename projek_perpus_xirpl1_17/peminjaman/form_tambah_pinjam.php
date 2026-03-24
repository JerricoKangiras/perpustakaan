<?php
// Gunakan session username yang sudah dicek di dashboard
$username = $_SESSION['username'];

// Ambil data user langsung dari DB
$stmt = mysqli_prepare($koneksi, "SELECT username, email, no_tlp FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "User tidak ditemukan di database!";
    exit;
}

// Ambil id buku jika ada
$id_buku_terpilih = isset($_GET['id_buku']) ? (int) $_GET['id_buku'] : '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Data Peminjam</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function hitungTanggalKembali() {
            let tanggalPinjam = document.getElementById("tanggal_pinjam").value;
            let durasi = document.getElementById("durasi_pinjam").value;

            if (tanggalPinjam !== "" && durasi !== "") {
                let tgl = new Date(tanggalPinjam);
                tgl.setDate(tgl.getDate() + parseInt(durasi));

                let tahun = tgl.getFullYear();
                let bulan = String(tgl.getMonth() + 1).padStart(2, '0');
                let hari = String(tgl.getDate()).padStart(2, '0');

                document.getElementById("tanggal_kembali").value =
                    tahun + "-" + bulan + "-" + hari;
            }
        }
    </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
            color: #5a1f1f;
            min-height: 100vh;
        }

        /* HEADER MERAH */
        .dashboard-header {
            max-width: 950px;
            margin: 30px auto 0;
            padding: 18px 40px;
            background: linear-gradient(135deg, #7a1f1f, #b91c1c);
            border-radius: 50px;
            box-shadow: 0 8px 25px rgba(185, 28, 28, 0.3);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: 600;
            color: #fff;
        }

        /* MAIN */
        .dashboard-main {
            display: flex;
            justify-content: center;
            padding: 40px 20px 80px;
        }

        /* CARD MERAH MUDA */
        .card {
            width: 100%;
            max-width: 950px;
            background: #fef2f2;
            padding: 50px;
            border-radius: 25px;
            border: 1px solid #fecaca;
            box-shadow: 0 15px 40px rgba(185, 28, 28, 0.15);
        }

        .card h2 {
            text-align: center;
            margin-bottom: 8px;
            font-weight: 600;
            color: #7a1f1f;
        }

        .card p {
            text-align: center;
            font-size: 14px;
            margin-bottom: 40px;
            color: #991b1b;
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

        .form-group.full {
            grid-column: span 2;
        }

        label {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: #7a1f1f;
        }

        input,
        select {
            width: 100%;
            padding: 13px 15px;
            background: #ffffff;
            border: 1px solid #fca5a5;
            border-radius: 12px;
            font-size: 14px;
            transition: 0.3s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #b91c1c;
            box-shadow: 0 0 8px rgba(185, 28, 28, 0.3);
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
            transition: 0.3s;
        }

        .btn-primary {
            border: none;
            background: linear-gradient(to right, #7a1f1f, #b91c1c);
            color: white;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(185, 28, 28, 0.4);
        }

        .btn-secondary {
            border: 1px solid #b91c1c;
            text-decoration: none;
            color: #7a1f1f;
        }

        .btn-secondary:hover {
            background: #b91c1c;
            color: white;
            transform: translateY(-3px);
        }

        /* SELECT2 */
        .select2-container--default .select2-selection--single {
            height: 45px;
            border-radius: 12px;
            border: 1px solid #fca5a5;
            padding: 8px;
        }

        @media(max-width:768px) {
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

<body>

    <header class="dashboard-header">
        <div class="logo">📕 PEMINJAMAN BUKU</div>
    </header>

    <main class="dashboard-main">
        <div class="card">
            <h2>Isi Data Peminjaman</h2>
            <p>Lengkapi informasi peminjaman buku</p>

            <form method="POST" action="dashboard.php?page=simpan_pinjam">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" value="<?= $user['username']; ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" value="<?= $user['email']; ?>" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label>No Telepon</label>
                    <input type="text" value="<?= $user['no_tlp']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Pilih Buku</label>
                    <select name="id_buku" class="select-search" required>
                        <option value="">Pilih Buku</option>
                        <?php
                        $queryBuku = "SELECT id_buku, judul_buku FROM tbl_buku ORDER BY judul_buku ASC";
                        $resultBuku = mysqli_query($koneksi, $queryBuku);
                        while ($rowBuku = mysqli_fetch_array($resultBuku)) {

                            $selected = ($rowBuku['id_buku'] == $id_buku_terpilih) ? "selected" : "";

                            echo "<option value='{$rowBuku['id_buku']}' $selected>
    {$rowBuku['id_buku']} - {$rowBuku['judul_buku']}
    </option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" onchange="hitungTanggalKembali()"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Durasi</label>
                        <select name="durasi_pinjam" id="durasi_pinjam" onchange="hitungTanggalKembali()" required>
                            <option value="">Pilih Durasi</option>
                            <option value="7">7 Hari</option>
                            <option value="14">14 Hari</option>
                            <option value="21">21 Hari</option>
                            <option value="30">30 Hari</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali" id="tanggal_kembali" readonly>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary">Ajukan Peminjaman</button>
                </div>

            </form>
        </div>
    </main>

    <script>
        $(document).ready(function () {
            $('.select-search').select2({
                placeholder: "Ketik judul buku...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

</body>

</html>