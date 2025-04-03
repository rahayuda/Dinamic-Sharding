<?php
// Memasukkan koneksi SQL
include('sql.php');

// Mengambil artikel dari DB A
$sql_A = "SELECT id, title, views, modified, TIMESTAMPDIFF(second, modified, NOW()) AS since_modified FROM articles";
$result_A = mysqli_query($con_A, $sql_A);

// Mengambil artikel dari DB B
$sql_B = "SELECT id, title, views, modified, TIMESTAMPDIFF(second, modified, NOW()) AS since_modified FROM articles";
$result_B = mysqli_query($con_B, $sql_B);

// Mengambil Q-Table dari DB A
$sql_q_table = "SELECT state, action0, action1, action2, (action0 + action1 + action2) AS action 
FROM q_table 
WHERE action0 != 0 OR action1 != 0 OR action2 != 0 
ORDER BY action DESC 
LIMIT 15";
$result_q_table = mysqli_query($con_A, $sql_q_table);


// Query untuk menghitung total views
$sql_T = "SELECT SUM(views) AS total_views FROM articles";
$result_T = mysqli_query($con_A, $sql_T);

// Cek apakah query berhasil
if ($result_T) {
    $row = mysqli_fetch_assoc($result_T);
    $total_views = "<progress value='" . $row['total_views'] . "' max='10'></progress>";  // Ambil nilai total_views dari hasil query
}



// Initialize variable to hold HTML table rows
$articles_A = "";
if (mysqli_num_rows($result_A) > 0) {
    while($row = mysqli_fetch_assoc($result_A)) {
        $title_short = strlen($row['title']) > 40 ? substr($row['title'], 0, 40) . "..." : $row['title'];
        $articles_A .= "<tr class='row'>
        <td><b>{$row['id']}</b></td>
        <td>{$title_short}</td>
        <td class='views'><progress value='" . $row['views'] . "' max='10'></progress></td>
        <td class='modified'><progress value='" . $row['since_modified'] . "' max='4000'></progress></td>
        </tr>";
    }
} else {
    $articles_A .= "<tr class='row'>
    <td>...</td>
    <td>...</td>
    <td class='views'>...</td>
    <td class='modified'>...</td>
    </tr>";
}

$articles_B = "";
if (mysqli_num_rows($result_B) > 0) {
    while($row = mysqli_fetch_assoc($result_B)) {
        $title_short = strlen($row['title']) > 40 ? substr($row['title'], 0, 40) . "..." : $row['title'];
        $articles_B .= "<tr class='row'>
        <td><b>{$row['id']}</b></td>
        <td>{$title_short}</td>
        <td class='views'><progress value='" . $row['views'] . "' max='100'></progress></td>
        <td class='modified'><progress value='" . $row['since_modified'] . "' max='60'></progress></td>
        </tr>";
    }
} else {
    $articles_B .= "<tr class='row'>
    <td>...</td>
    <td>...</td>
    <td class='views'>...</td>
    <td class='modified'>...</td>
    </tr>";
}

$q_table = "";
if (mysqli_num_rows($result_q_table) > 0) {
    while($row = mysqli_fetch_assoc($result_q_table)) {
        // Format angka untuk memastikan dua angka di belakang koma
        $action0 = number_format($row['action0'], 2);
        $action1 = number_format($row['action1'], 2);
        $action2 = number_format($row['action2'], 2);
        $action = number_format($row['action'], 2);

        $q_table .= "<tr class='row'>
        <td class='views'>{$row['state']}</td>
        <td class='views'>{$action0}</td>
        <td class='views'>{$action1}</td>
        <td class='views'>{$action2}</td>
        <td class='views'>{$action}</td>
        </tr>";
    }
} else {
    $q_table = "<tr class='row'>
    <td>...</td>
    <td>...</td>
    <td>...</td>
    <td>...</td>
    <td>...</td>
    </tr>";
}

// Menutup koneksi
mysqli_close($con_A);
mysqli_close($con_B);

// Menampilkan hasil dalam format JSON untuk JS
echo json_encode([
    'articles_A' => $articles_A,
    'articles_B' => $articles_B,
    'q_table' => $q_table,
    'total_views' => $total_views
]);
?>