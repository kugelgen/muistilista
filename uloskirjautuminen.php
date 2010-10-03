<?php 
	session_start();
	$_SESSION['kirjauduttu'] = '0';
	header('Location: index.php');

?>
