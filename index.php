<?php 
	session_start();
	$_SESSION['kirjauduttu'] = '0';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fi" lang="fi"> 
<head> 
<meta http-equiv="Content-Type" content ="text/html; charset=UTF-8"/> 
<title>Kirjautuminen</title> 
<link rel="stylesheet" type="text/css" href="main.css" /> 
 
</head> 
 
<body>
<div class="kehys">
	<div class="ruutu">
		<title>Kirjautuminen</title>
		<link rel="stylesheet" type="text/css" href="main.css" />
		<h1>Kirjautuminen</h1>
		<p>
			Tervetuloa Muistilista-sovellukseen.
		</p>
		<p>
		<form action="kirjautuminen.php" method="post">
		<p>Salasana: <input type="password" name="salasana" value="" size="15" /> <input type=submit value="Kirjaudu"/></p>
		</form>
		</p>
	</div>
</div>
</body>
</html> 
