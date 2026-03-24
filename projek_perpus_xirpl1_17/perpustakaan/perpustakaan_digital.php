<?php
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) && in_array($_GET['sort'], ['az', 'za'])
    ? $_GET['sort']
    : 'az';

/* =========================
   1. DATA MANUAL
========================= */
$bukuManual = [
    ["judul" => "Invincible", "author" => "Robert Kirkman", "stok" => 0, "img" => "cover/invincible_cover.jpg", "is_blob" => false, "link" => "detail_buku_invincible"],
    ["judul" => "Jujutsu Kaisen", "author" => "Gege Akutami", "stok" => 0, "img" => "cover/jujutsu_kaisen_cover.jpg", "is_blob" => false, "link" => "detail_buku_jjk"],
    ["judul" => "Chainsaw Man", "author" => "Tatsuki Fujimoto", "stok" => 0, "img" => "cover/chainsawman_cover.webp", "is_blob" => false, "link" => "detail_buku_csm"],
    ["judul" => "Detective Conan", "author" => "Gosho Aoyama", "stok" => 0, "img" => "cover/detective_conan_cover.jpg", "is_blob" => false, "link" => "detail_buku_detective_conan"],
    ["judul" => "The Amazing World of Gumball", "author" => "Christian Blauvelt", "stok" => 0, "img" => "cover/tawog_cover.webp", "is_blob" => false, "link" => "detail_buku_tawog"]
];
/* =========================
   2. DATA DARI DATABASE
========================= */
$bukuDB = [];

$query = mysqli_query($koneksi, "SELECT * FROM tbl_buku");

while ($row = mysqli_fetch_assoc($query)) {
    $bukuDB[] = [
        "judul" => $row['judul_buku'],
        "author" => ($row['author_buku'] ?? 'Anonymous'),
        "stok" => $row['jumlah_buku'],
        "img" => $row['cover_buku'],
        "is_blob" => true,
        "link" => "detail_buku&id=" . $row['id_buku']
    ];
}

/* =========================
   3. GABUNGKAN MANUAL + DB
========================= */
$buku = array_merge($bukuManual, $bukuDB);

/* =========================
   4. GROUPING DIGABUNG JADI 1 COVER (FIX FINAL)
========================= */

$bukuGrouped = [];

/* Loop setiap buku */
foreach ($buku as $item) {

    $judulLower = strtolower($item['judul']);
    $matched = false;

    /* Cek apakah judul mengandung salah satu judul manual */
    foreach ($bukuManual as $manual) {

        $manualLower = strtolower($manual['judul']);

        if (strpos($judulLower, $manualLower) !== false) {

            $key = $manualLower;

            if (!isset($bukuGrouped[$key])) {

                // Pakai cover manual sebagai utama
                $bukuGrouped[$key] = $manual;
                $bukuGrouped[$key]['stok'] = 0; // reset dulu

            }

            $bukuGrouped[$key]['stok'] += $item['stok'];
            $matched = true;
            break;
        }
    }

    /* Jika tidak cocok dengan manual, tampil normal */
    if (!$matched) {

        $key = strtolower(trim($item['judul']));

        if (!isset($bukuGrouped[$key])) {
            $bukuGrouped[$key] = $item;
        } else {
            $bukuGrouped[$key]['stok'] += $item['stok'];
        }
    }
}

$buku = array_values($bukuGrouped);

/* =========================
   5. SEARCH
========================= */
if ($search !== '') {
    $buku = array_filter($buku, function ($item) use ($search) {
        return stripos($item['judul'], $search) !== false;
    });
}

/* =========================
   6. SORTING
========================= */
usort($buku, function ($a, $b) use ($sort) {
    if ($sort == 'za') {
        return strcmp($b['judul'], $a['judul']);
    }
    return strcmp($a['judul'], $b['judul']);
});

/* =========================
   7. PAGINATION
========================= */

$limit = 15; // jumlah buku per halaman
$totalData = count($buku);
$totalPage = ceil($totalData / $limit);

$page = isset($_GET['hal']) ? (int) $_GET['hal'] : 1;

if ($page < 1 || $page > $totalPage && $totalPage > 0) {
    header("Location: 404.php");
    exit;
}

$start = ($page - 1) * $limit;
$buku = array_slice($buku, $start, $limit);
?>

<!DOCTYPE html>
<html lang="id">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<head>
    <meta charset="UTF-8">
    <title>Perpustakaan Digital</title>
    <style>
        /* ===============================
   GLOBAL STYLE
================================= */

        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4efe6;
            margin: 0;
            color: #5c4033;
        }

        /* ===============================
   HEADER
================================= */

        .perpus-header {
            background: linear-gradient(135deg, #8b6f47, #a67c52);
            padding: 30px;
            border-radius: 22px;
            margin-bottom: 35px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(92, 64, 51, 0.25);
        }

        .perpus-header h1 {
            margin: 0;
            color: #fffaf3;
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* ===============================
   SEARCH BAR - BUTTON DI UJUNG
================================= */

        .perpus-search-bar {
            margin-bottom: 30px;
        }

        .perpus-search-bar form {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .perpus-search-bar input {
            flex: 1;
            /* biar panjang */
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid #d6c5b4;
            background-color: #fffaf3;
            color: #5c4033;
            font-size: 14px;
            outline: none;
        }


        .search-btn {
            margin-left: auto;
            width: 48px;
            height: 48px;
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

        /* ===============================
   GRID
================================= */

        .perpus-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 25px;
        }

        /* ===============================
   CARD
================================= */

        .perpus-card {
            background: #fffaf3;
            border-radius: 18px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #e4d6c3;
            box-shadow: 0 6px 18px rgba(92, 64, 51, 0.08);
            transition: all 0.3s ease;
        }

        .perpus-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(92, 64, 51, 0.18);
        }

        .perpus-card img {
            width: 100%;
            max-width: 140px;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 14px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        /* ===============================
   TEXT
================================= */

        .perpus-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 6px;
            text-align: center;
        }

        .perpus-title a {
            text-decoration: none;
            color: #5c4033;
            transition: 0.2s ease;
        }

        .perpus-title a:hover {
            color: #8b6f47;
        }

        .perpus-author {
            font-size: 13px;
            color: #7a5c45;
            margin-bottom: 8px;
            text-align: center;
            font-weight: 400;
        }

        .perpus-stock {
            font-size: 12px;
            background-color: #e8dccb;
            color: #5c4033;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* ===============================
   EMPTY STATE
================================= */

        .perpus-grid p {
            grid-column: 1 / -1;
            text-align: center;
            font-style: italic;
            color: #7a5c45;
            margin-top: 30px;
        }

        /* ===============================
   RESPONSIVE
================================= */

        @media (max-width: 600px) {
            .perpus-header h1 {
                font-size: 22px;
            }

            .perpus-search-bar form {
                flex-direction: column;
            }

            .perpus-search-bar input,
            .perpus-search-bar select,
            .perpus-search-bar button {
                width: 100%;
            }
        }

        /* ===============================
   PAGINATION
================================= */

        .perpus-pagination {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .perpus-pagination a {
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            background: #e8dccb;
            color: #5c4033;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .perpus-pagination a:hover {
            background: #8b6f47;
            color: #fff;
        }

        .perpus-pagination a.active {
            background: #8b6f47;
            color: #fff;
        }
    </style>

    <div class="perpus-header">
        <h1>📚 Perpustakaan Digital</h1>
    </div>

    <div class="perpus-search-bar">
        <form method="GET" action="dashboard.php">
            <input type="hidden" name="page" value="perpustakaan">

            <input type="text" name="search" placeholder="Cari buku..." value="<?= htmlspecialchars($search) ?>">

            <button type="submit" class="search-btn">
                <img src="cover/search.png" alt="Search">
            </button>
        </form>
    </div>

    <!-- GRID -->
    <div class="perpus-grid">
        <?php if (!empty($buku)): ?>
            <?php foreach ($buku as $item): ?>

                <?php
                // Tentukan source gambar
                if ($item['is_blob'] === true && !empty($item['img'])) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_buffer($finfo, $item['img']);
                    finfo_close($finfo);

                    $src = "data:$mime;base64," . base64_encode($item['img']);
                } else {
                    $src = $item['img'];
                }
                ?>

                <div class="perpus-card">
                    <img src="<?= $src ?>" alt="<?= htmlspecialchars($item['judul']) ?>" style="height:220px;object-fit:cover;">

                    <div class="perpus-title">
                        <a href="dashboard.php?page=<?= $item['link'] ?>">
                            <?= htmlspecialchars($item['judul']) ?>
                        </a>
                    </div>

                    <div class="perpus-author">
                        By <?= htmlspecialchars($item['author']) ?>
                    </div>

                    <div class="perpus-stock">
                        Stok: <?= htmlspecialchars($item['stok']) ?>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p>Tidak ada buku ditemukan.</p>
        <?php endif; ?>
    </div>
    <!-- PAGINATION -->
    <?php if ($totalPage > 1): ?>
        <div class="perpus-pagination">
            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                <a href="dashboard.php?page=perpustakaan&hal=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                    class="<?= ($i == $page) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    </body>

</html>