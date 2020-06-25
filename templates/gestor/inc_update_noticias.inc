<?php
   	$id=$_POST['id'];
   	$name=$_POST['name'];
   	$email=$_POST['email'];
   	$phone=$_POST['phone'];
   	$city=$_POST['city'];
   	$sql = "UPDATE `crud`
   	SET `name`='$name',
   	`email`='$email',
   	`phone`='$phone',
   	`city`='$city' WHERE id=$id";
   	if (mysqli_query($conn, $sql)) {
   		echo json_encode(array("statusCode"=>200));
   	}
   	else {
   		echo json_encode(array("statusCode"=>201));
   	}
   	mysqli_close($conn);
?>