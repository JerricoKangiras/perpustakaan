<?php
$idp = $_POST['id_penerbit'];
$np = $_POST['nama_penerbit'];
$notlpp = $_POST['notlp_penerbit'];
$ns = $_POST['nama_sales'];
$notlps = $_POST['notlp_sales'];

$sql = "INSERT INTO tbl_penerbit (id_penerbit, nama_penerbit, notlp_penerbit, nama_sales, notlp_sales) 
        VALUES ('$idp', '$np', '$notlpp', '$ns', '$notlps')";
$eksekusi = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Penyimpanan Penerbit</title>

    <!-- Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        /* CARD HIJAU */
        .result-box {
            background: #f0fdf4;
            padding: 45px 55px;
            border-radius: 25px;
            border: 1px solid #bbf7d0;
            box-shadow: 0 15px 40px rgba(22, 163, 74, 0.15);
            text-align: center;
            width: 420px;
            animation: fadeIn 0.4s ease-in-out;
        }

        /* SUCCESS */
        .success-title {
            color: #166534;
            margin-bottom: 15px;
        }

        /* ERROR */
        .error-title {
            color: #dc2626;
            margin-bottom: 15px;
        }

        p {
            font-size: 15px;
            color: #15803d;
            margin-bottom: 30px;
        }

        /* BUTTON SUCCESS HIJAU */
        .btn {
            display: inline-block;
            background: linear-gradient(to right, #166534, #16a34a);
            color: #fff;
            padding: 12px 28px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(22, 163, 74, 0.35);
        }

        /* BUTTON ERROR */
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
        <?php if ($eksekusi) { ?>
            <h2 class="success-title">✅ Data Berhasil Disimpan!</h2>
            <p>Penerbit <strong><?= htmlspecialchars($np) ?></strong> berhasil ditambahkan.</p>
            <a href="dashboard.php?page=penerbit" class="btn">Lihat Data Penerbit</a>
        <?php } else { ?>
            <h2 class="error-title">❌ Terjadi Kesalahan!</h2>
            <p><?= htmlspecialchars(mysqli_error($koneksi)) ?></p>
            <a href="dashboard.php?page=tambah_penerbit" class="btn btn-error">Kembali</a>
        <?php } ?>
    </div>
</body>

</html>