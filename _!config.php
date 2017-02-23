<?php
//Konfigurációs fájl. 
//2016.05.13.
//Tüske Balázs (www.programkeszites.hu)
global $hitelesites;
if (!isset($hitelesites) or ($hitelesites!="OK")) { header("Location: index.php");exit;}

	mb_internal_encoding("UTF-8");
	$adatbazis='spinnerserver';
	$adatb_host='localhost';
	$adatb_felhasznalo='root';
	$adatb_jelszo='';
  $karakterkeszlet="utf-8";
  $weblap_nev="Builder Game Web Server";
  $weblap_host="http://".$_SERVER["HTTP_HOST"];
  $weblap_email="admin@localhost";
  $weblap_telefon="";
  $weblap_tulajdonos="Green Burger Spinners";
  $weblap_admin="Green Burger Spinners";
  $weblap_admin_email="admin@localhost";  
?>