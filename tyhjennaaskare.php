<?php 
	session_start();
	unset($_SESSION['a_id']);
	header('Location: uusi_askare.php');

?>
