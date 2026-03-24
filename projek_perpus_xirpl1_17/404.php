<?php
include "inc/koneksi.php";

// cek login
if (!isset($_SESSION['username'])) {
    header("location:form_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 - Halaman Tidak Ditemukan</title>

<style>

/* ===== BODY ===== */
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* ===== CARD ===== */
.error-card{
    background:linear-gradient(145deg,#f8efe4,#e8d2b8);
    padding:60px 50px;
    border-radius:30px;
    text-align:center;
    box-shadow:
        0 30px 70px rgba(92,58,33,0.25),
        inset 0 0 0 1px rgba(255,255,255,0.4);
    position:relative;
    overflow:hidden;
    animation:fadeIn .6s ease;
}

/* top accent line */
.error-card::before{
    content:"";
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:8px;
    background:linear-gradient(90deg,#5c3a21,#c58a5c,#5c3a21);
}

/* 404 TEXT */
.error-code{
    font-size:120px;
    font-weight:800;
    margin:0;
    background:linear-gradient(90deg,#5c3a21,#a47148);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* MESSAGE */
.error-message{
    font-size:22px;
    font-weight:600;
    color:#4b2e2b;
    margin-top:-10px;
}

.error-desc{
    margin-top:15px;
    font-size:14px;
    color:#6f4e37;
}

/* BUTTON */
.btn{
    display:inline-block;
    margin-top:30px;
    padding:14px 32px;
    background:linear-gradient(135deg,#5c3a21,#8b5e3c);
    color:#fff8ec;
    border-radius:40px;
    text-decoration:none;
    font-weight:600;
    font-size:14px;
    letter-spacing:.5px;
    box-shadow:0 10px 25px rgba(92,58,33,0.35);
    transition:.3s ease;
}

.btn:hover{
    transform:translateY(-5px);
    box-shadow:0 20px 40px rgba(92,58,33,0.45);
}

/* animation */
@keyframes fadeIn{
    from{opacity:0; transform:translateY(30px);}
    to{opacity:1; transform:translateY(0);}
}

/* responsive */
@media(max-width:600px){
    .error-code{
        font-size:80px;
    }
    .error-card{
        padding:40px 25px;
    }
}

</style>
</head>

<body>

<div class="error-card">
    <h1 class="error-code">404</h1>
    <div class="error-message">Halaman Tidak Ditemukan</div>
    <div class="error-desc">
        Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.
    </div>

    <a href="dashboard.php" class="btn">Kembali ke Dashboard</a>
</div>

</body>
</html>