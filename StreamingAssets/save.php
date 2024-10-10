<?php

$connection = mysqli_connect('server38.hosting.reg.ru', 'u2776883_admin', 'oX6aH2dX5chL7vQ5', 'u2776883_regionium');

if (mysqli_connect_errno()) {
    error_log("Connection error: " . mysqli_connect_error());
    exit("Connection failed. Please try again later.");
}

$id = $_POST["id"];
$score = $_POST["score"];
$save = $_POST["save"];

$query = "SELECT okId, score, save FROM players WHERE okId = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, 's', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    error_log("User with such ID does not exist.");
    exit();
}
else{
    $updateQuery = "UPDATE players SET score = ?, save = ? WHERE okId = ?";
    $updateStmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, 'iss', $score, $save, $id);
    
    if (!mysqli_stmt_execute($updateStmt)) {
        error_log("Update query failed: " . mysqli_error($connection));
        exit("Failed to update user data.");
    }
}

echo "ok";

mysqli_close($connection);
?>
