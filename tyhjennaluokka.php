<?php 
	session_start();
	unset($_SESSION['l_id']);
	header('Location: uusi_luokka.php');

?>
