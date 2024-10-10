<?php

$connection = mysqli_connect('server38.hosting.reg.ru', 'u2776883_admin', 'oX6aH2dX5chL7vQ5', 'u2776883_regionium');

if (mysqli_connect_errno()) {
    error_log("Connection error: " . mysqli_connect_error());
    exit("Connection failed. Please try again later.");
}

$id = $_POST["id"];

$query = "SELECT okId, fullname, imgUrl, gender, score, save FROM players WHERE okId = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, 's', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($data = mysqli_fetch_assoc($result)) {
    
    $fullname = $data["fullname"];
    $imgUrl = $data["imgUrl"];
    $gender = $data["gender"];
    $score = $data["score"];
    $save = $data["save"];

    echo "ok\n" . $fullname . "\n" . $imgUrl . "\n" . $gender . "\n" . $score . "\n" . $save;

} else {

    error_log("User with such ID does not exist.");
    exit();
}

mysqli_close($connection);
?>
