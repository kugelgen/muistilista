<?php 
	session_start(); 	
	try {
		$yhteys = new PDO("pgsql:host=localhost;dbname=kugelgen",
						"kugelgen", "d3626dddc9b387bc");	
		$yhteys->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		session_start();
	
		$_SESSION['kirjauduttu'] = '0';
		
		$salasana = $_POST["salasana"];
		if($salasana == 'testisala') {
			$_SESSION['kirjauduttu'] = '1';
			header('Location: etusivu.php');
			exit;
		}

		else {
			header('Location: index.php');
			exit;
		}


	} catch (PDOException $e) {
	    die("Virhe: " . $e->getMessage());
	}
?>
