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
	<title>Uusi luokka</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="main.css" />
</head>

<div class="headnav">
	<a href="etusivu.php">Etusivu</a> *
	<a href="uusi_askare.php">Uusi askare</a> *  
	<a href="luokat.php">Muokkaa luokkia</a> *
	<a href="index.php">Kirjaudu ulos</a>
</div>

<div class="pienikehys">
	<div class="ruutu">
	
	<h2>Luokka</h2>	
	
	<p>
	<table align="center">
	<col width="130px"/>
	<col width="170px"/>
		<tr>
		<td class="noborder">Nimi</td>
		<td class="noborder"><form action="jotain" method="post">
		<input type="text" name="nimi" value="" size="15" maxlength="10" />
		</td>
		</tr>
		
		<tr>
		<td class="noborder">Yläluokka</td>
		<td class="noborder">
		<select name="luokat">
		<option>Valitse</option>
		<option>joku luokka</option>
		</select>
		</td>
		</tr>
		
		</table>		
	</p>
	<p></p>
	<p>
		<p align=right><input type=submit value="Tallenna"/></p>
		</form>
	</p>
	<p></p>

</div>
</div>
<?php	} catch (PDOException $e) {
	    die("Virhe: " . $e->getMessage());
	}
}
?>
