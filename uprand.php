<?php
// Include the database connection
include('sql.php');

// Menghasilkan ID acak antara 17 dan 20
$id_random = rand(55, 70);

// Menghasilkan angka acak antara 1 dan 5 untuk views
$views_random = rand(1, 1);

// Query update untuk menambah views pada artikel yang dipilih secara acak
$sql = "UPDATE articles SET views = views + ? WHERE id = ?";

// Persiapkan dan bind parameter untuk db_A
$stmt_A = mysqli_prepare($con_A, $sql);
mysqli_stmt_bind_param($stmt_A, "ii", $views_random, $id_random);

// Persiapkan dan bind parameter untuk db_B
$stmt_B = mysqli_prepare($con_B, $sql);
mysqli_stmt_bind_param($stmt_B, "ii", $views_random, $id_random);

// Eksekusi pernyataan untuk db_A
mysqli_stmt_execute($stmt_A);

// Eksekusi pernyataan untuk db_B
mysqli_stmt_execute($stmt_B);

// Variabel untuk menampung hasil notifikasi
$notification = "Artikel dengan ID: " . $id_random . " berhasil diperbarui. Views ditambahkan sebesar: " . $views_random;

// Tampilkan notifikasi
echo $notification;

// Menutup koneksi
mysqli_stmt_close($stmt_A);
mysqli_stmt_close($stmt_B);
mysqli_close($con_A);
mysqli_close($con_B);
?>

<!-- Auto Refresh Page every 3 seconds -->
<meta http-equiv="refresh" content="3">
