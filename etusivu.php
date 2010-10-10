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
  <a href="uusi_askare.php">Uusi askare</a> * 
  <a href="uusi_luokka.php">Uusi luokka</a> * 
  <a href="luokat.php">Muokkaa luokkia</a> *
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
	<col width="150px"/>
	<col width="60px"/>
		<tr align="center">
		<th>Askare</th>
		<th>Luokka</th>
		<th>Luotu</th>
		<th>DL</th>
		<th>Tärkeys</th>
		</tr>
		<tr>
		<td class="noborder"></td>
		<td class="noborder"><select name="luokat">
		<option>Valitse</option>
		<option>joku luokka</option>
		</select></td>
		<td class="noborder"><input type=radio name="pvm1" value="SELECT jotain" checked>uusin ensin<br>
		<input type=radio name="pvm1" value="SELECT jotain muuta">vanhin ensin</td>
		<td class="noborder"><input type=radio name="pvm2" value="SELECT jotain">ensimmäinen ^<br>
		<input type=radio name="pvm2" value="SELECT jotain muuta">viimeinen ^</td>
		<td class="noborder"><input type=radio name="tarkeys" value="SELECT jotain">1 ensin<br>
		<input type=radio name="tarkeys" value="SELECT jotain muuta">5 ensin</td>
		</tr>
		</table>
	</p>
	<p>
		<table align="center">
		<col width="170px"/>
		<col width="130px"/>
		<col width="150px"/>	
		<col width="150px"/>
		<col width="150px"/>
		<col width="60px"/>
		
		<?php 
			$hae = $yhteys->prepare("SELECT a.askareid, a.nimi as askare, l.nimi as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a, luokka l WHERE a.luokka = l.luokkaid UNION SELECT a.askareid, a.nimi as askare, NULL as luok, a.kirjaushetki, a.dl, a.tarkeysaste FROM askare a WHERE a.luokka is null ORDER BY kirjaushetki");
			$hae->execute();
			$kaikki = $hae->fetchAll();
			
			for ($i=0; $i<count($kaikki); $i++) {
				$askareenID = $kaikki[$i]["askareid"];
				$askarenimi = $kaikki[$i]["askare"];
				$luokannimi = $kaikki[$i]["luok"];
				$kirjhetki = $kaikki[$i]["kirjaushetki"];
				$dl = $kaikki[$i]["dl"];
				$tarkeys = $kaikki[$i]["tarkeysaste"];
		?>
		
		<tr>
		<td><?php echo $askarenimi ?></td>
		<td><?php echo $luokannimi ?></td>
		<td><?php echo $kirjhetki ?></td>
		<td><?php echo $dl ?></td>
		<td><?php echo $tarkeys ?></td>
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
