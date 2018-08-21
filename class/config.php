<?php
// connect.php
    $host="111.223.34.243" ; // กำหนด host
    $user="polboyai_linepbx"; // กำหนดชื่อ user
    $pass="131lfAh8";   // กำหนดรหัสผ่าน
    $db="asteriskcdrdb";  // กำหนดชื่อฐานข้อมูล
	$concdr = new mysqli($host, $user, $pass, $db);
	if ($concdr->connect_errno) {
		echo $concdr->connect_error;
		exit;
	}

	$host="111.223.34.243" ; // กำหนด host
    $user="polboyai_linepbx"; // กำหนดชื่อ user
    $pass="131lfAh8";   // กำหนดรหัสผ่าน
    $db="asterisk";  // กำหนดชื่อฐานข้อมูล
	$conast = new mysqli($host, $user, $pass, $db);
	if ($conast->connect_errno) {
		echo $conast->connect_error;
		exit;
	}

	mysqli_set_charset($concdr,"utf8");
	mysqli_set_charset($conast,"utf8");

?>
