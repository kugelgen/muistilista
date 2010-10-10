<?php
/*
		$yhteys = new PDO("pgsql:host=localhost;dbname=kugelgen",
						"kugelgen", "d3626dddc9b387bc");	
*/
		$yhteys = new PDO("pgsql:host=localhost;dbname=muistilista",
						"muistilista", "muistilista");
		
		$yhteys->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
