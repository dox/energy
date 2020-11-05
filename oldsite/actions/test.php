<?php

$errorMSG = "";

/* NAME */
if (empty($_POST["name"])) {
    $errorMSG = "<li>Name is required</<li>";
} else {
    $name = $_POST["name"];
}

/* EMAIL */
if (empty($_POST["email"])) {
    $errorMSG .= "<li>Email is required</li>";
} else if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $errorMSG .= "<li>Invalid email format</li>";
}else {
    $email = $_POST["email"];
}

/* MSG SUBJECT */
if (empty($_POST["msg_subject"])) {
    $errorMSG .= "<li>Subject is required</li>";
} else {
    $msg_subject = $_POST["msg_subject"];
}

/* MESSAGE */
if (empty($_POST["message"])) {
    $errorMSG .= "<li>Message is required</li>";
} else {
    $message = $_POST["message"];
}

if(empty($errorMSG)){
	$msg = "Name: ".$name.", Email: ".$email.", Subject: ".$msg_subject.", Message:".$message;
	echo json_encode(['code'=>200, 'msg'=>$msg]);
	exit;
}

echo json_encode(['code'=>404, 'msg'=>$errorMSG]);

?>