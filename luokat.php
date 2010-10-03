<?php 
session_start(); 

if($_SESSION['kirjauduttu'] != '1') {
	header('Location: index.php');
	exit;
}

else {
		
	try {
	$yhteys = new PDO("pgsql:host=localhost;dbname=kugelgen",
		              "kugelgen", "d3626dddc9b387bc");	
	$yhteys->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fi" lang="fi">

<head>
	<title>Muistilista</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="main.css" />
</head>

<div class="headnav">
  <a href="etusivu.php">Etusivu</a> * 
  <a href="uusi_askare.php">Uusi askare</a> *  
  <a href="uusi_luokka.php">Uusi luokka</a> * 
  <a href="index.php">Kirjaudu ulos</a>
</div>

<div class="pienikehys">
	<div class="ruutu">

	<h2>Luokat</h2>	

	<p>
	<table align="center">
	<col width="150px"/>
	<col width="150px"/>
	<col width="60px"/>
		<tr align="center">
		<th class="noborder">Luokka</th>
		<th class="noborder">Yläluokka</th>
		</tr>
		<tr>
		<td>eka luokka</td>
		<td></td>
		<td class="noborder"><form><input type=image src="muokkaa.jpg" alt="Muokkaa"/>  <input type=image src="poista.jpg" alt="Poista"></form></td>
		</tr>
		<tr>
		<td>toka luokka</td>
		<td>tokan yläluokka</td>
		<td class="noborder"><form><input type=image src="muokkaa.jpg" alt="Muokkaa"/>  <input type=image src="poista.jpg" alt="Poista"></form></td>
		</tr>
	</table>
	</p>
	<p></p>

</div>
</div>
<?php	} catch (PDOException $e) {
	    die("Virhe: " . $e->getMessage());
	}
}
?>
