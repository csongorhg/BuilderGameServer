<?php
//SQL illesztő
//2013.01.05.
//Tüske Balázs (www.programkeszites.hu)
global $hitelesites;
if (!isset($hitelesites) or ($hitelesites!="OK")) { header("Location: index.php");exit;}

require_once("_os_objektum.php");

class sql extends os_objektum
{
  private static $db=0;
  /** 0 - semmi, 1 - csak a hibák, 2 - elsődleges kulcs is, 3 - minden */
	public $logtable=1;
	
  /** 0 - semmi, 1 - csak a hibák, 2 - elsődleges kulcs is, 3 - minden */
	public $monitor=1;

  /** 0 - semmi, 1 - csak a hibák, 2 - elsődleges kulcs is, 3 - minden */
	public $email=1;
  
  /** True esetén elküldi a legfontosabb környezeti változók értékét is.*/
  public $kornyezeti_valtozo_kiir=true;
  
	
  public $email_to="";
  public $email_from="valaki@freemail.hu";
  public $email_from_name="Objektum";
  public $email_subject="SQL napló";
  /** Email objektum konfiguráció*/
  public $email_config="";
  
	public $sql_tabla="sql_hibak";
	public $aktiv;
	public $kapcsolat;
	public $nyelv;
	public $adatbazis;
	public $engine="InnoDB";
	public $name;
	public $affected_rows;
	public $err;
	public $errno;
	public $num_rows;
	public $mysql_insert_id;	
	public $sorok; //ugyanaz, mint az előző
  public $utolsolekerdezes="";
  public $idegenkulcs_hiba=false;
  /** A leghosszabb utasítás hossza nem haladhatja meg az itt feltüntetett értéket. */
  public $sql_max=50000;


	public function kornyezetfelepit()
	{
		global $aktiv;
		global $nyelv;
		global $kapcsolat;
		global $adatbazis;
		$this->aktiv=$aktiv;
		$this->nyelv=$nyelv;
		$this->kapcsolat=$kapcsolat;
		$this->adatbazis=$adatbazis;
	}	

	
//////////////////////////////////////////////////////////////////////////////////////	

	public function install()
	{
		$lekerdezes="create table ".$this->sql_tabla."   
	(`azon` INTEGER AUTO_INCREMENT,
	oldal varchar(255),
	obj varchar(255),
  regdat DATETIME,
  errno INTEGER,
  err LONGTEXT,
  rows INTEGER,
  affected INTEGER,
  query LONGTEXT,
  PRIMARY KEY (`azon`))
			ENGINE = ".$this->engine.";";
			mysql_query($lekerdezes,$this->kapcsolat);
			return mysql_errno();
	}


//////////////////////////////////////////////////////////////////////////////////////	

	function __construct($nev="",$kornyezetfelepit=true, $sqlconfig=true)
	{
    parent::__construct($nev, $kornyezetfelepit);
		$this->name=$nev;
		if ($kornyezetfelepit)
		{
			$this->kornyezetfelepit();
		}
		if ($sqlconfig)
		{
			$hitelesites="OK";
			if (is_file("_sql_config.php")) include("_sql_config.php");
		}
	}	

  /** Visszaadja az eddig lefuttatott lekérdezések számát*/
  public function stat_db()
  {
    return self::$db;
  }
  
  /** A lekérdezés eredményeként visszaadott első rekordot adja eredményül. Ha nincs ilyen, akkor üres szöveget. 
   * @param $lek A lekérdezés, sqlben
   * @param $pontosan_egy Alapból igaz, igaz esetén csak akkor ad vissza eredményt, ha pontosan egy rekord van.
   */
  public function egy_rekord_kiolvas($lek, $pontosan_egy=true, $mysql_result_type=1)
  {
    $tabla=$this->query($lek);
    if ((($this->sorok==1) and ($pontosan_egy)) or (($this->sorok>=1) and ($pontosan_egy==false)))
    {
      return mysql_fetch_array($tabla,$mysql_result_type);
    }
    else
    {
      return "";
    }
  }


  /** A lekérdezés eredményeként visszaadott első rekordot adja eredményül. Ha nincs ilyen, akkor üres szöveget. 
   * @param $lek A lekérdezés, sqlben
   * @param $pontosan_egy Alapból igaz, igaz esetén csak akkor ad vissza eredményt, ha pontosan egy rekord van.
   */
  public function egy_mezo_kiolvas($lek, $pontosan_egy=true, $mysql_result_type=2)
  {
    $tabla=$this->query($lek);
    if ((($this->sorok==1) and ($pontosan_egy)) or (($this->sorok>=1) and ($pontosan_egy==false)))
    {
      $x=mysql_fetch_array($tabla,$mysql_result_type);
      return $x[0];
    }
    else
    {
      return "";
    }
  }  
  
  
  /** Eredményül a kiovasott teljes táblát adja, két dimenziós tömbben.
   *
   * @param type $lek Lekérdezés
   * @param type $mysql_result_type
   * @return type 
   */
  public function osszes_rekord_kiolvas($lek, $mysql_result_type=1)
  {
    $tabla=$this->query($lek);
    $tmp="";
    for ($i=1;$i<=$this->sorok;$i++)
    {
      $tmp[]=mysql_fetch_array($tabla,$mysql_result_type);
    }
    return $tmp;
  }  
  
  /** Eredményül a kiovasott teljes táblát adja, két dimenziós tömbben. A bemeneti tömböt cím szerint kell átadni.
   *
   * @param type $lek Lekérdezés
   * @param type $tmp A tömb, amit kibővít.
   * @param type $mysql_result_type
   * @return type 
   */
  public function osszes_rekord_kiolvas_cimszerinti($lek, &$tmp ,$mysql_result_type=1)
  {
    $tabla=$this->query($lek);
    for ($i=1;$i<=$this->sorok;$i++)
    {
      $tmp[]=mysql_fetch_array($tabla,$mysql_result_type);
    }
  }    
  
	public function query($lek,$id=false)
	{
    self::$db++;
    $this->utolsolekerdezes=$lek;
    if ($this->kapcsolat!="")
    {
      $x=mysql_query($lek, $this->kapcsolat);
      if ($id==true) $this->mysql_insert_id=mysql_insert_id($this->kapcsolat);
      $this->errno=mysql_errno($this->kapcsolat);
      $this->err=mysql_error($this->kapcsolat);
      if ($this->errno==0)
      {
        $this->affected_rows=mysql_affected_rows($this->kapcsolat);
  			//print($x."<br>");
        if ($x!=1)
        {
          $this->num_rows=mysql_num_rows($x);
        }
        else
        {
          $this->num_rows=-1;
        }

      }
      else
      {
        $this->affected_rows=-1;
        $this->num_rows=-1;
      }
      $hibakod="nincs";
      $this->sorok=$this->num_rows;
      if (($this->logtable==3) or (($this->logtable==1) and ($this->errno!=0) and ($this->errno!=1062) and ($this->errno!=1451)) or (($this->logtable==2) and ($this->errno!=0)))
      {
        mysql_query("insert into ".$this->sql_tabla." (oldal, obj, regdat, errno, err, rows, query) values 
        ('".$this->aktiv."','".$this->name."',now(),".$this->errno.",'".idezojelcsere($this->err)."',".$this->affected_rows.", '".idezojelcsere($lek)."')",$this->kapcsolat);
        if (mysql_error($this->kapcsolat))
        {
          print("<p>Hiba az adatbázis kapcsolattal!</p>");
          print("<p>".mysql_error($this->kapcsolat)."<p>");
          print("<p>".mysql_errno($this->kapcsolat)."<p>");
        }
        else
        {
          $hibakod=mysql_insert_id($this->kapcsolat);
        }
      }

      if (($this->monitor==3) or (($this->monitor==1) and ($this->errno!=0) and ($this->errno!=1062) and ($this->errno!=1451)) or (($this->monitor==2) and ($this->errno!=0)))
      {
        print ("<p>".$this->errno."<br>");
        print ($this->err."</p>");
        print ("<p><b>".$this->name."</b> ".$lek."</p>");
        if ($this->errno==0)
        {
          print ("<p>Érintett sorok: ".$this->affected_rows."<br>");
          print ("Eredmény sorok: ".$this->num_rows."</p>");
        }
      }		
      if (($this->email==3) or (($this->email==1) and ($this->errno!=0) and ($this->errno!=1062) and ($this->errno!=1451)) or (($this->email==2) and ($this->errno!=0)))
      {
        if ($this->email_to!="")
        {
          require_once("_email.php");
          $eml=new email();
          $eml->message="";
          $eml->to=$this->email_to;
          $eml->from=$this->email_from;
          $eml->type = "mix";          
          //$eml->from_name=$this->email_from_name;
          $eml->message="";
          if (($this->logtable==3) or (($this->logtable==1) and ($this->errno!=0) and ($this->errno!=1062) and ($this->errno!=1451)) or (($this->logtable==2) and ($this->errno!=0)))
          {
            $eml->message.="<h1>SQL napló (hibanapló azonosító: ".$hibakod.")</h1>\n";          
            $eml->subject=$this->email_subject." (hibanapló azonosító: ".$hibakod.")";
          }
          else
          {
            $eml->message.="<h1>SQL napló</h1>\n";
            $eml->subject=$this->email_subject;
          }
          $eml->message.="<p><b>".$this->errno."</b><br>\n";
          $eml->message.=str_replace(array("near '","' at line"),array("near '<br><b>","</b><br>' at line"),$this->err)."</p>\n";
          //print_r_2($this->name);
          $eml->message.="<p><b>".$this->name."</b><br>".$lek."</p>\n";
          if ($this->errno==0)
          {
            $eml->message.="<p>Érintett sorok: ".$this->affected_rows."<br>\n";
            $eml->message.="Eredmény sorok: ".$this->num_rows."</p>\n";
          }
          if ($this->kornyezeti_valtozo_kiir)
          {
            $eml->message.="<h1>PHP környezet változói</h1>\n";
            $eml->message.="<h2>GET</h2>\n";
            $eml->message.="<p>".print_r_2($_GET,false)."</p>";
            $eml->message.="<h2>POST</h2>\n";
            $eml->message.="<p>".print_r_2($_POST,false)."</p>";
            $eml->message.="<h2>COOKIE</h2>\n";
            $eml->message.="<p>".print_r_2($_COOKIE,false)."</p>";
            if (isset($_SESSION))
            {
              $eml->message.="<h2>SESSION</h2>\n";
              $eml->message.="<p>".print_r_2($_SESSION,false)."</p>";
            }
            $eml->message.="<h2>SERVER</h2>\n";
            $eml->message.="<p>".str_replace(";", "; ", print_r_2($_SERVER,false))."</p>";
            $eml->message.="<h2>FILES</h2>\n";
            $eml->message.="<p>".print_r_2($_FILES,false)."</p>";
          }
          if (is_array($this->email_config))
          {
            foreach($this->email_config as $k=>$e)
            {
              $eml->$k=$e;
            }
          }          
          $eml->kuld();
        }
      }
      if ($this->errno==1451) $this->idegenkulcs_hiba=true;
      return $x;
    }
    else
    {
      print("Nincs kapcsolat az adatbázis szerverrel.");
      return false;
    }
	}

  /** Tömb tartalmát insert into utasítással a memóriába tölti. 
   *
   * @param type $tomb A tömb, amit fel kell tölteni. $table["rekord"]["mezonev"]=ertek;
   * @param type $konfiguracio $konfiguracio["..."]=array("idezojelbe"=>true, "null"=>true) A bemeneti tömbhöz tartozó konfiguráció.
   */
  public function tomb_sql(&$tomb,$konfiguracio,$tabla, $replace=false)
  {
    if ((is_array($tomb)) and (is_array($konfiguracio)))
    {
      $mezok="";
      foreach($konfiguracio as $k=>$e)
      {
        $mezok=$mezok.$k.", ";
      }
      $mezok=substr($mezok,0,strlen($mezok)-2);
      $lek="";
      foreach($tomb as $k=>$e)
      {
        $l="(";
        foreach($konfiguracio as $kk=>$ee)
        {
          if ($ee["idezojelbe"])
          {
//            $l=$l."'".$e[$kk]."', ";
            if ((isset($ee["null"])) and ($ee["null"]==true))
            {
              if ($tomb[$k][$kk]=="")
              {
                $l=$l."null, ";
              }
              else
              {
                $l=$l."'".$tomb[$k][$kk]."', ";
              }
            }
            else
            {
              $l=$l."'".$tomb[$k][$kk]."', ";
            }
          }
          else
          {
            if ($e[$kk]=="")
            {
              $l=$l."null, ";
            }
            else
            {
              $l=$l.$e[$kk].", ";
            }
          }
        }
        $l=substr($l,0,strlen($l)-2);
        $l=$l.")";
        if (strlen($lek.$l)>=$this->sql_max)
        {
          if ($replace)
          {
            $this->query("replace ".$tabla."(".$mezok.") values ".substr($lek,0,strlen($lek)-2));
          }
          else
          {
            $this->query("insert into ".$tabla."(".$mezok.") values ".substr($lek,0,strlen($lek)-2));
          }
          $lek="";
        }
        $lek=$lek.$l.", ";
      }
      if ($lek!="")
      {
        if ($replace)
        {
          $this->query("replace ".$tabla."(".$mezok.") values ".substr($lek,0,strlen($lek)-2));
        }
        else
        {
          $this->query("insert into ".$tabla."(".$mezok.") values ".substr($lek,0,strlen($lek)-2));
        }
      }    
      return true;
    }
    else
    {
      return false;
    }
  }
  
  public function sql_hibak_tablazat_kiir()
  {
    require_once("_tablazat.php");
    
    $tablazat["oldal"]=array( "tipus"=>"varchar", "cim"=>"Oldal");
    $tablazat["regdat"]=array("rendezett"=>true, "rendezes_sorrend"=>"desc", "tipus"=>"datetime", "cim"=>"Dátum");
    $tablazat["errno"]=array("tipus"=>"integer", "cim"=>"Hibakód");
    $tablazat["err"]=array("tipus"=>"varchar", "cim"=>"Hiba");
    $tablazat["query"]=array("tipus"=>"varchar", "cim"=>"Lekérdezés","max"=>2500);
    $tbla=new tablazat("sql_tabla",$tablazat);
    $tbla->lekerdezes="select * from ".$this->sql_tabla." ";
    $tbla->cim="SQL hibanapló";
    $tbla->szoveg_nincsrekord="Jelenleg a hibanapló üres.";
    $tbla->letrehoz();
    
  }
  
  
  
  /** Eredményül adja asszociatív tömbben egy tábla mezőinek listáját.*/
  public function tabla_mezok($tabla)
  {
    return $this->osszes_rekord_kiolvas("SHOW FIELDS FROM ".$tabla);
  }

  
  
  /** Több rekord másolható egyszerre egy másik táblába. 
   *
   * @param type $forras A forrás tábla neve
   * @param type $cel A cél tábla neve
   * @param type $szures A where záradék utáni rész, szűrés. A where szót nem kell odaírni, automatikusan generálódik
   * @param type $feluliras Ebben az esetben ON DUPLICATE KEY UPDATE az összes mezőt frissíti, ha az elsődleges kulcs egyezik, vagyis nem vihető fel rekord.
   * @param type $cel_forras_alap True esetén az alapértelmezett mezőlista a cél táblóból generálódik, false esetén a forrásból. Ebből vonódik ki a $mezo_kimarad.
   * @param type $mezo_kimarad Azoknak a mezőknek a listája, amik kimaradnak a másolásból. Vesszővel elválasztva.
   * @return type Visszaadott értéke az érintett sorok száma, -1 esetén a másolás a mezők hiánya miatt szakadt meg
   */
  public function rekord_klon($forras, $cel, $szures="", $feluliras=true, $cel_forras_alap=false ,$mezo_kimarad="")
  {
    if ($cel_forras_alap)
    {
      $m=$this->tabla_mezok($cel);
    }
    else
    {
      $m=$this->tabla_mezok($forras);
    }
    foreach($m as $k=>$e)
    {
      $mezok[$e["Field"]]=$e;
    }
    if ($mezo_kimarad!="")
    {
      $x=explode(",", $mezo_kimarad);
      if (is_array($x))
      {
        foreach ($x as $k=>$e)
        {
          unset($mezok[trim($e)]);
        }
      }
    }
    if (is_array($mezok))
    {
      {
        $me="";
        foreach ($mezok as $k=>$e)
        {
          $me.=$k.", ";
        }
        $me=substr($me,0,strlen($me)-2);
        $lekerdezes="INSERT INTO ".$cel." (".$me.") SELECT ".$me." FROM ".$forras." ";
        if ($szures!="")
        {
          $lekerdezes.=" where ".$szures." ";
        }
        if ($feluliras)
        {
          $fe="";
          foreach ($mezok as $k=>$e)
          {
            $fe.=$cel.".".$k."=".$forras.".".$k.", ";
          }
          $fe=substr($fe,0,strlen($fe)-2);
          $lekerdezes.=" ON DUPLICATE KEY UPDATE ".$fe;
        }
        $this->query($lekerdezes);
        return $this->affected_rows;
      }
    }
    else
    {
      return -1;
    }
  }

  
  function tomb_2_sql_insert_egyrekord($tomb, $tabla, $nullertek=false)
  {
    $values="";
    $mezok="";
    foreach($tomb as $k=>$e)
    {
      if (($nullertek) and ($e==""))
      {
        $values.="null, ";
      }
      else
      {
        $values.="'".sql_kompatibilis($e)."', ";        
      }
      $mezok.=$k.", ";
    }
    $values=substr($values, 0, strlen($values)-2);
    $mezok=substr($mezok, 0, strlen($mezok)-2);
    $ki="insert into ".$tabla." (".$mezok.") values (".$values.");";
    $this->query($ki,true);
  }

  
  function tomb_2_sql_update_egyrekord($tomb, $tabla, $kulcs, $kulcsmezo="id", $nullertek=false)
  {
    $mezok="";
    foreach($tomb as $k=>$e)
    {
      if (($nullertek) and ($e==""))
      {
        $mezok.=$k."=null, ";
      }
      else
      {
        $mezok.=$k."="."'".sql_kompatibilis($e)."', ";
      }
      
    }
    $mezok=substr($mezok, 0, strlen($mezok)-2);
    $ki="update ".$tabla." set ".$mezok." where ".$kulcsmezo."='".$kulcs."';";
    $this->query($ki);
      //$this->query($ki);
  }
  
  
}

?>