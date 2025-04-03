<?php
// Mulai session untuk menghitung berapa kali halaman di-refresh
session_start();

// Jika session 'refresh_count' belum ada, setel ke 1
if (!isset($_SESSION['refresh_count'])) {
    $_SESSION['refresh_count'] = 1;
}

// Jika session 'article_id' belum ada, pilih ID acak sekali
if (!isset($_SESSION['article_id'])) {
    $_SESSION['article_id'] = rand(55, 70);  // Pilih ID acak antara 25 dan 34
}

// Jika sudah mencapai 15 refresh, reset dan pilih ID baru
if ($_SESSION['refresh_count'] > 15) {
    $_SESSION['refresh_count'] = 1;  // Reset refresh count
    $_SESSION['article_id'] = rand(55, 70);  // Pilih ID acak baru
}

// Jika masih kurang dari 15 refresh, lakukan pembaruan
if ($_SESSION['refresh_count'] <= 15) {
    // Include the database connection
    include('sql.php');

    // ID artikel yang dipilih
    $id_random = $_SESSION['article_id'];

    // Menghasilkan angka acak antara 1 dan 5 untuk views
    $views_random = rand(1, 2);

    // Query update untuk menambah views pada artikel yang dipilih secara acak
    $sql = "UPDATE articles SET views = views + ? WHERE id = ?";

    // Persiapkan dan bind parameter untuk db_A
    $stmt_A = mysqli_prepare($con_A, $sql);
    mysqli_stmt_bind_param($stmt_A, "ii", $views_random, $id_random);

    // Persiapkan dan bind parameter untuk db_B
    $stmt_B = mysqli_prepare($con_B, $sql);
    mysqli_stmt_bind_param($stmt_B, "ii", $views_random, $id_random);

    // Eksekusi pernyataan untuk db_A dan db_B
    mysqli_stmt_execute($stmt_A);
    mysqli_stmt_execute($stmt_B);

    // Menutup koneksi
    mysqli_stmt_close($stmt_A);
    mysqli_stmt_close($stmt_B);
    mysqli_close($con_A);
    mysqli_close($con_B);

    // Menampilkan notifikasi pembaruan
    $notification = "Artikel dengan ID: " . $id_random . " berhasil diperbarui. Views ditambahkan sebesar: " . $views_random;
    echo "<p>{$notification}</p>";

    // Increment refresh count
    $_SESSION['refresh_count']++;
} else {
    // Jika sudah mencapai 15 refresh, tampilkan pesan selesai
    echo "<p>Artikel dengan ID: " . $_SESSION['article_id'] . " telah diperbarui sebanyak 15 kali.</p>";
}

// Menutup session untuk memastikan penghitung hanya terjadi sekali
session_write_close();

// Auto refresh page every 15 seconds (akan berhenti setelah 15 kali)
echo '<meta http-equiv="refresh" content="3">';
?>
