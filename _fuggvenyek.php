<?php


global $hitelesites;
if (!isset($hitelesites) or ( $hitelesites != "OK"))
{
  header("Location: index.php");
  exit;
}



$honapok[1] = "Jan.";
$honapok[2] = "Febr.";
$honapok[3] = "Márc.";
$honapok[4] = "Ápr.";
$honapok[5] = "Máj.";
$honapok[6] = "Jún.";
$honapok[7] = "Júl.";
$honapok[8] = "Aug.";
$honapok[9] = "Szept.";
$honapok[10] = "Okt.";
$honapok[11] = "Nov.";
$honapok[12] = "Dec.";


$honapok_hosszu[1] = "Január";
$honapok_hosszu[2] = "Február";
$honapok_hosszu[3] = "Március";
$honapok_hosszu[4] = "Április";
$honapok_hosszu[5] = "Május";
$honapok_hosszu[6] = "Június";
$honapok_hosszu[7] = "Július";
$honapok_hosszu[8] = "Augusztus";
$honapok_hosszu[9] = "Szeptember";
$honapok_hosszu[10] = "Október";
$honapok_hosszu[11] = "November";
$honapok_hosszu[12] = "December";


$napok[0] = 'Vasárnap';
$napok[1] = 'Hétfő';
$napok[2] = 'Kedd';
$napok[3] = 'Szerda';
$napok[4] = 'Csütörtök';
$napok[5] = 'Péntek';
$napok[6] = 'Szombat';

function js_biztose($szoveg, $hiv, $href = true)
{
  if ($href)
  {
    return "href='JavaScript:var r=confirm(\"" . $szoveg . "\");if (r==true) window.location=\"" . $hiv . "\"'";
  }
  else
  {
    return "onclick='var r=confirm(\"" . $szoveg . "\");if (r==true) window.location=\"" . $hiv . "\"'";
  }
}

function sql_most($timestamp = 0)
{
  if ($timestamp != 0)
  {
    return date("Y-m-d H:i:s", $timestamp);
  }
  else
  {
    return date("Y-m-d H:i:s");
  }
}

function mezo_to_sql_insert($adat, $m, $tabla)
//$adat a tömb, amiből készül a lekérdezés, 
//$m a mező a tömbben, ami értékként belekerül a táblába
{
  //varchar, email, password, checkbox, ckeditor, longtext, datetime, integer, float, elvalaszto
  $ertekek = "";
  $mezok = "";
  foreach ($adat as $k => $e)
  {
    if (((isset($e["nincsfelvitel"]) == false) or ( $e["nincsfelvitel"] == false)) and ( $e["tipus"] != "ellkod") and ( $e["tipus"] != "elvalaszto"))
    {
      if ($e["tipus"] == "checkbox")
      {
        foreach ($e["items"] as $kk => $ee)
        {
          if ($ee["ertek"] == true)
          {
            $ertekek = $ertekek . "true, ";
          }
          else
          {
            $ertekek = $ertekek . "false, ";
          }
        }
      }
      if ($e["tipus"] == "boolean")
      {
        if ($e[$m] == true)
        {
          $ertekek = $ertekek . "true, ";
        }
        else
        {
          $ertekek = $ertekek . "false, ";
        }
      }
      if ($e[$m] != "")
      {
        if ($e["tipus"] == "integer")
          $ertekek = $ertekek . $e[$m] . ", ";
        if ($e["tipus"] == "float")
          $ertekek = $ertekek . $e[$m] . ", ";
      }
      if ($e["tipus"] == "varchar")
        $ertekek = $ertekek . "'" . $e[$m] . "', ";
      if ($e["tipus"] == "email")
        $ertekek = $ertekek . "'" . $e[$m] . "', ";
      if ($e["tipus"] == "datetime")
        $ertekek = $ertekek . "'" . $e[$m] . "', ";
      if ($e["tipus"] == "time")
        $ertekek = $ertekek . "'" . $e[$m] . "', ";
      if ($e["tipus"] == "date")
        $ertekek = $ertekek . "'" . $e[$m] . "', ";
      if ($e["tipus"] == "password")
        $ertekek = $ertekek . "'" . sha1($e[$m]) . "', ";
      if ($e["tipus"] == "longtext")
        $ertekek = $ertekek . "'" . $e[$m] . "', ";
      if ($e["tipus"] == "ckeditor")
        $ertekek = $ertekek . "'" . $e[$m] . "', ";
      if ($e["tipus"] == "telefon")
        $ertekek = $ertekek . "'" . $e[$m] . "', ";

      if (!(($e[$m] == "") and ( ($e["tipus"] == "integer") or ( $e["tipus"] == "float"))))
      {
        if ($e["tipus"] != "checkbox")
        {
          if (($e["tipus"] != "ellkod") and ( $e["tipus"] != "file"))
            $mezok = $mezok . $k . ", ";
        }
        else
        {
          foreach ($e["items"] as $k => $e)
          {
            $mezok = $mezok . $k . ", ";
          }
        }
      }
    }
  }
  $ertekek = substr($ertekek, 0, strlen($ertekek) - 2);
  $mezok = substr($mezok, 0, strlen($mezok) - 2);
  return "insert into " . $tabla . " (" . $mezok . ") VALUES (" . $ertekek . ");";
}

/** A megadott oldallal és nyelvvel hoz létre hivatkozást. Sem az oldalt, sem a nyelvet nem kötelező megadni. */
function getir_oldal($aktiv_hiv = "", $nyelv_hiv = "")
{
  global $aktiv;
  global $nyelv;
  if ($aktiv_hiv == "")
    $aktiv_hiv = $aktiv;
  if ($nyelv_hiv == "")
    $nyelv_hiv = $nyelv;
  $param["aktiv"] = $aktiv_hiv;
  $param["nyelv"] = $nyelv_hiv;
  return getir($param, "", true);
}

function getir($parameter = "", $ertek = "", $torol = false)
//$parameter lehet tömb is. Asszociatív, index érték párok fontosak.
{
  global $_SERVER;
  global $_GET;
  if ($parameter != "")
  {
    if (($torol == false) and ( $_SERVER["QUERY_STRING"] != ""))
    {
      $p = explode("&", $_SERVER["QUERY_STRING"]);
      foreach ($p as $k => $e)
      {
        unset($x);
        $x = explode("=", $e);
        if (isset($x[1]))
        {
          $g[$x[0]] = $x[1];
        }
        else
        {
          $g[$x[0]] = "";
        }

        $i = 2;
        while (isset($x[$i]))
        {
          $g[$x[0]].="=" . $x[$i];
          $i++;
        }
      }
    }


    //		$g=$_GET;
    /* 		foreach($_SERVER as $k=>$e)		
      {
      print(" $k=>$e<br>");
      } */
    //	print($_SERVER["QUERY_STRING"]."<br>");
    if (is_array($parameter))
    {
      foreach ($parameter as $k => $e)
      {
        $g[$k] = $e;
      }
    }
    else
    {
      $g[$parameter] = $ertek;
    }


    global $global_urlbarat;
    if ((isset($global_urlbarat)) and ( is_array($global_urlbarat)))
    {
      $v = keresobarat($g);
      $ki = $v["ki"];
      $g = $v["g"];
    }
    else
    {
      //print("ifsdiofisdfdiosdf");
      $ki = $_SERVER["PHP_SELF"] . "?";
      foreach ($g as $k => $e)
      {
        $ki.=$k . "=" . $e . "&";
      }
      $ki = substr($ki, 0, strlen($ki) - 1);
    }

    if (ellenoriz($ki) == "")
    {
      return $_SERVER["PHP_SELF"];
    }
    else
    {
      return $ki;
    }
  }
  else
  {
    if (ellenoriz($_SERVER["QUERY_STRING"]) == "")
    {
      return $_SERVER["PHP_SELF"];
    }
    else
    {

      global $global_urlbarat;
      if ((isset($global_urlbarat)) and ( is_array($global_urlbarat)))
      {
        $url = keresobarat($_SERVER["QUERY_STRING"]);
        return "/" . $url["ki"];
      }
      else
      {
        return $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"];
      }
    }
  }
}

/** Keresőbarát URL-t hoz létre */
function keresobarat($g)
{

  if (!is_array($g))
  {

    $g = explode("&", $g);



    foreach ($g as $k => $e)
    {
      $v = explode("=", $e);

      if (isset($v[1]))
      {
        $gy[$v[0]] = $v[1];
      }
      else
      {
        $gy[$v[0]] = "";
      }
    }

    $g = $gy;
  }


  global $global_urlbarat;
  $urlbarat = $global_urlbarat;

  foreach ($g as $k => $e)
  {
    $urlvaltozok[] = $k;
  }

  $db = count($urlbarat);
  $szamlalo = 0;
  $ki = "";
  $id = "";

  while ($szamlalo < $db)
  {
    $configsor = array_slice($urlbarat, $szamlalo, 1);

    foreach ($configsor[0] as $k => $e)
    {

      $urlbaratsor = $e;
      $id = explode("*", array_shift($urlbaratsor));
      $kulonbseg = array_intersect($urlbaratsor, $urlvaltozok);
      $idletezik = true;

      if (count($id) == 2)
      {

        if (!array_key_exists($id[1], $g))
          $idletezik = false;
      }

      if (count($urlbaratsor) == count($kulonbseg) and $idletezik)
      {

        $szamlalo = $db;
        $urlgyujto = array();
        foreach ($kulonbseg as $kulcs => $ertek)
        {

          if ($id[0] === $ertek)
          {
//                  $urlgyujto[] = str_replace(array("-"), " ", $g[$ertek]).$kulcs.$g[$id[1]];
            $urlgyujto[] = $g[$ertek] . $kulcs . $g[$id[1]];
          }
          else
          {
//                  $urlgyujto[] = str_replace(array("-"), " ", $g[$ertek]).$kulcs;
            $urlgyujto[] = $g[$ertek] . $kulcs;
          }

          unset($g[$ertek]);
        }


        if (count($id) == 2)
        {
          unset($g[$id[1]]);
        }

        $urlgyujto = implode("-", $urlgyujto);
        $ki[] = $urlgyujto;
        unset($g["aktiv"]);
        unset($g["nyelv"]);
      }
    }
    $szamlalo++;
  }

  if (is_array($ki))
  {
    $ki = implode("-", $ki);
  }


  $i = 0;
  foreach ($g as $k => $e)
  {
    if ($i == 0)
      $ki.="?";
    if ($i > 0)
    {
      $ki.="&amp;" . $k . "=" . $e;
    }
    else
    {
      $ki.=$k . "=" . $e;
    }
    $i++;
  }

  $visszatero["g"] = $g;
  $visszatero["ki"] = $ki;

  return $visszatero;
}

/** Megvizsgálja, hogy az angolabc és számok az elemei e a szüvegnek. */
function angolabc123($be)
{
  $mit = array("q", "w", "e", "r", "t", "z", "u", "i", "o", "p", "a", "s", "d", "f", "g", "h", "j", "k", "l", "y", "x", "c", "v", "b", "n", "m", "_", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
  $mire = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
  $ki = str_replace($mit, $mire, $be);
  $ki = strtolower($ki);
  return $ki == "";
}

function emailellenoriz($email)
{
  $isValid = true;
  $atIndex = strrpos($email, "@");
  if (is_bool($atIndex) && !$atIndex)
  {
    $isValid = false;
  }
  else
  {
    $domain = substr($email, $atIndex + 1);
    $local = substr($email, 0, $atIndex);
    $localLen = strlen($local);
    $domainLen = strlen($domain);
    if ($localLen < 1 || $localLen > 64)
    {
      // local part length exceeded
      $isValid = false;
    }
    else if ($domainLen < 1 || $domainLen > 255)
    {
      // domain part length exceeded
      $isValid = false;
    }
    else if ($local[0] == '.' || $local[$localLen - 1] == '.')
    {
      // local part starts or ends with '.'
      $isValid = false;
    }
    else if (preg_match('/\\.\\./', $local))
    {
      // local part has two consecutive dots
      $isValid = false;
    }
    else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
    {
      // character not valid in domain part
      $isValid = false;
    }
    else if (preg_match('/\\.\\./', $domain))
    {
      // domain part has two consecutive dots
      $isValid = false;
    }
    else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local)))
    {
      // character not valid in local part unless 
      // local part is quoted
      if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local)))
      {
        $isValid = false;
      }
    }
  }
  return $isValid;
}

function telefonellenoriz($text)
{
  return preg_match('/^(\+){0,1}(\([0-9]+\))?[0-9_\-\/]{6,}$/', str_replace(' ', '', trim($text)));
}

function darabol($szoveg, $hatarolo = "\s", $szovegjelzo = "'", $szovegjelzo2 = "")
{
  if ($szovegjelzo2 == "")
    $szovegjelzo2 = $szovegjelzo;
//   return preg_split("[\s,]*".$szovegjelzo."([^".$szovegjelzo."]+)".$szovegjelzo."[\s,]*|" . "[\s,]+/",$szoveg, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
//    return preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[\s,]+/", $szoveg, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
//   return preg_split("/[".$hatarolo.",]*\\".$szovegjelzo2."([^\\".$szovegjelzo2."]+)\\".$szovegjelzo2."[".$hatarolo.",]*|" . "[".$hatarolo.",]*".$szovegjelzo."([^".$szovegjelzo."]+)".$szovegjelzo."[".$hatarolo.",]*|" . "[".$hatarolo.",]+/", $szoveg, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
  return preg_split("/[" . $hatarolo . ",]*\\" . $szovegjelzo2 . "([^\\" . $szovegjelzo2 . "]+)\\" . $szovegjelzo2 . "[" . $hatarolo . ",]*|" . "[" . $hatarolo . ",]*" . $szovegjelzo . "([^" . $szovegjelzo . "]+)" . $szovegjelzo . "[" . $hatarolo . ",]*|" . "[" . $hatarolo . ",]+/", $szoveg, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
}

function rendez(&$tomb, $mezo, $irany, $kezdoindex = 0)
{
  $hossz = count($tomb);
  for ($a = $kezdoindex; $a < $hossz - 1; $a++)
  {
    $leg = $a;
    for ($b = $a + 1; $b < $hossz; $b++)
    {
      if (($irany and ( $tomb[$b]["$mezo"] > $tomb[$leg]["$mezo"])) or ( !$irany and ( $tomb[$b]["$mezo"] < $tomb[$leg]["$mezo"])))
      {
        $leg = $b;
      }
    }
    $cs = $tomb[$a];
    $tomb[$a] = $tomb[$leg];
    $tomb[$leg] = $cs;
  }
  //print($hossz);
}

function igazhamis($be)
{
  if ($be == true)
  {
    return "true";
  }
  else
  {
    return "false";
  }
}

function ekezetnelkul($be, $kisbetu = true)
{
  $mit = array("á", "é", "ő", "ú", "ü", "ö", "ó", "ű", "í", "Á", "É", "Ő", "Ú", "Ü", "Ö", "Ó", "Ű", "Í", " ", ",");
  $mire = array("a", "e", "o", "u", "u", "o", "o", "u", "i", "A", "E", "O", "U", "U", "O", "O", "U", "I", "_", "_");
  $ki = str_replace($mit, $mire, $be);
  if ($kisbetu == true)
    $ki = strtolower($ki);
  return $ki;
}

function fajlszamoz($be, $index = 1, $elvalaszto = "_")
{
  $reszek = explode(".", $be);
  $i = 0;
  $x = count($reszek);
  $ki = "";
  if ($index != "")
  {
    if ($x > 1)
    {
      for ($i = 0; $i < $x - 1; $i++)
      {
        $ki = $ki . $reszek[$i];
        if ($i < $x - 2)
          $ki = $ki . ".";
      }
      $ki = $ki . "$elvalaszto" . $index . "." . $reszek[$x - 1];
    }
    else
    {
      $ki = $be . "$elvalaszto" . $index;
    }
  }
  else
  {
    $ki = $be;
  }
  return $ki;
}

function ellenoriz(&$be)
{
  if (isset($be) == false)
  {
    $ki = "";
  }
  else
  {
    $ki = $be;
  }
  $ki = strip_tags($ki);
  $mit = array("'", "\"");
  $mire = array("&#39;", "&#34;");
  $ki = str_replace($mit, $mire, $ki);
  //$ki=addslashes($ki);
  $ki = stripslashes($ki);
  return $ki;
}

function sql_kompatibilis(&$be)
{
  if (isset($be) == false)
  {
    $ki = "";
  }
  else
  {
    $ki = $be;
  }
  //$mit=array("'","\\");
  //$mire=array("\\'","");
  //$ki=str_replace("'","\\'",$ki);
  //$ki=addslashes($ki);
  //$ki=stripslashes($ki);
  return addslashes(stripslashes($ki));
}

function ckeditor_to_sql(&$be)
{
  if (isset($be) == false)
  {
    $ki = "";
  }
  else
  {
    $ki = $be;
  }
  $mire = array("'", "\"");
  $mit = array("&#39;", "&#34;");
  $ki = str_replace($mit, $mire, $ki);
//  $mit=array("'","\\");
//  $mire=array("\\'","\\\\");
//  $ki=str_replace($mit,$mire,$ki);
  //$ki=str_replace("'","\\'",$ki);
  //addslashes
  $ki = addslashes(stripslashes($ki));
  //$ki=stripslashes($ki);
  return $ki;
}

function idezojelcsere(&$be)
{
  if (isset($be) == false)
  {
    $ki = "";
  }
  else
  {
    $ki = $be;
  }
  $mit = array("&#39;", "&#34;", "'", "\"");
  $mire = array("&rsquo;", "&quot;", "&rsquo;", "&quot;");
  $ki = str_replace($mit, $mire, $ki);
  //$ki=addslashes($ki);
  //$ki=stripslashes($ki);
  return $ki;
}

function otszam($be, $szam = 5)
{
  $c = $be;
  while (strlen($c) < $szam)
  {
    $c = "0" . $c;
  }
  return $c;
}

/** Hiba: 1- A fájl nem másolható. 2 - A kép mérete meghaladja a megadott értéket., 3-Hiba történt az átméretezés kzben. Hibás fájl, memórialimist, stb */
function kepmeretez($forrasfajl, $celfajl, $maxx, $maxy, $minoseg, $nagyit, $megapixel = 5.3, $canvas = false, $outtype = "jpg", $canvas_color = "0xFFFFFF", $transparency = 100, $bg_r = 255, $bg_g = 255, $bg_b = 255, $sharpen = 30)
{
  $size = getimagesize($forrasfajl);
  $size["mime"] = str_replace("jpeg", "jpg", $size["mime"]);
  //print_r($size);
  if (($nagyit == false) and ( $size[0] <= $maxx) and ( $size[1] <= $maxy))
  {
    if (strstr($size["mime"], $outtype) == false)
    {
      if ((strstr($size["mime"], "jpg")) or ( strstr($size["mime"], "jpeg")))
      {
        $forras = @imagecreatefromjpeg($forrasfajl);
      }
      if (strstr($size["mime"], "png"))
      {
        $forras = imagecreatetruecolor($size[0], $size[1]);
        $color = imagecolorallocate($forras, $bg_r, $bg_g, $bg_b);
        imagefill($forras, 0, 0, $color);
        imagealphablending($forras, true);
        imagecopy($forras, @imagecreatefrompng($forrasfajl), 0, 0, 0, 0, $size[0], $size[1]);
        //$forras = @imagecreatefrompng($forrasfajl);
      }
      if (strstr($size["mime"], "gif"))
      {
        $forras = imagecreatetruecolor($size[0], $size[1]);
        $color = imagecolorallocate($forras, $bg_r, $bg_g, $bg_b);
        imagefill($forras, 0, 0, $color);
        imagealphablending($forras, true);
        imagecopy($forras, @imagecreatefromgif($forrasfajl), 0, 0, 0, 0, $size[0], $size[1]);
      }
      if ($outtype == "jpg")
        $xx = imagejpeg($forras, $celfajl, $minoseg);
      if ($outtype == "png")
        $xx = imagepng($forras, $celfajl, $minoseg);
      if ($outtype == "gif")
        $xx = imagegif($forras, $celfajl);
      if ($xx == false)
      {
        return 3;
      }
      else
      {
        return 0;
      }
    }
    else
    if ($forrasfajl != $celfajl)
    {
      $xx = copy($forrasfajl, $celfajl);
      if ($xx == false)
        return 1;
    }
  }
  else
  {
    if (($size[0] * $size[1]) > $megapixel * 1000000)
    {
      return 2;
    }
    else
    {
      //$kit=strtolower((substr($forrasfajl, strlen($forrasfajl)-4)));
      if ((strstr($size["mime"], "jpg")) or ( strstr($size["mime"], "jpeg")))
      {
        $forras = @imagecreatefromjpeg($forrasfajl);
      }
      if (strstr($size["mime"], "png"))
      {
        $forras = imagecreatetruecolor($size[0], $size[1]);
        $color = imagecolorallocate($forras, $bg_r, $bg_g, $bg_b);
        imagefill($forras, 0, 0, $color);
        imagealphablending($forras, true);
        imagecopy($forras, @imagecreatefrompng($forrasfajl), 0, 0, 0, 0, $size[0], $size[1]);
        //$forras = @imagecreatefrompng($forrasfajl);
      }
      if (strstr($size["mime"], "gif"))
      {
        $forras = imagecreatetruecolor($size[0], $size[1]);
        $color = imagecolorallocate($forras, $bg_r, $bg_g, $bg_b);
        imagefill($forras, 0, 0, $color);
        imagealphablending($forras, true);
        imagecopy($forras, @imagecreatefromgif($forrasfajl), 0, 0, 0, 0, $size[0], $size[1]);

        //$forras = @imagecreatefromgif($forrasfajl);
      }
      $ujszel = $maxx;
      $ujmag = (($ujszel / $size[0]) * $size[1]);
      settype($ujmag, "integer");
      if ($ujmag > $maxy)
      {
        $ujmag = $maxy;
        $ujszel = (($ujmag / $size[1]) * $size[0]);
        settype($ujszel, "integer");
      }
      $cel = imagecreatetruecolor($ujszel, $ujmag);
      imagecopyresampled($cel, $forras, 0, 0, 0, 0, $ujszel, $ujmag, $size[0], $size[1]);

      if ($canvas)
      {
        $vegso = imagecreatetruecolor($maxx, $maxy);
        imagefilledrectangle($vegso, 0, 0, $maxx, $maxy, $canvas_color);
        $helyx = abs($ujszel - $maxx + 1) / 2;
        $helyy = abs($ujmag - $maxy + 1) / 2;
        settype($helyx, "integer");
        settype($helyy, "integer");
        imagecopymerge($vegso, $cel, $helyx, $helyy, 0, 0, $ujszel, $ujmag, $transparency);
        $cel = $vegso;
      }



      if ($sharpen != 0)
      {
        $sharpenMatrix = array
            (
            array(-1.2, -1, -1.2),
            array(-1, $sharpen, -1),
            array(-1.2, -1, -1.2)
        );

        // calculate the sharpen divisor
        $divisor = array_sum(array_map('array_sum', $sharpenMatrix));

        $offset = 0;

        // apply the matrix
        imageconvolution($cel, $sharpenMatrix, $divisor, $offset);
      }


      if ($outtype == "jpg")
        $xx = imagejpeg($cel, $celfajl, $minoseg);
      if ($outtype == "png")
        $xx = imagepng($cel, $celfajl, $minoseg);
      if ($outtype == "gif")
        $xx = imagegif($cel, $celfajl);
      if ($xx == false)
      {
        return 3;
      }
      else
      {
        return 0;
      }
    }
  }
}

/* * Átalakítja az sql ből származó időt szövegessé.
 *
 * @global type $nyelv
 * @param type $idopont Maga a dátum idő. Ha nincs megadva, az aktuális dátumot írja ki. Elfogadott formátum: unix időbélyeg, vagy 2011.02.02 02:02:02, ahol az elválasztó jel lehet . , - :
 * @param type $tipdatum Megjelenítendő dátum formátuma. 0-nincs, 1-PL: 2011.09.22, 2-PL: 2011. Szept. 22., 3-PL:2011. Szeptember 22.
 * @param type $tipnap
 * @param type $tipido Megjelenítendő idő formátuma. 0-nincs, 1-PL: 0:00:00, 2-PL: 0:00, 3-PL: 0 óra 00 percm 4-PL:0 óra 00 perc 00 másodperc, 5-PL:0 óra 00 perc 00 mp,
 * @param type $ma Beállítható, hogy ha mai dátum van, akkor a Ma szó jelenjen meg a dátum helyett. 0-nincs beállítva, 1-Ma szó jelenik meg, 2-nem jelenik meg a dátum, csak az idő.
 * @return string 
 */

function sql2ido($idopont = "", $tipdatum = 3, $tipnap = 1, $tipido = 1, $ma = 1)
{
  global $nyelv;

  $honapok[1]["hu"] = "Jan.";
  $honapok[2]["hu"] = "Febr.";
  $honapok[3]["hu"] = "Márc.";
  $honapok[4]["hu"] = "Ápr.";
  $honapok[5]["hu"] = "Máj.";
  $honapok[6]["hu"] = "Jún.";
  $honapok[7]["hu"] = "Júl.";
  $honapok[8]["hu"] = "Aug.";
  $honapok[9]["hu"] = "Szept.";
  $honapok[10]["hu"] = "Okt.";
  $honapok[11]["hu"] = "Nov.";
  $honapok[12]["hu"] = "Dec.";


  $honapok_h[1]["hu"] = "Január";
  $honapok_h[2]["hu"] = "Február";
  $honapok_h[3]["hu"] = "Március";
  $honapok_h[4]["hu"] = "Április";
  $honapok_h[5]["hu"] = "Május";
  $honapok_h[6]["hu"] = "Június";
  $honapok_h[7]["hu"] = "Július";
  $honapok_h[8]["hu"] = "Augusztus";
  $honapok_h[9]["hu"] = "Szeptember";
  $honapok_h[10]["hu"] = "Október";
  $honapok_h[11]["hu"] = "November";
  $honapok_h[12]["hu"] = "December";


  $napok[0]["hu"] = 'Vasárnap';
  $napok[1]["hu"] = 'Hétfő';
  $napok[2]["hu"] = 'Kedd';
  $napok[3]["hu"] = 'Szerda';
  $napok[4]["hu"] = 'Csütörtök';
  $napok[5]["hu"] = 'Péntek';
  $napok[6]["hu"] = 'Szombat';








  $honapok[1]["de"] = "Jan.";
  $honapok[2]["de"] = "Febr.";
  $honapok[3]["de"] = "März";
  $honapok[4]["de"] = "Apr.";
  $honapok[5]["de"] = "Mai";
  $honapok[6]["de"] = "Juni";
  $honapok[7]["de"] = "Juli";
  $honapok[8]["de"] = "Aug.";
  $honapok[9]["de"] = "Sept.";
  $honapok[10]["de"] = "Okt.";
  $honapok[11]["de"] = "Nov.";
  $honapok[12]["de"] = "Dez.";


  $honapok_h[1]["de"] = "Januar";
  $honapok_h[2]["de"] = "Februar";
  $honapok_h[3]["de"] = "März";
  $honapok_h[4]["de"] = "April";
  $honapok_h[5]["de"] = "Mai";
  $honapok_h[6]["de"] = "Juni";
  $honapok_h[7]["de"] = "Juli";
  $honapok_h[8]["de"] = "August";
  $honapok_h[9]["de"] = "September";
  $honapok_h[10]["de"] = "Oktober";
  $honapok_h[11]["de"] = "November";
  $honapok_h[12]["de"] = "Dezember";


  $napok[0]["de"] = 'Vasárnap';
  $napok[1]["de"] = 'Hétfő';
  $napok[2]["de"] = 'Kedd';
  $napok[3]["de"] = 'Szerda';
  $napok[4]["de"] = 'Csütörtök';
  $napok[5]["de"] = 'Péntek';
  $napok[6]["de"] = 'Szombat';



  $honapok[1]["en"] = "Jan.";
  $honapok[2]["en"] = "Febr.";
  $honapok[3]["en"] = "Mar.";
  $honapok[4]["en"] = "Apr.";
  $honapok[5]["en"] = "May";
  $honapok[6]["en"] = "June";
  $honapok[7]["en"] = "July";
  $honapok[8]["en"] = "Aug.";
  $honapok[9]["en"] = "Sept.";
  $honapok[10]["en"] = "Oct.";
  $honapok[11]["en"] = "Nov.";
  $honapok[12]["en"] = "Dec.";


  $honapok_h[1]["en"] = "January";
  $honapok_h[2]["en"] = "February";
  $honapok_h[3]["en"] = "March";
  $honapok_h[4]["en"] = "April";
  $honapok_h[5]["en"] = "May";
  $honapok_h[6]["en"] = "June";
  $honapok_h[7]["en"] = "July";
  $honapok_h[8]["en"] = "August";
  $honapok_h[9]["en"] = "September";
  $honapok_h[10]["en"] = "October";
  $honapok_h[11]["en"] = "November";
  $honapok_h[12]["en"] = "December";


  $napok[0]["en"] = 'Sunday';
  $napok[1]["en"] = 'Monday';
  $napok[2]["en"] = 'Tuesday';
  $napok[3]["en"] = 'Wednesday';
  $napok[4]["en"] = 'Thursday';
  $napok[5]["en"] = 'Friday';
  $napok[6]["en"] = 'Saturday';



  $honapok[1]["es"] = "Jan.";
  $honapok[2]["es"] = "Febr.";
  $honapok[3]["es"] = "Mar.";
  $honapok[4]["es"] = "Apr.";
  $honapok[5]["es"] = "Majo";
  $honapok[6]["es"] = "Jun.";
  $honapok[7]["es"] = "Jul.";
  $honapok[8]["es"] = "A&#365;g.";
  $honapok[9]["es"] = "Sept.";
  $honapok[10]["es"] = "Okt.";
  $honapok[11]["es"] = "Nov.";
  $honapok[12]["es"] = "Dec.";


  $honapok_h[1]["es"] = "Januaro";
  $honapok_h[2]["es"] = "Februaro";
  $honapok_h[3]["es"] = "Marto";
  $honapok_h[4]["es"] = "Aprilo";
  $honapok_h[5]["es"] = "Majo";
  $honapok_h[6]["es"] = "Junio";
  $honapok_h[7]["es"] = "Julio";
  $honapok_h[8]["es"] = "A&#365;gusto";
  $honapok_h[9]["es"] = "Septembro";
  $honapok_h[10]["es"] = "Oktobro";
  $honapok_h[11]["es"] = "Novembro";
  $honapok_h[12]["es"] = "Decembro";


  $napok[0]["es"] = 'Vasárnap';
  $napok[1]["es"] = 'Hétfő';
  $napok[2]["es"] = 'Kedd';
  $napok[3]["es"] = 'Szerda';
  $napok[4]["es"] = 'Csütörtök';
  $napok[5]["es"] = 'Péntek';
  $napok[6]["es"] = 'Szombat';




  $honapok[1]["il"] = "Jan.";
  $honapok[2]["il"] = "Febr.";
  $honapok[3]["il"] = "Mart.";
  $honapok[4]["il"] = "Apr.";
  $honapok[5]["il"] = "Maio";
  $honapok[6]["il"] = "Jun.";
  $honapok[7]["il"] = "Jul.";
  $honapok[8]["il"] = "Aug.";
  $honapok[9]["il"] = "Sept.";
  $honapok[10]["il"] = "Oct.";
  $honapok[11]["il"] = "Nov.";
  $honapok[12]["il"] = "Dec.";


  $honapok_h[1]["il"] = "Januario";
  $honapok_h[2]["il"] = "Februario";
  $honapok_h[3]["il"] = "Martio";
  $honapok_h[4]["il"] = "April";
  $honapok_h[5]["il"] = "Maio";
  $honapok_h[6]["il"] = "Junio";
  $honapok_h[7]["il"] = "Julio";
  $honapok_h[8]["il"] = "Augusto";
  $honapok_h[9]["il"] = "Septembre";
  $honapok_h[10]["il"] = "Octobre";
  $honapok_h[11]["il"] = "Novembre";
  $honapok_h[12]["il"] = "Decembre";


  $napok[0]["il"] = 'Vasárnap';
  $napok[1]["il"] = 'Hétfő';
  $napok[2]["il"] = 'Kedd';
  $napok[3]["il"] = 'Szerda';
  $napok[4]["il"] = 'Csütörtök';
  $napok[5]["il"] = 'Péntek';
  $napok[6]["il"] = 'Szombat';






  $forditas[1]["hu"] = "Ma";
  $forditas[1]["de"] = "Heute";
  $forditas[1]["en"] = "Today";
  $forditas[1]["es"] = "Hodia&#365;";
  $forditas[1]["il"] = "Hodie";


  //if ($idopont=="") $idopont=date("Y-m-d H:i:s");
  if ($idopont == "")
    return "";

  if (is_integer($idopont))
  {
    $uts = getdate($idopont);
    $dat[0] = $uts["year"];
    $dat[1] = $uts["mon"];
    $dat[2] = $uts["mday"];
    $dat[3] = $uts["hours"];
    $dat[4] = $uts["minutes"];
    $dat[5] = $uts["seconds"];
  }
  else
  {
    $dat = mb_split('[/.\:\ -]', $idopont);
    for ($i = 0; $i <= 6; $i++)
    {
      if (isset($dat[$i]))
        settype($dat[$i], "integer");
    }
  }


  $ki = "";
  if (($ma > 0) and ( date("Y") == $dat[0]) and ( date("m") == $dat[1]) and ( date("j") == $dat[2]))
  {
    if ($ma == 1)
    {
      if ($tipido > 0)
      {
        if ($tipido == 6)
        {
          if (!(($dat[3] == 0) and ( $dat[4] == 0)))
          {
            $ki = $ki . $forditas[1][$nyelv] . ",";
          }
          else
          {
            $ki = $ki . $forditas[1][$nyelv];
          }
        }
        else
        {
          $ki = $ki . $forditas[1][$nyelv] . ",";
        }
      }
      else
      {
        $ki = $ki . $forditas[1][$nyelv];
      }
    }
    /* 			if ($ma==2)
      {
      $k=mktime(0,0,0,$dat[1],$dat[2],$dat[0]);

      if ($tipido>0)
      {
      $ki=$ki.$napok[date("w")].",";
      }
      else
      {
      $ki=$ki.$napok[date("w")];
      }
      } */
  }
  else
  {
    if ($tipdatum == 1) //2009.12.09.
    {
      if ($nyelv == "hu")
      {
        $ki = $ki . $dat[0] . "." . otszam($dat[1], 2) . "." . otszam($dat[2], 2) . ".";
      }
      else
      {
        $ki = $ki . otszam($dat[2], 2) . "." . otszam($dat[1], 2) . "." . $dat[0] . ".";
      }
    }
    if ($tipdatum == 2) //2009. Dec. 9.
    {
      if ($nyelv == "hu")
      {
        $ki = $ki . $dat[0] . ". " . $honapok[$dat[1]][$nyelv] . " " . $dat[2] . ".";
      }
      else
      {
        $ki = $ki . $dat[2] . ". " . $honapok[$dat[1]][$nyelv] . " " . $dat[0] . ".";
      }
    }
    if ($tipdatum == 3) //2009. December 9.
    {
      if ($nyelv == "hu")
      {
        $ki = $ki . $dat[0] . ". " . $honapok_h[$dat[1]][$nyelv] . " " . $dat[2] . ".";
      }
      else
      {
        $ki = $ki . $dat[2] . ". " . $honapok_h[$dat[1]][$nyelv] . " " . $dat[0] . ".";
      }
    }
    if ($tipdatum == 4) //2009.12.09. napján
    {
      if ($nyelv == "hu")
      {
        $ki = $ki . $dat[0] . "." . otszam($dat[1], 2) . "." . otszam($dat[2], 2) . ". napján";
      }
      else
      {
        $ki = $ki . otszam($dat[2], 2) . "." . otszam($dat[1], 2) . "." . $dat[0] . ".";
      }
    }
  }

  if ($tipnap > 0)
  {
    if ($ki != "")
    {
      $ki = $ki . " ";
    }
    ///////////////////////////////
  }

  if ($tipido > 0)
  {
    if ($ki != "")
    {
      $ki = $ki . " ";
    }
    if ($tipido == 1)
    {
      $ki = $ki . $dat[3] . ":" . otszam($dat[4], 2) . ":" . otszam($dat[5], 2);
    }
    if ($tipido == 2)
    {
      $ki = $ki . $dat[3] . ":" . otszam($dat[4], 2);
    }
    if ($tipido == 3)
    {
      $ki = $ki . $dat[3] . " óra " . otszam($dat[4], 2) . " perc";
    }
    if ($tipido == 4)
    {
      $ki = $ki . $dat[3] . " óra " . otszam($dat[4], 2) . " perc " . otszam($dat[5], 2) . " másodperc";
    }
    if ($tipido == 5)
    {
      $ki = $ki . $dat[3] . " óra " . otszam($dat[4], 2) . " perc " . otszam($dat[5], 2) . " mp";
    }
    if ($tipido == 6)
    {
      if (!(($dat[3] == 0) and ( $dat[4] == 0)))
      {
        $ki = $ki . $dat[3] . ":" . otszam($dat[4], 2);
      }
    }
    if ($tipido == 7)
    {
      if (isset($dat[3]) and isset($dat[4]))
        $ki = $ki . $dat[3] . ":" . otszam($dat[4], 2) . " perckor";
    }
    if ($tipido == 8)
    {
      if (isset($dat[3]) and isset($dat[4]))
        $ki = $ki . otszam($dat[3], 2) . ":" . otszam($dat[4], 2);
    }
    if ($tipido == 9)
    {
      if (isset($dat[3]) and isset($dat[4]) and isset($dat[5]))
        $ki = $ki . otszam($dat[3], 2) . ":" . otszam($dat[4], 2) . ":" . otszam($dat[5], 2);
    }
  }


  return $ki;
  /* 		
    if (($dat[0]==1900) and ($dat[1]==1) and ($dat[2]==1))
    {
    if ($szoveg==true) return("Nincs megadva"); else return("");
    }
    else
    {
    if ($tip==1) return($dat[0].". ".$honapok[$dat[1]]." ".$dat[2].".");
    if ($tip==2)
    {
    return($dat[0].". ".$honapok[$dat[1]]." ".$dat[2].". &nbsp;".$dat[3]." óra ".$dat[4]." perc");
    }
    if ($tip==3)
    {
    return($dat[0].". ".$honapok[$dat[1]]." ".$dat[2].". ".$dat[3]." óra ".$dat[4]." perc");
    }
    if ($tip==4)
    {
    return($dat[3]." óra ".$dat[4]." perc");
    }

    if ($tip==5)
    {
    return(otszam($dat[3],2).":".otszam($dat[4],2));
    }

   */
}

function emailkukac($szoveg)
{
  $mire = array(" (kukac) ");
  $mit = array("@");
  return str_replace($mit, $mire, $szoveg);
}

function ckkiir($szoveg)
{
  $mire = array("'", "\"");
  $mit = array("&#39;", "&#34;");
  return str_replace($mit, $mire, $szoveg);
}

function time_2_timestamp($ora = 0, $perc = 0, $mp = 0)
{
  return $ora * 3600 + $perc * 60 + $mp;
}

function date_2_timestamp($ev = 0, $honap = 0, $nap = 0)
{
  return $ev * 31536000 + $honap * 2592000 + $nap * 86400;
}

function timestamp_2_sql_datetime($timestamp)
{
  return timestamp_2_datetime($timestamp, $formatum = "yy-mm-dd hh:mi:ss");
}

function timestamp_2_datetime($timestamp, $formatum = "yy.mm.dd hh:mi:ss")
{
  $formatum = str_replace("yy", "Y", $formatum);
  $formatum = str_replace("mm", "m", $formatum);
  $formatum = str_replace("dd", "d", $formatum);
  $formatum = str_replace("hh", "H", $formatum);
  $formatum = str_replace("mi", "i", $formatum);
  $formatum = str_replace("ss", "s", $formatum);
  return date($formatum, $timestamp);
}

function datetime_2_timestamp($ertek, $formatum = "yy.mm.dd hh:mi:ss")
{
  $ertek = datetime_ellenoriz($ertek, $formatum, true);
  if ($ertek != false)
  {
    $be = preg_split("/[\.\-\s\,\:]+/", trim($ertek));
    if (strstr($formatum, "yy") == false)
    {
      if (isset($be[0]) == false)
        $be[0] = 0;
      if (isset($be[1]) == false)
        $be[1] = 0;
      if (isset($be[2]) == false)
        $be[2] = 0;
      return $be[0] * 3600 + $be[1] * 60 + $be[2];
    }
    else
    {
      if (isset($be[3]) == false)
        $be[3] = 0;
      if (isset($be[4]) == false)
        $be[4] = 0;
      if (isset($be[5]) == false)
        $be[5] = 0;
      return mktime($be[3], $be[4], $be[5], $be[1], $be[2], $be[0]);
    }
  }
  else
  {
    return false;
  }
}

/** Kiszámolja két dátum különbségét. Visszaadja a különbség id?bélyegét. Ha a dátum értékek közül valamelyik nem helyes, akkor false értékkel tér vissza, ha jó akkor INTEGER típusú értékkel.
 * Visszatérési értéke 0, ha egyeznek. Negatív, ha a második érték kisebb, mint az els?. Pozitív ha a második érték nagyobb, mint az els?.
 * 
 * @param type $ertek1 Els? érték
 * @param type $ertek2 Második érték
 * @param type $formatum1 Els? érték formátuma, elhagyható
 * @param type $formatum2 Második érték formátuma, elhagyható
 * @return type INTEGER jó paraméterek esetén, hibás esetén BOOLEAN false
 */
function datetime_kulonbseg($ertek1, $ertek2, $formatum1 = "yy.mm.dd hh:mi:ss", $formatum2 = "yy.mm.dd hh:mi:ss")
{
  $ts1 = datetime_2_timestamp($ertek1, $formatum1);
  $ts2 = datetime_2_timestamp($ertek2, $formatum2);
  if (($ts1 != false) and ( $ts2 != false))
  {
    return $ts2 - $ts1;
  }
  else
  {
    return false;
  }
}

/** Egy dátum értékhez, ami megfelel a formátumnak, hozzáadja az időbélyeget. Visszatérési értéke időbélyeg. Hibás művelet esetén boolean, false.
 *
 * @param type $ertek A kiinduális dátum.
 * @param type $timestamp Időbélyeg. + és - is lehet
 * @param type $formatum A dátum formátuma, elhagyható, apaból yy.mm.dd hh:mi:ss
 * @return type 
 */
function datetime_osszead($ertek, $timestamp, $formatum = "yy.mm.dd hh:mi:ss")
{
  $ts1 = datetime_2_timestamp($ertek, $formatum);
  if ($ts1 != false)
  {
    return $ts1 + $timestamp;
  }
  else
  {
    return false;
  }
}

/** Dátum elelnőrzése. Ha a dátum jó, akkor visszaad egy mégjobbat a formátumkód alapján, ha nem, akkor false értékkel tér vissza. A formátumban az elválaszó jel . : - és szóköz lehet. A formátumkódban megadott elválasztó jelek csak a kimenetnél jelennek meg, a bemenetnél akár felváltva is használható.
 * @param $ertek a dátum, amit validálni kell.
 * @param $formatum pl:yy-mm-nn hh.mi.ss
 * @param $sql igaz esetén, ha jó a dátum, a yy-mm-nn hh:mi:ss formátumú dátussal tér vissza
 */
function datetime_ellenoriz($ertek, $formatum = "yy.mm.dd hh:mi:ss", $sql = false)
{
//  $be=explode($ertek);
  $form = preg_split("/[\.\-\s\,\:]+/", ($formatum));
  if ($sql)
  {
    if (strstr($formatum, "yy") == false)
    {
      $fff = "hh:mi:ss";
    }
    else
    {
      $fff = "yy-mm-dd hh:mi:ss";
    }
  }
  else
  {
    $fff = $formatum;
  }

  $form2 = preg_split("/[\.\-\s\,\:]+/", ($fff), -1, PREG_SPLIT_OFFSET_CAPTURE);
  $be = preg_split("/[\.\-\s\,\:]+/", trim($ertek));
  $valid = true;
  for ($i = 0; $i < count($form); $i++)
  {
    if (isset($be[$i]))
    {
      $be2[$form[$i]] = trim($be[$i]);
      $regi = $be2[$form[$i]];
      settype($be2[$form[$i]], "integer");
      settype($be2[$form[$i]], "string");
      if ($regi != $be2[$form[$i]])
        $valid = false;
      settype($be2[$form[$i]], "integer");
    }
    else
    {
      $be2[$form[$i]] = "";
    }
  }
  // print_r($be2);
  if (($valid == true) and ( (in_array("yy", $form)) or ( in_array("dd", $form)) or ( in_array("mm", $form))))
  {
    if ((isset($be2["yy"])) and ( $be2["yy"] != "") and ( isset($be2["mm"])) and ( $be2["mm"] != "") and ( isset($be2["dd"])) and ( $be2["dd"] != ""))
    {
      $valid = checkdate($be2["mm"], $be2["dd"], $be2["yy"]);
    }
    else
    {
      $valid = false;
    }
  }
  if (($valid == true) and ( (in_array("hh", $form)) or ( in_array("mi", $form)) or ( in_array("ss", $form))))
  {
    if ((isset($be2["ss"]) == false) or ( $be2["ss"] == ""))
      $be2["ss"] = 0;

    if ((isset($be2["hh"])) and ( isset($be2["mi"])) and ( isset($be2["ss"])))
    {
      if (checktime($be2["hh"], $be2["mi"], $be2["ss"]) == false)
      {
        if ((in_array("yy", $form)) or ( in_array("dd", $form)) or ( in_array("mm", $form)))
        {
          $be2["hh"] = 0;
          $be2["mi"] = 0;
          $be2["ss"] = 0;
        }
        else
        {
          $valid = false;
        }
      }
    }
  }
  if ($valid == false)
  {
    return false;
  }
  else
  {
    foreach ($be2 as $k => $e)
    {
      if ($k != "yy")
        $be2[$k] = otszam($e, 2);
    }
    $ki = "";
    /* print_r_2($be2);
      print($fff."<br>"); */
    for ($i = 0; $i < count($form2); $i++)
    {
      if ($i + 1 < count($form2))
      {
        if (isset($be2[$form2[$i][0]]))
        {
          $ki.=$be2[$form2[$i][0]] . $fff[$form2[$i + 1][1] - 1];
        }
      }
      else
      {
        if (isset($be2[$form2[$i][0]]))
        {
          $ki.=$be2[$form2[$i][0]];
        }
      }
    }
    return $ki;
  }
}

function enter_to_paragraph($szoveg, $htmltag = "p", $class = "", $uressortorol = false)
{
  $szoveg = str_replace("\n", "\r", $szoveg);
  $szoveg = str_replace("\r\r", "\r", $szoveg);
  $tmp = explode("\r", $szoveg);
  $kimenet = "";
  foreach ($tmp as $k => $e)
  {
    $e = trim($e);
    if ($uressortorol == false)
      if ($e == "")
        $e = "&nbsp;";
    if ($class != "")
    {
      if (($e != "") or ( $uressortorol == false))
      {
        $kimenet.="<" . $htmltag . " class='" . $class . "'>" . $e . "</" . $htmltag . ">";
      }
    }
    else
    {
      if (($e != "") or ( $uressortorol == false))
      {
        $kimenet.="<" . $htmltag . ">" . $e . "</" . $htmltag . ">";
      }
    }
  }
  return $kimenet;
}

/** Ellenőrzi az idő helyességét. Kimenete igaz, ha jó, különben hamis. */
function checktime($o, $p, $mp = 0)
{
  $oo = $o;
  $pp = $p;
  $mmp = $mp;
  settype($o, "integer");
  settype($p, "integer");
  settype($mp, "integer");
  settype($o, "string");
  settype($p, "string");
  settype($mp, "string");
  if ($oo != $o)
    return false;
  if ($pp != $p)
    return false;
  if ($mp != $mmp)
    return false;
  settype($o, "integer");
  settype($p, "integer");
  settype($mp, "integer");
  return ((($o >= 0) and ( $o <= 23)) and ( ($p >= 0) and ( $p <= 59)) and ( ($mp >= 0) and ( $mp <= 59)));
}

/** Ha túl hosszú a szöveg, akkor megvágja egy megadott helyen. HTML re nem alkalmazható.
 *
 * @param type $ertek A szöveg, amit vágni kell.
 * @param type $max A leghosszabb, megengedett szöveg.
 * @param type $max_vagashelye A vágás helye % ban, 0-100-ig, alapérték 70
 * @param type $max_vagas A vágás helyére kerülő söveg alapérték: [...]
 * @return type string
 */
function ertek_vagas($ertek, $max = 0, $max_vagashelye = 70, $max_vagas = " [...] ")
{
  if ($max > 0)
  {
    if (strlen($ertek) > $max)
    {
      $hely = ($max * $max_vagashelye) / 100;
      settype($hely, "integer");
      return mb_substr($ertek, 0, $hely) . $max_vagas . mb_substr($ertek, strlen($ertek) - ($max - $hely));
    }
    else
    {
      return $ertek;
    }
  }
  else
  {
    return $ertek;
  }
}

function print_r_2($r, $print = true)
{
  if ($print)
  {
    print(str_replace(" ", "&nbsp;", str_replace("\n", "<br>", print_r($r, true))));
  }
  else
  {
    return str_replace(" ", "&nbsp;", str_replace("\n", "<br>", print_r($r, true)));
  }
}

/** Átalakítja a tomb[]=array("ertek"=>..., "cimke"=>....) struktúrát kimenet[ertek]=cimke struktórára */
function ertek_cimke_to_associativ(&$tomb)
{
  $kimenet = "";
  if (is_array($tomb))
  {
    foreach ($tomb as $k => $e)
    {
      $kimenet[$e["ertek"]] = $e["cimke"];
    }
  }
  //print_r_2($kimenet);
  return $kimenet;
}

/** Átalakítja a tomb[]=array("ertek"=>..., "cimke"=>....) struktúrát kimenet[ertek]=cimke struktórára */
function ertek_cimke_to_associativ_checkbox(&$tomb)
{
  $kimenet = "";
  if (is_array($tomb))
  {
    foreach ($tomb as $k => $e)
    {
      $kimenet[] = array("kulcsertek" => $e["ertek"], "cimke" => $e["cimke"]);
    }
  }
  //print_r_2($kimenet);
  return $kimenet;
}

function kep_meret_validal($file, $minx = "", $miny = "", $maxx = "", $maxy = "")
{
  $size = getimagesize($file);
  $x = $size[0];
  $y = $size[1];
  if ((is_integer($minx)) and ( is_integer($miny)))
  {
    if (($minx > $x) or ( $miny > $y))
      return -1;
  }
  if (!(is_integer($minx)) and ( is_integer($miny)))
  {
    if ($miny > $y)
      return -1;
  }
  if ((is_integer($minx)) and ( !is_integer($miny)))
  {
    if ($minx > $y)
      return -1;
  }


  if ((is_integer($maxx)) and ( is_integer($maxy)))
  {
    if (($maxx < $x) or ( $maxy < $y))
      return 1;
  }
  if (!(is_integer($maxx)) and ( is_integer($maxy)))
  {
    if ($maxy < $y)
      return 1;
  }
  if ((is_integer($maxx)) and ( !is_integer($maxy)))
  {
    if ($maxx < $y)
      return 1;
  }

  return 0;
}

function text2url($str)
{
  $message = trim(preg_replace("/[^a-z0-9äßíéáuoőűúöüó \-]/i", "", mb_strtolower($str)));
  $message = preg_replace("'\s+'", ' ', $message);
  $mit = array('ó', 'ö', 'ő', 'ü', 'ú', 'ű', 'é', 'á', 'í', ' ', 'ß', 'ä');
  $mire = array('o', 'o', 'o', 'u', 'u', 'u', 'e', 'a', 'i', '-', 'ss', 'a');
  return mb_strtolower(str_ireplace($mit, $mire, $message));
}

function csoport_osszefuz($tomb, $elvalaszto = ", ", $maxhossz = 0)
{
  if (is_array($tomb))
  {
    //$tomb=array_unique($tomb);
    //$tomb=array_filter($tomb);
    $ki = "";
    if ($maxhossz == 0)
    {
      foreach ($tomb as $k => $e)
      {
        $ki.=$e . $elvalaszto;
      }
    }
    else
    {
      foreach ($tomb as $k => $e)
      {
        if ((strlen($ki) + strlen($e)) > $maxhossz)
          break;
        $ki.=$e . $elvalaszto;
      }
    }
    return substr($ki, 0, strlen($ki) - strlen($elvalaszto));
  }
  else
  {
    return "";
  }
}

function adoazon_valid($adoa)
{
  return preg_match("([0-9]{8}\-[0-9]\-[0-9]{2})", $adoa);
}

function strip_html($be, $uressor = true)
{
  if ($uressor)
  {
    return str_replace("&nbsp;", "", preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", strip_tags(preg_replace("(<head>(\s*|.*)*</head>)", "", $be))));
  }
  else
  {
    return str_replace("&nbsp;", "", strip_tags(preg_replace("(<head>(\s*|.*)*</head>)", "", $be)));
  }
}

function kulcsszo_szuro($szoveg)
{
  $szoveg = str_replace(array("#34", "#39"), array("", ""), $szoveg);
  $szoveg = trim(preg_replace("/[^a-z0-9äßíéáuoőűúöüó ]/i", " ", mb_strtolower($szoveg)));
  $szoveg = preg_replace("/\s+/", " ", $szoveg);
  $szoveg = str_replace(" ", ", ", $szoveg);
  return $szoveg;
}

function ini_to_file($assoc_arr, $path = "", $has_sections = FALSE)
{
  $content = "";
  if ($has_sections)
  {
    foreach ($assoc_arr as $key => $elem)
    {
      $content .= "[" . $key . "]\n";
      foreach ($elem as $key2 => $elem2)
      {
        if (is_array($elem2))
        {
          for ($i = 0; $i < count($elem2); $i++)
          {
            $content .= $key2 . "[] = \"" . $elem2[$i] . "\"\n";
          }
        }
        else if ($elem2 == "")
          $content .= $key2 . " = \n";
        else
          $content .= $key2 . " = \"" . $elem2 . "\"\n";
      }
    }
  }
  else
  {
    foreach ($assoc_arr as $key => $elem)
    {
      if (is_array($elem))
      {
        for ($i = 0; $i < count($elem); $i++)
        {
          $content .= $key . "[] = \"" . $elem[$i] . "\"\n";
        }
      }
      else if ($elem == "")
        $content .= $key . " = \n";
      else
        $content .= $key . " = \"" . $elem . "\"\n";
    }
  }

  if ($path == "")
    return $content;

  if (!$handle = fopen($path, 'w'))
  {
    return false;
  }
  if (!fwrite($handle, $content))
  {
    return false;
  }
  fclose($handle);
  return true;
}

function aaz($szoveg, $mondatkezdo = false)
{
  //$szoveg=trim($szoveg);
  if ($szoveg != "")
  {
    if (mb_strstr("15euioőúüóöaéáűí", mb_strtolower(mb_substr($szoveg, 0, 1))))
    {
      if ($mondatkezdo)
      {
        return "Az";
      }
      else
      {
        return "az";
      }
    }
    else
    {
      if ($mondatkezdo)
      {
        return "A";
      }
      else
      {
        return "a";
      }
    }
  }
  else
  {
    return "";
  }
}

function file_extension($file_nev)
{
  if ($file_nev != "")
  {
    $d = explode(".", $file_nev);
    if (count($d > 0))
    {
      return ellenoriz($d[count($d) - 1]);
    }
  }
  return "";
}

/** Ha a szöveg üres, akkor nbsp jelenik meg. */
function nbsp_szoveg($szoveg)
{
  if ($szoveg != "")
    return $szoveg;
  else
    return "&nbsp;";
}

/** Ha a szám 0 vagy üres, akkor nbsp jelenik meg. */
function nbsp_szam($szam)
{
  if (($szam != "") and ( $szam != 0))
    return $szam;
  else
    return "&nbsp;";
}

function mappa_letrehoz($mappanev, $jog)
{
  if ((substr($mappanev, strlen($mappanev) - 1)) == "/")
    $mappanev = substr($mappanev, 0, strlen($mappanev) - 1);
  if (is_dir($mappanev) == false)
  {
    $d = explode("/", $mappanev);
    $mappa = "";
    for ($i = 0; $i < count($d); $i++)
    {
      $mappa.=$d[$i];
      if (is_dir($mappa) == false)
      {
        mkdir($mappa, $jog);
      }
      $mappa.="/";
    }
  }
  return is_dir($mappanev);
}

function ertek_cimke_2_if($tomb, $mezonev)
{
  $kimenet = "";
  $i = 0;
  foreach ($tomb as $k => $e)
  {
    $kimenet.="if(" . $mezonev . "=" . $e["ertek"] . ",'" . $e["cimke"] . "',";
    $i++;
  }
  $kimenet.="NULL";
  foreach ($tomb as $k => $e)
  {
    $kimenet.=")";
  }
  $kimenet.=" as " . $mezonev;
  return $kimenet;
}

function gettorol($mezo, $ertek)
{
  global $_GET;
  global $_SERVER;
  if ((isset($_GET[$mezo])) and ( $_GET[$mezo] == $ertek))
  {
    unset($_GET[$mezo]);
    //print($mezo);
    $_SERVER["QUERY_STRING"] = str_replace($mezo . "=" . $ertek . "&", "", $_SERVER["QUERY_STRING"]);
    $_SERVER["QUERY_STRING"] = str_replace($mezo . "=" . $ertek, "", $_SERVER["QUERY_STRING"]);
    $_SERVER["QUERY_STRING"] = str_replace("&=&", "", $_SERVER["QUERY_STRING"]);
    //print($_SERVER["QUERY_STRING"]);
  }
}

function startsWith($haystack, $needle)
{
  // search backwards starting from haystack length characters from the end
  return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle)
{
  // search forward starting from end minus needle length characters
  return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

function sutik($szoveg)
{
  if (!isset($_COOKIE["cookies"]))
  {
    print("<div id='cookies'>".$szoveg."<div onclick='cookies();'>Értem.</div></div>");
  }
}

?>