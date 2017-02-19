<?php

//SQL illesztő konfigurációs fájl
//2013.01.05.
//Tüske Balázs (www.programkeszites.hu)
if (!isset($hitelesites) or ( $hitelesites != "OK"))
{
  header("Location: index.php");
  exit;
}



$this->sql_tabla = "sql_hibak";
$this->logtable = 2;
//0 - semmi, 1 - csak a hibák, 2 - elsődleges kulcs is, 3 - minden
$this->monitor = 1;
//0 - semmi, 1 - csak a hibák, 2 - elsődleges kulcs is, 3 - minden
$this->email = 1;
//0 - semmi, 1 - csak a hibák, 2 - elsődleges kulcs is, 3 - minden

global $hitelesites;
global $weblap_admin_email;
global $weblap_email;
global $weblap_nev;
$this->email_to = $weblap_admin_email;
$this->email_from = $weblap_email;
$this->email_from_name = $weblap_nev;
//$this->email_config["sablon"] = "";
?>