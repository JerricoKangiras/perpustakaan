<?php 
$idk = $_POST['id_kategori'];
$nama_kategori = $_POST['nama_kategori'];
$query="update tbl_kategori set nama_kategori='$nama_kategori' where id_kategori='$idk'";
$result=mysqli_query($koneksi,$query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Update Kategori</title>
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
            background: #eff6ff;
            padding: 45px 55px;
            border-radius: 25px;
            border: 1px solid #bfdbfe;
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.15);
            text-align: center;
            width: 420px;
            animation: fadeIn 0.4s ease-in-out;
        }

        /* SUCCESS */
        .success-title {
            color: #1e3a8a;
            margin-bottom: 15px;
        }

        /* ERROR */
        .error-title {
            color: #dc2626;
            margin-bottom: 15px;
        }

        p {
            font-size: 15px;
            color: #1d4ed8;
            margin-bottom: 30px;
        }

        /* BUTTON SUCCESS */
        .btn {
            display: inline-block;
            background: linear-gradient(to right, #1e3a8a, #2563eb);
            color: #fff;
            padding: 12px 28px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.35);
        }

        /* ERROR BUTTON */
        .btn-error {
            background: #dc2626;
        }

        .btn-error:hover {
            box-shadow: 0 6px 15px rgba(220, 38, 38, 0.35);
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
            <h2 class="success-title">✅ Data Berhasil Diubah!</h2>
            <p>Kategori <strong><?= htmlspecialchars($nama_kategori) ?></strong> berhasil diperbaruhi.</p>
            <a href="dashboard.php?page=kategori" class="btn">Lihat Data Kategori</a>
        <?php } else { ?>
            <h2 class="error-title">❌ Gagal Mengubah Data!</h2>
            <p><?= htmlspecialchars(mysqli_error($koneksi)) ?></p>
            <a href="dashboard.php?page=edit_kategori" class="btn btn-error">Kembali</a>
        <?php } ?>
    </div>
</body>
</html>