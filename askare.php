<?php 
session_start(); 

if($_SESSION['kirjauduttu'] != '1') {
	header('Location: index.php');
	exit;
}

else {
		
	try {
		unset($_SESSION['l_id']);
		include 'tietokanta.php';
	
		if (isset($_POST['submit'])) {

			$nimi = filter_input(INPUT_POST, "nimi", FILTER_SANITIZE_SPECIAL_CHARS);
			$luokka = $_POST["luokat"];
			if ($luokka == 0) $luokka = NULL;
			$tarkeys = $_POST["tarkeys"];
			if ($tarkeys == 0) $tarkeys = NULL;
			$pvm = date(DATE_ATOM, $_SERVER['REQUEST_TIME']);
			
			$tarkista = $yhteys->prepare("SELECT nimi FROM askare WHERE nimi=?");
			$tarkista->execute(array($nimi));
			$onko_olemassa = $tarkista->fetchObject();
			
			if (isset($_SESSION['a_id'])) {                          //muokataan
				$tama = $yhteys->prepare("SELECT nimi FROM askare WHERE askareid=?");
				$tama->execute(array($_SESSION['a_id']));
				$tamanimi = $tama->fetchObject()->nimi;

				if($onko_olemassa == TRUE && $nimi != $tamanimi) {
					$virheteksti = 1;
				}
				else {
					$muuta = $yhteys->prepare("UPDATE askare SET nimi=?, tarkeysaste=?, luokka=? WHERE askareid=?");
					$muuta->execute(array($nimi, $tarkeys, $luokka, $_SESSION['a_id']));
					header('Location: etusivu.php');
				}
				
			}
			else {                                                   //lisätään uusi
				if ($onko_olemassa == FALSE) {
					$yhteys->beginTransaction();
					$lisaa_askare = $yhteys->prepare("INSERT INTO askare (nimi, kirjaushetki, luokka, tarkeysaste) VALUES (?, ?, ?, ?)");
					$lisaa_askare->execute(array($nimi, $pvm, $luokka, $tarkeys));
					$yhteys->commit();
					header('Location: etusivu.php');
				}
				else {
					$virheteksti = 1;
				}
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
	<a href="etusivu.php">Askareet</a> * 
	<a href="luokat.php">Luokat</a> * 
	<?php if (isset($_SESSION['a_id'])) { ?>
		<a href="tyhjennaaskare.php">Uusi askare</a> * 
	<?php } else { ?>
		Uusi askare * 
	<?php } ?>
	<a href="luokka.php">Uusi luokka</a> * 
	<a href="uloskirjautuminen.php">Kirjaudu ulos</a>
</div>

<div class="pienikehys">
	<div class="ruutu">
	
	<h2>Askare</h2>	
	<?php
		if ($virheteksti == 1) {
			echo "Askare $nimi löytyy jo.";
		}
	?>
	<p>
	<table align="center">
	<col width="130px"/>
	<col width="170px"/>
	
	
		<?php	
		if(isset($_SESSION['a_id'])) {
			$askareid = $_SESSION['a_id'];
			$tiedot = $yhteys->prepare("SELECT askareid, nimi, tarkeysaste, luokka FROM askare WHERE askareid=?");
			$tiedot->execute(array($askareid));
			$pohjatiedot = $tiedot->fetchAll();
			$tiedotid = $pohjatiedot[0]["askareid"];
			$tiedotnimi = $pohjatiedot[0]["nimi"];
			$tiedottark = $pohjatiedot[0]["tarkeysaste"];
			$tiedotluok = $pohjatiedot[0]["luokka"];
		}
		?>
	
	
		<tr>
		<td class="noborder">Nimi</td>
		<td class="noborder">
		<form action="<?php echo $PHP_SELF;?>" method="post">
		<input type="text" name="nimi" value="<?php echo $tiedotnimi ?>" size="20" maxlength="20" />
		</td>
		</tr>
		
		<?php 
			$hae = $yhteys->prepare("SELECT luokkaid, nimi FROM luokka ORDER BY nimi");
			$hae->execute();
			$kaikki = $hae->fetchAll();
		?>
		<tr>
		<td class="noborder">Luokka</td>
		<td class="noborder">
		<select name="luokat">
		<option value=0>Ei mitään</option>
		<?php
			for ($i=0; $i<count($kaikki); $i++) { 
				$valinta = $kaikki[$i]["nimi"];
				$valintaID = $kaikki[$i]["luokkaid"];
		?>
		<option <?php if($tiedotluok == $valintaID) echo "selected" ?> value="<?php echo $valintaID ?>"><?php echo $valinta ?></option>
		<?php } ?>
		</select>
		</td>
		</tr>
		
<!--		<tr>
		<td class="noborder">DL</td>
		<td class="noborder"><form>
		<input type="text" name="dl" value="" size="15" maxlength="10" />
		</td>
		</tr>
-->		
		<tr>
		<td class="noborder">Tärkeys</td>
		<td class="noborder">
		<select name="tarkeys">
		<option value=0>Ei mitään</option>
		<?php
			for ($i=1; $i<6; $i++) {
		?>
			<option <?php if($tiedottark == $i) echo "selected" ?> value="<?php echo $i ?>"><?php echo $i ?></option>	
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
