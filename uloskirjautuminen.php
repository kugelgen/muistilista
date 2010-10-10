<?php 
	session_start();
	$_SESSION['kirjauduttu'] = '0';
	unset($_SESSION['l_id']);
	unset($_SESSION['a_id']);
	header('Location: index.php');

?>
