<?php
//MySQL adatbázis kapcsolatot létesítő modul
//2011.08.23.
//Tüske Balázs (www.programkeszites.hu)
global $hitelesites;
if (!isset($hitelesites) or ($hitelesites!="OK")) { header("Location: index.php");exit;}

	$kapcsolat = mysql_connect( $adatb_host, $adatb_felhasznalo, $adatb_jelszo ) or die ( "<center>Az adatbázis szerver nem elérhető. Kérjük lépjen kapcsolatba az oldal üzemeltetőjével:<br>".$weblap_tulajdonos." / ".$weblap_email." / ".$weblap_telefon."<br>".mysql_error()."</center>" );
	mysql_select_db( $adatbazis, $kapcsolat ) or die ( "<center>Nem lehet a megnyitni az adatbázist. Az oldal nem érhető el. Kérjük lépjen kapcsolatba az oldal üzemeltetőjével:<br>".$weblap_tulajdonos." / ".$weblap_email." / ".$weblap_telefon."<br>".mysql_error()."</center>" );
  require_once("_sql.php");
  $sql_kapcsolat=new sql("Kapcsolat()");
  $sql_kapcsolat->query("SET NAMES 'utf8'");
  $sql_kapcsolat->query('SET CHARACTER SET utf8');
  unset($sql_kapcsolat);
	print(mysql_error());
?>