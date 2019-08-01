<?php

// Connect to database
// $conn = new SQLite3($dbFile);
$conn = new PDO('sqlite:'.$dbFile);

if(!$conn) {
    http_response_code(500);
    echo "Could not connect database.";
    return;
}

$conn-> exec("CREATE TABLE IF NOT EXISTS captchas(
    id varchar(50) PRIMARY KEY, 
    created integer NOT NULL)");

// Done autocreate table

// define main functions
function removeOldCaptchas($conn){
    $stmt = $conn->prepare("DELETE FROM captchas WHERE created < :minAge");

    if(!$stmt){
        print_r($conn->errorInfo());
    }

    $time = time() - (60*60);
    $stmt->bindparam(':minAge',$time, PDO::PARAM_INT);
    $stmt->execute();
}

function existsCaptcha($conn, $id){

    removeOldCaptchas($conn);

    $stmt = $conn->prepare("SELECT id FROM captchas WHERE id=:id");

    if(!$stmt){
        print_r($conn->errorInfo());
    }

    $stmt->bindparam(':id',$id, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    //print_r($row);

    return $row != false;
    /*
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($res);
    exit();
    */
}

function storeCaptcha($conn, $id){
    $stmt = $conn->prepare("INSERT INTO captchas (id,created) VALUES (:id,:created)");

    if(!$stmt){
        print_r($conn->errorInfo());
    }

    $stmt->bindparam(':id',$id, PDO::PARAM_STR);
    $time = time();
    $stmt->bindparam(':created',$time, PDO::PARAM_INT);
    $res = $stmt->execute();
}

function deleteCaptcha($conn, $id){
    $stmt = $conn->prepare("DELETE FROM captchas WHERE id=:id");

    if(!$stmt){
        print_r($conn->errorInfo());
    }

    $stmt->bindparam(':id',$id, PDO::PARAM_STR);
    $res = $stmt->execute();
}