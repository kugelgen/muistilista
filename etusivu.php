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
			$askare = $_POST['poista'];
			$poisto = $yhteys->prepare("DELETE FROM askare WHERE askareid=?");
			$poisto->execute(array($askare));
		} else if (isset($_POST['muokkaa'])) {
			$_SESSION['a_id'] = $_POST['muokkaa'];
			header('Location: uusi_askare.php');
		} else if (isset($_POST['submit'])) {
			$luokka = $_POST["luokat"];
			$tark = $_POST["tarkeys"];
			$luotu = $_POST["pvm"];
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
	<?php if (isset($_POST['submit'])) { ?>
		<a href="tyhjenna.php">Askareet</a> * 
	<?php } ?>
	<a href="luokat.php">Luokat</a> * 
	<a href="uusi_askare.php">Uusi askare</a> * 
	<a href="uusi_luokka.php">Uusi luokka</a> * 
	<a href="uloskirjautuminen.php">Kirjaudu ulos</a>
</div>

<div class="kehys">
	<div class="ruutu">
	<p>
	
	<table align="center">
	<col width="170px"/>
	<col width="130px"/>
	<col width="150px"/>	
	<col width="150px"/>
<!--	<col width="150px"/>-->
	<col width="55px"/>
		<tr align="center">
		<th>Askare</th>
		<th>Luokka</th>
		<th>Tärkeys</th>
		<th>Luotu</th>
<!--		<th>DL</th>-->
		</tr>
		<tr align="center">
		<td class="noborder"></td>
		<form action="<?php echo $PHP_SELF;?>" method="post">
		<td class="noborder"><select name="luokat">
		<?php 
			$hae = $yhteys->prepare("SELECT luokkaid, nimi FROM luokka ORDER BY nimi");
			$hae->execute();
			$kaikki = $hae->fetchAll();
		?>
		<option value=0>Kaikki</option>
		<?php
			for ($i=0; $i<count($kaikki); $i++) { 
				$valinta = $kaikki[$i]["nimi"];
				$valintaID = $kaikki[$i]["luokkaid"];
		?>
		<option <?php if($luokka == $valintaID) echo "selected" ?> value="<?php echo $valintaID ?>"><?php echo $valinta ?></option>
		<?php } ?>
		</select>
		</td>
		
		<td class="noborder"><select name="tarkeys">
		<option value=0>Ei merkitystä</option>
		<option <?php if($tark == 1) echo "selected" ?> value=1>1 ensin</option>
		<option <?php if($tark == 2) echo "selected" ?> value=2>5 ensin</option>
		
-->		<td class="noborder"><input type=radio name="pvm" <?php if($luotu != 2) echo "checked" ?> value=1 >uusin ensin<br>
		<input type=radio name="pvm" <?php if($luotu == 2) echo "checked" ?> value=2>vanhin ensin</td>
<!--		<td class="noborder"><input type=radio name="pvm2" value=1>ensimmäinen ^<br>
		<input type=radio name="pvm2" value=2>viimeinen ^</td>-->
		<td align="right" class="noborder"><input type=submit name=submit value="Hae"/></td>
		</form>
		</tr>
		</table>
	</p>
	<p>
		<table align="center">
		<col width="170px"/>
		<col width="130px"/>
		<col width="150px"/>	
		<col width="150px"/>
<!--		<col width="150px"/>-->
		<col width="55px"/>
		
		<?php 
			if ($luokka != 0 && $tark == 1 && $luotu == 2) {         //valittu luokka, tärkeys 1 ensin ja vanhin ensin
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid AND a.luokka=? UNION SELECT a.askareid, a.nimi as askare, l1.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l1, luokka l2 WHERE a.luokka = l1.luokkaid AND l1.ylaluokka = l2.luokkaid AND l2.luokkaid=? ORDER BY tarkeysaste, kirjaushetki");
				$hae->execute(array($luokka, $luokka));
			}
			else if ($luokka != 0 && $tark == 2 && $luotu == 2) {    //valittu luokka, tärkeys 5 ensin ja vanhin ensin
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid AND a.luokka=? UNION SELECT a.askareid, a.nimi as askare, l1.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l1, luokka l2 WHERE a.luokka = l1.luokkaid AND l1.ylaluokka = l2.luokkaid AND l2.luokkaid=? ORDER BY tarkeysaste DESC, kirjaushetki");
				$hae->execute(array($luokka, $luokka));
			}
			else if ($luokka != 0 && $tark == 1) {                   //valittu luokka, tärkeys 1 ensin (uusin ensin)
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid AND a.luokka=? UNION SELECT a.askareid, a.nimi as askare, l1.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l1, luokka l2 WHERE a.luokka = l1.luokkaid AND l1.ylaluokka = l2.luokkaid AND l2.luokkaid=? ORDER BY tarkeysaste, kirjaushetki DESC");
				$hae->execute(array($luokka, $luokka));
			}
			else if ($luokka != 0 && $tark == 2) {                   //valittu luokka, tärkeys 5 ensin (uusin ensin)
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid AND a.luokka=? UNION SELECT a.askareid, a.nimi as askare, l1.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l1, luokka l2 WHERE a.luokka = l1.luokkaid AND l1.ylaluokka = l2.luokkaid AND l2.luokkaid=? ORDER BY tarkeysaste DESC, kirjaushetki DESC");
				$hae->execute(array($luokka, $luokka));
			}
			else if ($luokka != 0 && $luotu == 2) {                  //valittu luokka ja vanhin ensin
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid AND a.luokka=? UNION SELECT a.askareid, a.nimi as askare, l1.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l1, luokka l2 WHERE a.luokka = l1.luokkaid AND l1.ylaluokka = l2.luokkaid AND l2.luokkaid=? ORDER BY kirjaushetki");
				$hae->execute(array($luokka, $luokka));
			}
			else if ($luokka != 0) {                                 //valittu luokka (uusin ensin)
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid AND a.luokka=? UNION SELECT a.askareid, a.nimi as askare, l1.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l1, luokka l2 WHERE a.luokka = l1.luokkaid AND l1.ylaluokka = l2.luokkaid AND l2.luokkaid=? ORDER BY kirjaushetki DESC");
				$hae->execute(array($luokka, $luokka));
			}
			else if ($tark == 1 && $luotu == 2) {                    //valittu tärkeys 1 ensin ja vanhin ensin
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid UNION SELECT a.askareid, a.nimi as askare, NULL as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a WHERE a.luokka is null ORDER BY tarkeysaste, kirjaushetki");
				$hae->execute();
			}
			else if ($tark == 2 && $luotu == 2) {                    //valittu tärkeys 5 ensin ja vanhin ensin
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid UNION SELECT a.askareid, a.nimi as askare, NULL as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a WHERE a.luokka is null ORDER BY tarkeysaste DESC, kirjaushetki");
				$hae->execute();
			}
			else if ($tark == 1) {                                   //valittu tärkeys 1 ensin (uusin ensin)
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid UNION SELECT a.askareid, a.nimi as askare, NULL as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a WHERE a.luokka is null ORDER BY tarkeysaste, kirjaushetki DESC");
				$hae->execute();
			}
			else if ($tark == 2) {                                   //valittu tärkeys 5 ensin (uusin ensin)
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid UNION SELECT a.askareid, a.nimi as askare, NULL as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a WHERE a.luokka is null ORDER BY tarkeysaste DESC, kirjaushetki DESC");
				$hae->execute();
			}
			else if ($luotu == 2) {                                   //valittu vanhin ensin
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid UNION SELECT a.askareid, a.nimi as askare, NULL as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a WHERE a.luokka is null ORDER BY kirjaushetki");
				$hae->execute();
			}
			else {                                                    //uusin ensin (oletus)
				$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid UNION SELECT a.askareid, a.nimi as askare, NULL as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a WHERE a.luokka is null ORDER BY kirjaushetki DESC");
				$hae->execute();
			}
			$kaikki = $hae->fetchAll();
			
			for ($i=0; $i<count($kaikki); $i++) {
				$askareenID = $kaikki[$i]["askareid"];
				$askarenimi = $kaikki[$i]["askare"];
				$luokannimi = $kaikki[$i]["luok"];
				$kirjhetki = $kaikki[$i]["kirjaushetki"];
//				$dl = $kaikki[$i]["dl"];
				$tarkeys = $kaikki[$i]["tarkeysaste"];
		?>
		
		<tr>
		<td><?php echo $askarenimi ?></td>
		<td><?php echo $luokannimi ?></td>
		<td><?php echo $tarkeys ?></td>
		<td><?php echo $kirjhetki ?></td>
<!--		<td><?php// echo $dl ?></td>-->
		
		<td class="noborder"><form action="<?php echo $PHP_SELF;?>" method="post" >
		<input type=image src="muokkaa.jpg" alt="muokkaa" name="muokkaa" value="<?php echo $askareenID ?>">  <input type=image src="poista.jpg" alt="poista" name="poista" value="<?php echo $askareenID ?>" ></form></td>
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
