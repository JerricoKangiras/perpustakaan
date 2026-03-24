<?php
if (!isset($_GET['id_buku']) || !is_numeric($_GET['id_buku'])) {
    die("ID buku tidak valid");
}

$id = (int) $_GET['id_buku'];

$stmt = mysqli_prepare($koneksi, "DELETE FROM tbl_buku WHERE id_buku = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
$result = mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Penghapusan Buku</title>
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

.emoji {
    font-size: 60px;
    margin-bottom: 10px;
    animation: bounce 0.6s ease;
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
        <?php if ($result) { ?>
            <div class="emoji">🗑️</div>
            <h2 class="success-title">Data Berhasil Dihapus!</h2>
            <p>Data buku dengan ID <strong><?= htmlspecialchars($id); ?></strong> telah dihapus.</p>
            <a href="dashboard.php?page=buku" class="btn">Kembali ke Data Buku</a>
        <?php } else { ?>
            <div class="emoji">⚠️</div>
            <h2 class="error-title">Gagal Menghapus Data!</h2>
            <p><?= htmlspecialchars(mysqli_error($koneksi)); ?></p>
            <a href="dashboard.php?page=buku" class="btn btn-error">Kembali</a>
        <?php } ?>
    </div>
</body>

</html>
