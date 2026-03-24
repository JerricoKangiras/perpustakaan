<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Tambah Data Peminjam</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script>         function hitungTanggalKembali() { let tanggalPinjam = document.getElementById("tanggal_pinjam").value; let durasi = document.getElementById("durasi_pinjam").value; if (tanggalPinjam !== "" && durasi !== "") { let tgl = new Date(tanggalPinjam); tgl.setDate(tgl.getDate() + parseInt(durasi)); let tahun = tgl.getFullYear(); let bulan = String(tgl.getMonth() + 1).padStart(2, '0'); let hari = String(tgl.getDate()).padStart(2, '0'); document.getElementById("tanggal_kembali").value = tahun + "-" + bulan + "-" + hari; } }     </script>
</head>

<body>
    <h2>Form Pengajuan Peminjaman Buku</h2>
    <form method="POST" action="simpan_pinjam.php"> <label>ID Buku:</label><br> <select name="id_buku" class="select2"
            required>
            <option value="">Pilih Buku</option>
            <?php include '../inc/koneksi.php';
            $queryBuku = "SELECT id_buku, judul_buku FROM tbl_buku ORDER BY judul_buku ASC";
            $resultBuku = mysqli_query($koneksi, $queryBuku);
            while ($rowBuku = mysqli_fetch_array($resultBuku)) {
                echo "<option value='" . $rowBuku['id_buku'] . "'>                     " . $rowBuku['id_buku'] . " - " . $rowBuku['judul_buku'] . "                   </option>";
            } ?>
        </select> <br><br> <label>Nama Peminjam:</label><br> <input type="text" name="nama_peminjam" required><br><br>
        <label>Email:</label><br> <input type="email" name="email_peminjam" required><br><br> <label>No
            Telepon:</label><br> <input type="text" name="notlp_peminjam" required><br><br> <label>Alamat:</label><br>
        <textarea name="alamat_peminjam" required></textarea><br><br> <label>Tanggal Pinjam:</label><br> <input
            type="date" name="tanggal_pinjam" id="tanggal_pinjam" onchange="hitungTanggalKembali()" required> <br><br>
        <label>Durasi Pinjam:</label><br> <select name="durasi_pinjam" id="durasi_pinjam"
            onchange="hitungTanggalKembali()" required>
            <option value="">-- Pilih Durasi --</option>
            <option value="7">7 Hari</option>
            <option value="14">14 Hari</option>
            <option value="21">21 Hari</option>
            <option value="30">30 Hari</option>
        </select> <br><br> <label>Tanggal Kembali:</label><br> <input type="date" name="tanggal_kembali"
            id="tanggal_kembali" readonly> <br><br> <button type="submit">Ajukan Peminjaman</button> </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script> $(document).ready(function () { $('.select2').select2({ placeholder: "Cari judul buku...", allowClear: true, width: '300px' }); }); </script>
</body>

</html>