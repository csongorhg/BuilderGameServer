<?php

//Az összes objektum ősobjektuma
//2016.5.20.
//Tüske Balázs (www.programkeszites.hu)
global $hitelesites;
if (!isset($hitelesites) or ( $hitelesites != "OK"))
{
  header("Location: index.php");
  exit;
}

class os_objektum {

  protected $name = "";
  protected $aktiv = "";
  protected $nyelv = "";
  protected $nyelvek = "";
  protected $type = "os";
  protected static $hash = 0;
  protected $felhnev = "";
  /**
   *
   * @var phpuser $usr
   */
  protected $usr = "";

  public function __construct($name = "", $kornyezetfelepit = true)
  {
    self::$hash++;
    if ($kornyezetfelepit)
    {
      $this->kornyezetfelepit();
    }
    if ($name != "")
    {
      $this->name = $name;
    }
    else
    {
      $this->name = md5(self::$hash);
    }
  }

  public function kornyezetfelepit()
  {
    global $aktiv;
    global $nyelv;
    global $usr;
    global $nyelvek;
    if ($this->type != "user")
    {
      if (isset($usr) and ( $usr->getType() == "user"))
      {
        $this->felhnev = $usr->felhnev();
        $this->usr = $usr;
      }
    }
    $this->aktiv = $aktiv;
    $this->nyelv = $nyelv;
    $this->nyelvek = $nyelvek;
  }

  public function toString()
  {
    return $this->name;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getHashCode()
  {
    return self::$hash;
  }

}

?>