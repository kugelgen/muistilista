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
  <a href="uloskirjautuminen.php">Kirjaudu ulos</a>
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
		
		<?php 
			$hae = $yhteys->prepare("SELECT luokkaid, nimi, ylaluokka FROM luokka ORDER BY nimi");
			$hae->execute();
			$kaikki = $hae->fetchAll();
			
			$yl_tiedot = $yhteys->prepare("SELECT B.nimi FROM luokka A, luokka B WHERE A.ylaluokka=B.luokkaID AND A.ylaluokka=?");
		?>
		
		<tr>
		<?php
		for ($i=0; $i<count($kaikki); $i++) {
			$luokanID = $kaikki[$i]["luokkaid"];
			$luokannimi = $kaikki[$i]["nimi"];
			$ylaluokkaID = $kaikki[$i]["ylaluokka"];
			
			$yl_tiedot->execute(array($ylaluokkaID));
			$ylaluokannimi = $yl_tiedot->fetchObject()->nimi;
		?>
		<td><?php echo $luokannimi ?></td>
		<td><?php echo $ylaluokannimi ?></td>
		<td class="noborder"><form action="<?php echo $PHP_SELF;?>" method="post">
		<input type=image src="muokkaa.jpg" alt="Muokkaa" name="muokkaa"/>  <input type=image src="poista.jpg" alt="Poista" name="poista"></form></td>
		</tr>
		<?php } ?>
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
