<?php
include "inc/koneksi.php";

$user = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username='$user'";
$hasil = mysqli_query($koneksi, $query);
$tampil = mysqli_fetch_array($hasil);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>

    <style>
        /* ===== BODY ===== */
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5e6d3, #fdf8f3);
        }

        /* ===== CONTAINER ===== */
        .profile-wrapper {
            max-width: 700px;
            margin: 100px auto 50px;
            padding: 20px;
        }

        /* ===== CARD PREMIUM ===== */
        .profile-card {
            background: linear-gradient(145deg, #fffdf9, #f8efe4);
            border-radius: 25px;
            padding: 40px;
            box-shadow:
                0 20px 45px rgba(92, 58, 33, 0.15),
                inset 0 0 0 1px rgba(255, 255, 255, 0.5);
            position: relative;
            overflow: hidden;
            animation: fadeIn .5s ease;
        }

        /* Top Line */
        .profile-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #5c3a21, #c58a5c, #5c3a21);
        }

        /* ===== FOTO PROFIL ===== */
        .profile-photo {
            text-align: center;
            margin-bottom: 25px;
        }

        .profile-photo img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid #e6b98a;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        /* ===== NAME ===== */
        .profile-name {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #4b2e2b;
            margin-bottom: 5px;
        }

        .profile-role {
            text-align: center;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #a47148;
            margin-bottom: 30px;
        }

        /* ===== INFO BOX ===== */
        .profile-info {
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 14px 18px;
            margin-bottom: 12px;
            background: linear-gradient(90deg,#c58a5c,#e6b98a);
            border-radius: 12px;
            font-size: 14px;
        }

        .info-item span {
            font-weight: 600;
            color: #5c3a21;
        }

        /* ===== BUTTON ===== */
        .profile-actions {
            text-align: center;
            margin-top: 35px;
        }

        .btn {
            display: inline-block;
            padding: 12px 28px;
            background: linear-gradient(135deg, #5c3a21, #8b5e3c);
            color: #fff8ec;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            letter-spacing: .5px;
            box-shadow: 0 8px 20px rgba(92, 58, 33, 0.3);
            transition: .3s;
        }

        .btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(92, 58, 33, 0.45);
        }

        /* ===== ANIMATION ===== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="profile-wrapper">
        <div class="profile-card">

            <!-- FOTO -->
            <div class="profile-photo">
                <?php if (!empty($tampil['foto'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($tampil['foto']); ?>" alt="Foto Profil">
                <?php else: ?>
                    <img src="assets/default-user.png" alt="Foto Default">
                <?php endif; ?>
            </div>

            <!-- NAMA -->
            <div class="profile-name">
                <?= htmlspecialchars($tampil['nama']); ?>
            </div>

            <div class="profile-role">
                <?= htmlspecialchars($tampil['akses']); ?>
            </div>

            <!-- INFO -->
            <div class="profile-info">

                <div class="info-item">
                    <span>Username</span>
                    <?= htmlspecialchars($tampil['username']); ?>
                </div>

                <div class="info-item">
                    <span>Email</span>
                    <?= htmlspecialchars($tampil['email']); ?>
                </div>

                <div class="info-item">
                    <span>No. Telp</span>
                    <?= htmlspecialchars($tampil['no_tlp']); ?>
                </div>

            </div>

            <!-- BUTTON -->
            <div class="profile-actions">
                <a href="dashboard.php" class="btn">Kembali ke Dashboard</a>
            </div>

        </div>
    </div>

</body>

</html>