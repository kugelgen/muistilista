<?php 
	session_start();
	unset($_SESSION['a_id']);
	unset($_SESSION['l_id']);
	unset($_POST['submit']);
	unset($_POST['poista']);
	unset($_POST['muokkaa']);
	header('Location: etusivu.php');
?>
