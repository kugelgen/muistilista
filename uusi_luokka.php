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

			$nimi = filter_input(INPUT_POST, "luokka", FILTER_SANITIZE_SPECIAL_CHARS);
			$ylaluokka = $_POST["luokat"];
			
			$tarkista = $yhteys->prepare("SELECT nimi FROM luokka WHERE nimi=?");
			$tarkista->execute(array($nimi));
			$onko_olemassa = $tarkista->fetchObject();
			
			if ($onko_olemassa == FALSE) {
			
				$yhteys->beginTransaction();
				if ($ylaluokka == 0) {
					$lisaaluokka = $yhteys->prepare("INSERT INTO luokka (nimi) VALUES (?)");
					$lisaaluokka->execute(array($nimi));
				}
				else {
					$lisaaluokka = $yhteys->prepare("INSERT INTO luokka (nimi, ylaluokka) VALUES (?, ?)");
					$lisaaluokka->execute(array($nimi, $ylaluokka));
				}
				$yhteys->commit();
				header('Location: luokat.php');
			}
			
		}
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
	<a href="uloskirjautuminen.php">Kirjaudu ulos</a>
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
		<td class="noborder"><form action="<?php echo $PHP_SELF;?>" method="post">
		<input type="text" name="luokka" value="" size="15" maxlength="10" />
		</td>
		</tr>
		<?php 
			$yhteys->beginTransaction();
			$hae = $yhteys->prepare("SELECT nimi FROM luokka ORDER BY nimi");
			$hae->execute();
			$kaikki = $hae->fetchAll();
		?>
		<tr>
		<td class="noborder">Yläluokka</td>
		<td class="noborder">
		<select name="luokat">
		<option value=0>Valitse</option>
		<?php
			for ($i=0; $i<count($kaikki); $i++) { 
				$valinta = $kaikki[$i]["nimi"];
				$valintaID = $kaikki[$i]["luokkaID"];
		?>
		<option value="<?php echo $valintaID ?>"><?php echo $valinta ?></option>
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
		
		<?php
		if ($onko_olemassa != FALSE) {
			echo "Luokka $nimi löytyy jo.";
		}
		?>
	</p>
	<p></p>

</div>
</div>
<?php	} catch (PDOException $e) {
	    die("Virhe: " . $e->getMessage());
	}
}
?>
