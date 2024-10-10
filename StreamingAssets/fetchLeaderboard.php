<?php

$connection = mysqli_connect('server38.hosting.reg.ru', 'u2776883_admin', 'oX6aH2dX5chL7vQ5', 'u2776883_regionium');

if (mysqli_connect_errno()) {
    error_log("Connection error: " . mysqli_connect_error());
    exit("Connection failed. Please try again later.");
}

$id = $_POST["id"];

$query = "SELECT okId, fullname, imgUrl, gender, score FROM players WHERE okId <> ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, 's', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$output = "ok\n";

while ($rows = mysqli_fetch_assoc($result)) {
    $output .= $rows["okId"] . "\t" . $rows["fullname"] . "\t" . $rows["imgUrl"] . "\t" . $rows["gender"] . "\t" . $rows["score"] . "\n";
}

echo $output;


mysqli_close($connection);
?>
