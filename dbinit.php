<?php

// Connect to database
$conn = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbDatabase);

if(!$conn) {
    http_response_code(500);
    echo "Could not connect database.";
    return;
}

// Auto create captchas table
$sqlCreate = <<<'EOT'
CREATE TABLE IF NOT EXISTS `captchas` (
  `id` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOT;

$result = $conn->query($sqlCreate);

// Done autocreate table


// define main functions
function removeOldCaptchas($conn){
    $conn->query("DELETE FROM captchas WHERE created < (NOW() - INTERVAL 60 MINUTE)");
}

function existsCaptcha($conn, $id){

    removeOldCaptchas($conn);

    $stmt = $conn->prepare("SELECT id FROM captchas WHERE id=?");
    $stmt->bind_param("s",$id);
    $stmt->execute();
    $stmt->store_result();

    $res = $stmt->num_rows > 0;
    return $res;
    /*
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($res);
    exit();
    */
}

function storeCaptcha($conn, $id){
    $stmt = $conn->prepare("INSERT INTO captchas (id,created) VALUES (?,NOW())");
    $stmt->bind_param("s",$id);
    $res = $stmt->execute();
}

function deleteCaptcha($conn, $id){
    $stmt = $conn->prepare("DELETE FROM captchas WHERE id=?");
    $stmt->bind_param("s",$id);
    $res = $stmt->execute();
}