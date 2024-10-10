<?php

$connection = mysqli_connect('server38.hosting.reg.ru', 'u2776883_admin', 'oX6aH2dX5chL7vQ5', 'u2776883_regionium');

if (mysqli_connect_errno()) {
    error_log("Connection error: " . mysqli_connect_error());
    exit("Connection failed. Please try again later.");
}

$id = $_POST["id"];
$fullname = $_POST["fullname"];
$imgUrl = $_POST["imgUrl"];
$gender = $_POST["gender"];

$query = "SELECT okId FROM players WHERE okId = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, 's', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    $insertQuery = "INSERT INTO players (okId, fullname, imgUrl, gender) VALUES (?, ?, ?, ?)";
    $insertStmt = mysqli_prepare($connection, $insertQuery);
    mysqli_stmt_bind_param($insertStmt, 'sssi', $id, $fullname, $imgUrl, $gender);
    
    if (!mysqli_stmt_execute($insertStmt)) {
        error_log("Insert query failed: " . mysqli_error($connection));
        exit("Failed to insert user.");
    }
} else {
    $updateQuery = "UPDATE players SET fullname = ?, imgUrl = ?, gender = ? WHERE okId = ?";
    $updateStmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, 'ssis', $fullname, $imgUrl, $gender, $id);
    
    if (!mysqli_stmt_execute($updateStmt)) {
        error_log("Update query failed: " . mysqli_error($connection));
        exit("Failed to update user data.");
    }
}

echo "ok";

mysqli_close($connection);
?>
