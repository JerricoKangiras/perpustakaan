<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
    min-height: 100vh;
}

/* HEADER BIRU */
.dashboard-header {
    max-width: 950px;
    margin: 30px auto 0;
    padding: 18px 40px;

    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    border-radius: 50px;
    box-shadow: 0 8px 25px rgba(37,99,235,0.3);

    display: flex;
    justify-content: center;
    align-items: center;
}

.logo {
    font-size: 22px;
    font-weight: 600;
    color: #ffffff;
    letter-spacing: 1px;
}

/* MAIN */
.dashboard-main {
    display: flex;
    justify-content: center;
    padding: 40px 20px 80px;
}

/* CARD BIRU MUDA */
.card {
    width: 100%;
    max-width: 600px;
    background: #eff6ff;
    padding: 45px;
    border-radius: 25px;
    border: 1px solid #bfdbfe;
    box-shadow: 0 15px 40px rgba(37,99,235,0.15);
}

.card h2 {
    text-align: center;
    margin-bottom: 10px;
    font-weight: 600;
    color: #1e3a8a;
}

.card p {
    text-align: center;
    font-size: 14px;
    margin-bottom: 35px;
    color: #1d4ed8;
}

/* FORM */
form {
    width: 100%;
}

.form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

label {
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 6px;
    color: #1e3a8a;
}

/* INPUT */
input {
    width: 100%;
    padding: 13px 15px;
    background: #ffffff;
    border: 1px solid #93c5fd;
    border-radius: 12px;
    font-size: 14px;
    transition: 0.3s ease;
}

input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 8px rgba(37,99,235,0.3);
}

/* BUTTON GROUP */
.button-group {
    display: flex;
    gap: 20px;
    margin-top: 35px;
}

/* PRIMARY */
.btn-primary {
    flex: 1;
    padding: 14px;
    border-radius: 50px;
    font-weight: 600;
    border: none;
    background: linear-gradient(to right, #1e3a8a, #2563eb);
    color: white;
    cursor: pointer;
    transition: 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(37,99,235,0.4);
}

/* SECONDARY */
.btn-secondary {
    flex: 1;
    padding: 14px;
    border-radius: 50px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    border: 1px solid #2563eb;
    color: #1e3a8a;
    transition: 0.3s ease;
}

.btn-secondary:hover {
    background: #2563eb;
    color: white;
    transform: translateY(-3px);
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .card {
        padding: 30px 20px;
    }

    .button-group {
        flex-direction: column;
    }
}

</style>
</head>

<body>

<header class="dashboard-header">
    <div class="logo">📚 PERPUSTAKAAN DIGITAL</div>
</header>

<main class="dashboard-main">
    <div class="card">
        <h2>Tambah Kategori</h2>
        <p>Masukkan data kategori baru</p>

        <form method="POST" action="dashboard.php?page=simpan_kategori">

            <div class="form-group">
                <label>ID Kategori</label>
                <input type="text" name="id_kategori" required>
            </div>

            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" required>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="dashboard.php?page=kategori" class="btn-secondary">Kembali</a>
            </div>

        </form>
    </div>
</main>

</body>
</html>