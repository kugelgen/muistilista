<?php 
session_start(); 

if($_SESSION['kirjauduttu'] != '1') {
	header('Location: index.php');
	exit;
}

else {
	unset($_SESSION['a_id']);
	try {
		include 'tietokanta.php';
	
		if (isset($_POST['submit'])) {

			$nimi = filter_input(INPUT_POST, "luokka", FILTER_SANITIZE_SPECIAL_CHARS);
			$ylaluokka = $_POST["luokat"];
			if ($ylaluokka == 0) $ylaluokka = NULL;
			
			$tarkista = $yhteys->prepare("SELECT nimi FROM luokka WHERE nimi=?");
			$tarkista->execute(array($nimi));
			$onko_olemassa = $tarkista->fetchObject();
			
			$tarkista = $yhteys->prepare("SELECT nimi FROM luokka WHERE luokkaid=? AND ylaluokka IS NOT NULL");
			$tarkista->execute(array($ylaluokka));
			$onko_alaluokka = $tarkista->fetchObject();
			
			if (isset($_SESSION['l_id'])) {
				$tama = $yhteys->prepare("SELECT nimi FROM luokka WHERE luokkaid=?");
				$tama->execute(array($_SESSION['l_id']));
				$tamanimi = $tama->fetchObject()->nimi;

				if($onko_olemassa == TRUE && $nimi != $tamanimi) {
					$virheteksti = 1;
				}
				
				else {
					$muutayl = $yhteys->prepare("UPDATE luokka SET nimi=?, ylaluokka=? WHERE luokkaid=?");
					$muutayl->execute(array($nimi, $ylaluokka, $_SESSION['l_id']));
					header('Location: luokat.php');
				}
				 
			}
			else {
			
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
					unset($_SESSION['l_id']);
					header('Location: luokat.php');
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
	<title>Luokka</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="main.css" />
</head>

<div class="headnav">
	<a href="etusivu.php">Askareet</a> * 
	<a href="luokat.php">Luokat</a> * 
	<a href="uusi_askare.php">Uusi askare</a> * 
	<?php if (isset($_SESSION['l_id'])) { ?>
		<a href="tyhjennaluokka.php">Uusi luokka</a> * 
	<?php } ?>
	<a href="uloskirjautuminen.php">Kirjaudu ulos</a>
</div>

<div class="pienikehys">
	<div class="ruutu">
	
	<h2>Luokka</h2>	
	<p>
	<table align="center">
	<col width="130px"/>
	<col width="170px"/>
	
		<?php	
		if(isset($_SESSION['l_id'])) {
			$luokid = $_SESSION['l_id'];
			$tiedot = $yhteys->prepare("SELECT luokkaid, nimi, '' as ylaluokka FROM luokka WHERE luokkaid=? AND ylaluokka is null UNION SELECT A.luokkaid, A.nimi, B.nimi as ylaluokka FROM luokka A, luokka B WHERE A.luokkaid=? AND A.ylaluokka=B.luokkaid");
			$tiedot->execute(array($luokid, $luokid));
			$pohjatiedot = $tiedot->fetchAll();
			$tiedotid = $pohjatiedot[0]["luokkaid"];
			$tiedotnimi = $pohjatiedot[0]["nimi"];
			$ylaluok = $pohjatiedot[0]["ylaluokka"];
		}
		?>
	
		<tr>
		<td class="noborder">Nimi</td>
		<td class="noborder"><form action="<?php echo $PHP_SELF;?>" method="post">
		<input type="text" name="luokka" value="<?php echo $tiedotnimi ?>" size="15" maxlength="10" />
		</td>
		</tr>
		<?php 
			$hae = $yhteys->prepare("SELECT nimi, luokkaid FROM luokka WHERE ylaluokka IS NULL ORDER BY nimi");
			$hae->execute();
			$kaikki = $hae->fetchAll();
			if(isset($_SESSION['l_id'])) {
				$tarkista = $yhteys->prepare("SELECT nimi FROM luokka WHERE ylaluokka=?");
				$tarkista->execute(array($_SESSION['l_id']));
				$onko_ylaluokka = $tarkista->fetchObject();
			}
		?>
		<tr>
		<td class="noborder">Yläluokka</td>
		<td class="noborder">
		<select name="luokat">
		<option value=0>Ei mitään</option>
		<?php
			if ($onko_ylaluokka == FALSE) {
				for ($i=0; $i<count($kaikki); $i++) { 
					$valinta = $kaikki[$i]["nimi"];
					$valintaID = $kaikki[$i]["luokkaid"];
					if ($valintaID != $_SESSION['l_id']) {
		?>
		<option <?php if($ylaluok == $valinta) echo "selected" ?> value="<?php echo $valintaID ?>"><?php echo $valinta ?></option>
		
		<?php
					}
				}
			} ?>
		
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
		if ($virheteksti == 1) {
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
