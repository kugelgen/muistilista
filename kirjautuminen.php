<?php 
	session_start(); 	
	try {
		include 'tietokanta.php';
		session_start();
	
		$_SESSION['kirjauduttu'] = '0';
		unset($_SESSION['l_id']);
		unset($_SESSION['a_id']);
		
		$salasana = filter_input(INPUT_POST, "salasana", FILTER_SANITIZE_SPECIAL_CHARS);
		if($salasana == 'testisala') {
			$_SESSION['kirjauduttu'] = '1';
			header('Location: etusivu.php');
			exit;
		}

		else {
			header('Location: index.php');
			$_SESSION['kirjauduttu'] = '2';
			exit;
		}


	} catch (PDOException $e) {
	    die("Virhe: " . $e->getMessage());
	}
?>
