<?php
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// ======================
// QUERY DINAMIS
// ======================
$where = "WHERE LOWER(s.judul_buku) LIKE '%invincible%'";

if ($search !== '') {
    $search_safe = mysqli_real_escape_string($koneksi, strtolower($search));
    $where .= " AND (
        LOWER(s.judul_buku) LIKE '%$search_safe%' 
        OR LOWER(s.author_buku) LIKE '%$search_safe%'
    )";
}

$data = mysqli_query($koneksi, "
    SELECT s.*, k.nama_kategori, p.nama_penerbit 
    FROM tbl_buku s 
    LEFT JOIN tbl_kategori k ON s.id_kategori = k.id_kategori 
    LEFT JOIN tbl_penerbit p ON s.id_penerbit = p.id_penerbit
    $where
");

$invincible_books = [];
while ($row = mysqli_fetch_assoc($data)) {
    $invincible_books[] = $row;
}

/* ======================
   PAGINATION
====================== */

$limit = 6; // jumlah buku per halaman
$totalData = count($invincible_books);
$totalPage = ceil($totalData / $limit);

$page = isset($_GET['hal']) ? (int) $_GET['hal'] : 1;

if ($page < 1 || ($page > $totalPage && $totalPage > 0)) {
    header("Location: 404.php");
    exit;
}

$start = ($page - 1) * $limit;
$invincible_books = array_slice($invincible_books, $start, $limit);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku Invincible</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4efe6;
            margin: 0;
            padding: 30px;
            color: #5c4033;
        }

        html {
            scroll-behavior: smooth;
        }

        /* ================= HEADER ================= */

        .judul-halaman {
            color: #5c4033;
            margin-bottom: 20px;
        }

        /* ================= BACK BUTTON ================= */

        .back-btn {
            display: inline-block;
            padding: 10px 18px;
            background: #8b6f47;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 500;
            transition: 0.3s ease;
        }

        .back-btn:hover {
            background: #6f5437;
        }

        /* ================= SEARCH BAR ================= */
        .search-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: none;
            background: #8b6f47;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s ease;
        }

        .search-btn img {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        .search-btn:hover {
            background: #6f5437;
            transform: translateY(-2px);
        }

        /* ================= DETAIL ================= */

        .main-detail {
            margin-top: 40px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 15px;
            background: white;
        }

        .detail-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .cover-box img {
            width: 180px;
            border-radius: 10px;
        }

        .btn-pinjam {
            display: block;
            margin-top: 12px;
            padding: 10px;
            text-align: center;
            background-color: #8b6f47;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-pinjam:hover {
            background-color: #6f5437;
        }

        .btn-pinjam.disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }
/* ================= SEARCH BAR ================= */

.search-wrapper {
    display: flex;
}

.input-box {
    position: relative;
    width: 3200px;
}

.input-box input {
    width: 100%;
    height: 40px;              /* 🔥 tambahkan */
    padding: 0 45px 0 15px;    /* rapikan padding */
    border-radius: 30px;
    border: 1px solid #ccc;
    box-sizing: border-box;    /* 🔥 penting */
}

.input-box input:focus {
    border-color: #8b6f47;
    box-shadow: 0 0 6px rgba(139,111,71,0.3);
}

.input-box button {
    position: absolute;
    right: 6px;
    top: 10px;          /* 🔥 ganti dari 50% */
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: none;
    background: #8b6f47;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.input-box button img {
    width: 13px;
    height: 13px;
}

.input-box button:hover {
    background: #6f5437;
}
        .sinopsis {
            margin-top: 10px;
        }

        /* ================= PAGINATION ================= */

        .pagination {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination a,
        .pagination a:visited,
        .pagination a:hover,
        .pagination a:active {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #8b6f47;
            border-radius: 50%;
            text-decoration: none;
            transition: 0.3s;
            color: white;
            /* tulisan putih */
        }

        .pagination a img {
            width: 18px;
            height: 18px;
        }

        .pagination a:hover {
            background: #6f5437;
        }

        .pagination a.active {
            background: #5c4033;
        }
    </style>
    <div class="container">
        <a href="../projek_perpus_xirpl1_17/dashboard.php?page=perpustakaan" class="back-btn">
            ← Kembali ke Perpustakaan
        </a>

        <h1 class="judul-halaman">
            Edisi Buku Invincible
        </h1>

        <!-- SEARCH BAR -->
        <div class="perpus-search-bar">
            <form method="GET" action="" class="search-wrapper">

                <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? 'perpustakaan'); ?>">

                <div class="input-box">
                    <input type="text" name="search" placeholder="Cari buku..."
                        value="<?= htmlspecialchars($search) ?>">

                    <button type="submit">
                        <img src="cover/search.png" alt="Search">
                    </button>
                </div>

            </form>
        </div>

        <?php if (count($invincible_books) === 0): ?>
            <p>Tidak ada buku Invincible ditemukan.</p>
        <?php else: ?>

            <?php foreach ($invincible_books as $buku): ?>

                <div class="main-detail" id="buku<?= $buku['id_buku']; ?>">

                    <div class="detail-container">

                        <!-- COVER -->
                        <div class="cover-box">
                            <?php
                            $cover = $buku['cover_buku'];

                            if (!empty($cover)) {

                                if (is_string($cover) && strpos($cover, 'cover/') === 0) {
                                    echo '<img src="' . htmlspecialchars($cover) . '">';
                                } else {
                                    $mime = 'image/jpeg';
                                    if (function_exists('finfo_buffer')) {
                                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                        $mime = finfo_buffer($finfo, $cover);
                                        finfo_close($finfo);
                                    }
                                    echo '<img src="data:' . $mime . ';base64,' . base64_encode($cover) . '">';
                                }

                            } else {
                                echo '<img src="../../cover/invincible_cover.jpg">';
                            }
                            ?>

                            <!-- BUTTON PINJAM -->
                            <?php if ($buku['jumlah_buku'] > 0): ?>
                                <a href="?page=pinjam&id_buku=<?= $buku['id_buku']; ?>" class="btn-pinjam">
    Pinjam Buku
</a>
                            <?php else: ?>
                                <button class="btn-pinjam disabled" disabled>
                                    Stok Habis
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- INFO -->
                        <div class="info-box">
                            <h2><?= htmlspecialchars($buku['judul_buku']); ?></h2>
                            <p><strong>Author:</strong> <?= htmlspecialchars($buku['author_buku']); ?></p>
                            <p><strong>Kategori:</strong> <?= htmlspecialchars($buku['nama_kategori']); ?></p>
                            <p><strong>Penerbit:</strong> <?= htmlspecialchars($buku['nama_penerbit']); ?></p>
                            <p><strong>Tahun:</strong> <?= (int) $buku['tahun_terbit']; ?></p>
                            <p><strong>Jumlah Halaman:</strong> <?= (int) $buku['jumlah_halaman']; ?> halaman</p>
                            <p><strong>Stok Tersedia:</strong> <?= (int) $buku['jumlah_buku']; ?> buku</p>

                            <div class="sinopsis">
                                <strong>Sinopsis:</strong><br>
                                <?= nl2br(htmlspecialchars($buku['sinopsis_buku'])); ?>
                            </div>
                        </div>

                    </div>

                </div>

            <?php endforeach; ?>
            <!-- PAGINATION -->
            <?php if ($totalPage > 1): ?>
                <div class="pagination">

                    <!-- PREV -->
                    <?php if ($page > 1): ?>
                        <a href="dashboard.php?page=detail_buku_invincible<?= $page - 1 == 1 ? '' : '&hal=' . ($page - 1) ?>">
                            <img src="cover/previous.png" alt="Previous">
                        </a>
                    <?php endif; ?>

                    <!-- NUMBER -->
                    <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                        <a href="dashboard.php?page=detail_buku_invincible<?= $i == 1 ? '' : '&hal=' . $i ?>"
                            class="<?= ($i == $page) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <!-- NEXT -->
                    <?php if ($page < $totalPage): ?>
                        <a href="dashboard.php?page=detail_buku_invincible&hal=<?= $page + 1 ?>">
                            <img src="cover/next.png" alt="Next">
                        </a>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div>
    <?php endif; ?>

    </div>
    </body>

</html>