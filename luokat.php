<?php 
session_start(); 

if($_SESSION['kirjauduttu'] != '1') {
	header('Location: index.php');
	exit;
}

else {
	unset($_SESSION['l_id']);
	unset($_SESSION['a_id']);
	try {
		include 'tietokanta.php';
	
		if (isset($_POST['poista'])) {
			$luokka = $_POST['poista'];
			
			$tarkista = $yhteys->prepare("SELECT nimi FROM luokka WHERE ylaluokka=?");
			$tarkista->execute(array($luokka));
			$onko_ylaluokkana = $tarkista->fetchObject();
			
			$tarkista = $yhteys->prepare("SELECT nimi FROM askare WHERE luokka=?");
			$tarkista->execute(array($luokka));
			$onko_luokkana = $tarkista->fetchObject();
			
			$tarkista = $yhteys->prepare("SELECT nimi FROM luokka WHERE luokkaid=?");
			$tarkista->execute(array($luokka));
			$lnimi = $tarkista->fetchObject()->nimi;
			
			if ($onko_ylaluokkana == TRUE) {
				$virheteksti = 1;
			}
			else if ($onko_luokkana == TRUE) {
				$virheteksti = 2;
			}
			else {
				$poisto = $yhteys->prepare("DELETE FROM luokka WHERE luokkaid=?");
				$poisto->execute(array($luokka));
			}
		} else if (isset($_POST['muokkaa'])) {
			$_SESSION['l_id'] = $_POST['muokkaa'];
			header('Location: uusi_luokka.php');
		}
	
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
  <a href="etusivu.php">Askareet</a> * 
  Luokat * 
  <a href="uusi_askare.php">Uusi askare</a> *  
  <a href="uusi_luokka.php">Uusi luokka</a> * 
  <a href="uloskirjautuminen.php">Kirjaudu ulos</a>
</div>

<div class="keskikehys">
	<div class="ruutu">

	<h2>Luokat</h2>	

	<p>
	<table align="center">
	<col width="150px"/>
	<col width="150px"/>
	<col width="55px"/>
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
		<td class="noborder"><form action="<?php echo $PHP_SELF;?>" method="post" >
		<input type=image src="muokkaa.jpg" alt="muokkaa" title="muokkaa" name="muokkaa" value="<?php echo $luokanID ?>">  <input type=image src="poista.jpg" alt="poista" title="poista" name="poista" value="<?php echo $luokanID ?>" ></form></td>
		</tr>
		<?php } ?>
	</table>
	</p>
	<p></p>
		<?php
		if ($virheteksti == 1) {
			echo "Et voi poistaa luokkaa $lnimi, sillä se on käytössä yläluokkana.";
		}
		if ($virheteksti == 2) {
			echo "Et voi poistaa luokkaa $lnimi, sillä se on käytössä yhden tai useamman askareen luokkana.";
		}
		?>
</div>
</div>
<?php	} catch (PDOException $e) {
	    die("Virhe: " . $e->getMessage());
	}
}
?>
