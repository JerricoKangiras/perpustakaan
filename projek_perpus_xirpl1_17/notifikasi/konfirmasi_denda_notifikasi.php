<?php
session_start();
include "inc/koneksi.php";

$id = $_GET['id'] ?? 0;

// ambil data peminjaman
$stmt = mysqli_prepare($koneksi, "
    SELECT id_user FROM tbl_peminjaman WHERE id_peminjam = ?
");
mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

$user_id = $data['id_user'] ?? 0;

if($user_id){

    // ambil saldo user
    $stmtSaldo = mysqli_prepare($koneksi,
        "SELECT saldo FROM users WHERE id=?");
    mysqli_stmt_bind_param($stmtSaldo,"i",$user_id);
    mysqli_stmt_execute($stmtSaldo);
    $resultSaldo = mysqli_stmt_get_result($stmtSaldo);
    $dataSaldo = mysqli_fetch_assoc($resultSaldo);

    $saldo = $dataSaldo['saldo'];

    // hitung denda manual (atau simpan di kolom denda kalau ada)
    $denda = 10000; // contoh

    if($saldo >= $denda){

        $saldoBaru = $saldo - $denda;

        // potong saldo
        $updateSaldo = mysqli_prepare($koneksi,
            "UPDATE users SET saldo=? WHERE id=?");
        mysqli_stmt_bind_param($updateSaldo,"ii",$saldoBaru,$user_id);
        mysqli_stmt_execute($updateSaldo);

        // ubah status jadi lunas
        $updateStatus = mysqli_prepare($koneksi,
            "UPDATE tbl_peminjaman 
             SET status='Denda_Lunas' 
             WHERE id_peminjam=?");
        mysqli_stmt_bind_param($updateStatus,"i",$id);
        mysqli_stmt_execute($updateStatus);
    }
}

header("Location: dashboard.php?page=notifikasi");
exit;