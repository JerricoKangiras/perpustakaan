<?php
require_once __DIR__ . '/../inc/koneksi.php';

$id = $_POST['id_buku'];
$judul = $_POST['judul_buku'];
$author = $_POST['author_buku'];
$sinopsis = $_POST['sinopsis_buku'];
$jumlahhalaman = $_POST['jumlah_halaman'];
$jumlahbuku = $_POST['jumlah_buku'];
$idk = $_POST['id_kategori'];
$idp = $_POST['id_penerbit'];
$tt = $_POST['tahun_terbit'];

$sql = "INSERT INTO tbl_buku 
(id_buku, judul_buku, author_buku, sinopsis_buku, jumlah_halaman, jumlah_buku, id_kategori, id_penerbit, tahun_terbit) 
VALUES 
('$id', '$judul', '$author', '$sinopsis', '$jumlahhalaman', '$jumlahbuku', '$idk', '$idp', '$tt')";

$eksekusi = mysqli_query($koneksi, $sql);
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Penyimpanan Buku</title>
    <style>
      body {
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
}

/* CARD */
.result-box {
    background: #f5e6d3;
    padding: 45px 55px;
    border-radius: 25px;
    border: 1px solid #e0c3a3;
    box-shadow: 0 15px 40px rgba(139,94,60,0.15);
    text-align: center;
    width: 420px;
    animation: fadeIn 0.4s ease-in-out;
}

/* SUCCESS */
.success-title {
    color: #6b3e26;
    margin-bottom: 15px;
}

/* ERROR */
.error-title {
    color: #a94442;
    margin-bottom: 15px;
}

p {
    font-size: 15px;
    color: #5a3825;
    margin-bottom: 30px;
}

/* BUTTON */
.btn {
    display: inline-block;
    background: linear-gradient(to right, #6b3e26, #8b5e3c);
    color: #fff;
    padding: 12px 28px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(107,62,38,0.3);
}

/* ERROR BUTTON */
.btn-error {
    background: #a94442;
}

.btn-error:hover {
    box-shadow: 0 6px 15px rgba(169,68,66,0.3);
}

/* ANIMATION */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
    </style>
</head>

<body>
    <div class="result-box">
        <?php if ($eksekusi) { ?>
          <h2 class="success-title">📚 Data Berhasil Disimpan!</h2>
            <p>Buku <strong><?= htmlspecialchars($judul) ?></strong> berhasil ditambahkan.</p>
            <a href="dashboard.php?page=buku" class="btn">Lihat Data Buku</a>
        <?php } else { ?>
          <h2 class="error-title">⚠️ Terjadi Kesalahan!</h2>
            <p><?= htmlspecialchars(mysqli_error($koneksi)) ?></p>
           <a href="dashboard.php?page=tambah_buku" class="btn btn-error">Kembali</a>
        <?php } ?>
    </div>
</body>

</html>