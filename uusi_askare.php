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
	
	if (isset($_POST['submit'])) {

			$nimi = filter_input(INPUT_POST, "nimi", FILTER_SANITIZE_SPECIAL_CHARS);
			$luokka = $_POST["luokat"];
			$tarkeys = $_POST["tarkeys"];
			$pvm = date(DATE_ATOM, $_SERVER['REQUEST_TIME']);
			
			$tarkista = $yhteys->prepare("SELECT nimi FROM askare WHERE nimi=?");
			$tarkista->execute(array($nimi));
			$onko_olemassa = $tarkista->fetchObject();
			
			if ($onko_olemassa == FALSE) {
			
				$yhteys->beginTransaction();
				if ($luokka == 0 && $tarkeys == 0) {
					$lisaa_askare = $yhteys->prepare("INSERT INTO askare (nimi, kirjaushetki) VALUES (?, ?)");
					$lisaa_askare->execute(array($nimi, $pvm));
				}
				else if ($tarkeys == 0) {
					$lisaa_askare = $yhteys->prepare("INSERT INTO askare (nimi, kirjaushetki, luokka) VALUES (?, ?, ?)");
					$lisaa_askare->execute(array($nimi, $pvm, $luokka));
				}
				else if ($luokka == 0) {
					$lisaa_askare = $yhteys->prepare("INSERT INTO askare (nimi, kirjaushetki, tarkeysaste) VALUES (?, ?, ?)");
					$lisaa_askare->execute(array($nimi, $pvm, $tarkeys));
				}
				else {
					$lisaa_askare = $yhteys->prepare("INSERT INTO askare (nimi, kirjaushetki, luokka, tarkeysaste) VALUES (?, ?, ?, ?)");
					$lisaa_askare->execute(array($nimi, $pvm, $luokka, $tarkeys));
				}
				$yhteys->commit();
				header('Location: etusivu.php');
			}
			
		}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fi" lang="fi">

<head>
	<title>Askare</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="main.css" />
</head>

<div class="headnav">
  <a href="etusivu.php">Etusivu</a> * 
  <a href="uusi_luokka.php">Uusi luokka</a> * 
  <a href="luokat.php">Muokkaa luokkia</a> *
  <a href="uloskirjautuminen.php">Kirjaudu ulos</a>
</div>

<div class="pienikehys">
	<div class="ruutu">
	
	<h2>Askare</h2>	
	
	<p>
	<table align="center">
	<col width="130px"/>
	<col width="170px"/>
		<tr>
		<td class="noborder">Nimi</td>
		<td class="noborder">
		<form action="<?php echo $PHP_SELF;?>" method="post">
		<input type="text" name="nimi" value="" size="20" maxlength="20" />
		</td>
		</tr>
		
		<?php 
			$yhteys->beginTransaction();
			$hae = $yhteys->prepare("SELECT luokkaid, nimi FROM luokka ORDER BY nimi");
			$hae->execute();
			$kaikki = $hae->fetchAll();
		?>
		<tr>
		<td class="noborder">Luokka</td>
		<td class="noborder">
		<select name="luokat">
		<option value=0>Valitse</option>
		<?php
			for ($i=0; $i<count($kaikki); $i++) { 
				$valinta = $kaikki[$i]["nimi"];
				$valintaID = $kaikki[$i]["luokkaid"];
		?>
		<option value="<?php echo $valintaID ?>"><?php echo $valinta ?></option>
		<?php } ?>
		</select>
		</td>
		</tr>
		
		<tr>
		<td class="noborder">DL</td>
		<td class="noborder"><form>
		<input type="text" name="dl" value="" size="15" maxlength="10" />
		</td>
		</tr>
		
		<tr>
		<td class="noborder">Tärkeys</td>
		<td class="noborder">
		<select name="tarkeys">
		<option value=0>Valitse</option>
		<?php
			for ($i=1; $i<6; $i++) {
		?>
			<option value="<?php echo $i ?>"><?php echo $i ?></option>	
		<?php } ?>
		</select>
		</td>
		</tr>
		</table>		
	</p>
	<p></p>
	<p>
		
		<p align=right><input type=submit name=submit value="Tallenna"/></p>
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
