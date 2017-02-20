<?php

namespace BuilderGameServer
{
  require_once("_sql.php");

  class messageTypes
  {

    /**
     * KLIENS: A kliens küld HELLO üzeneteket 5 másodpercenként. A szerver visszaküldi az online játékosok listáját. Ha 20 mp-ig nem küld valaki hello üzenetet, akkor offline lesz.
     */
    const HELLO = 10;

    /**
     * SZERVER: A szerver felhasználó listát küld.
     */
    const LST = 11;

    /**
     * SZERVER: A server nem küld felhasználó listát, mert a kapcsolat a HELLO üzenetek kimaradása miatt elveszett.
     */
    const CONNECTIONLOST = 12;

    /**
     * KLIENS: A kliens megtámad egy másik, online klienst.
     */
    const ATTACK = 20;

    /**
     * SZERVER: A harc elfogadva. Egyik fél sem harcolt eddig.
     */
    const ATTACKACCEPT = 21;

    /**
     * SZERVER: A harc elutasítva. Az egyik fél harcba lépett a felkérés és a hello üzenet közt.
     */
    const ATTACKREFUSE = 22;

    /**
     * KLIENS: A kliens offline állapotba álítja magát, azaz mégsem akar harcolni.
     */
    const DISCONNECT = 30;

    /**
     * SZERVER: Szétkapcsolódás sikeres.
     */
    const DISCONNECTACK = 31;

    /**
     * KLIENS: A kliens elküldi kapcsolódási kérelmét, saját adatait a támadáshoz és védekezéshez használt erőkkel.
     */
    const CONNECT = 40;

    /**
     * SZERVER: Kapcsolódás sikeres, adatokat a szerver feldolgozta.
     */
    const CONNECTACK = 41;

    /**
     * KLIENS: A kliens lekéri a módosult adatokat.
     */
    const GETDATA = 50;

    /**
     * SZERVER: A szerver az adatokat elküldte.
     */
    const DATA = 51;

    /**
     * KLIENS: A kliens nyugtázza a megkapott adatokat. A nyugta válaszaként a szerver tájékoztatja a klienst, hogy a másik fél is nyugtázta-e. Amennyiben igen, feldolgozza a kapott adatokat. Ha nem, párszor újra nyugtáz.
     */
    const GETDATAACK = 52;

    /**
     * SZERVER: A másik felhasználó letöltötte az adatokat.
     */
    const ANOTHERUSERACK = 53;

    /**
     * SZERVER: A másik felhasználó a megendegett időn belül nem töltötte le az adatokat.
     */
    const BACKTRACK = 54;

    /**
     * SZERVER: A szerver visszautasítja a kapcsolatot, jelszó hiba.
     */
    const AUTHFAILED = 60;

    /**
     * SZERVER: A szerver elfogadja a kapcsolatot. Minden adatküldés előtt ellenőrzi a hozzáférést.
     */
    const AUTHACCEPT = 61;

    /**
     * SZERVER: A szerveren a jelenlegi felhasználónév foglalt, és a jelszó nem egyezik. A felhasználónév nem használható a regisztráció lejáratáig.
     */
    const AUTHUSERNAMEFAILED = 62;

    /**
     * Az üzenet nem megfelelő.
     */
    const ERROR = 404;

    /**
     * Ismeretlen üzenet jött.
     */
    const UNKNOWN = 101010;

  }

  class server
  {

    /**
     *
     * @var \sql
     */
    private $sqla;
    private $userID = null;

    const tableUser = "user";
    const tableData = "data";
    const tableBattle = "battle";
    const tableOnline = "online";

    /**
     *
     * @var messageTypes
     */
    private $messageIN = null;

    /**
     *
     * @var messageTypes
     */
    private $messageOUT = null;

    function __construct()
    {
      $this->sqla = new \sql("buildergameserver");
      $this->messageIN = ellenoriz($_POST["message"]);
    }

    /**
     * Elvégzi a felhasználónév és jelszó ellenőrzését.
     * Amennyiben a felhasználónév foglalt, AUTHUSERNAMEFAILED üzenetet állít be. Ha a felhasználónév és jelszó páros nem jó, akkor AUTHFAILED. Egyébként AUTHACCEPT
     * @param type $user
     * @param type $pass
     * @return messageTypes
     */
    private function authentication($user, $pass)
    {
      if ((!isset($_POST["user"]) || (!isset($_POST["password"]))))
      {
        return messageTypes::AUTHFAILED;
      }
      $this->userID = $this->sqla->egy_mezo_kiolvas("select id from " . server::tableUser . " where name='" . $user . "' and password='" . sha1($pass) . "'");
      if ($this->userID == "")
      {
        if ($this->sqla->egy_mezo_kiolvas("select id from " . server::tableUser . " where name='" . $user . "'"))
        {
          return messageTypes::AUTHUSERNAMEFAILED;
        }
        else
        {
          //user felvitele
          $this->sqla->query("insert into " . server::tableUser . " (name, password, lastlogintime) values ('" . $user . "', '" . sha1($pass) . "', now())", true);
          $this->userID = $this->sqla->mysql_insert_id;
          //$this->commandOUT = messageTypes::AUTHACCEPT;
          return messageTypes::AUTHACCEPT;
        }
      }
      $this->sqla->query("update " . server::tableUser . " set lastlogintime=now() where id='".$this->userID."';");
      return messageTypes::AUTHACCEPT;
    }

    /**
     * return messageTypes
     */
    private function hello()
    {
      if ($this->sqla->egy_mezo_kiolvas("select userid from " . server::tableOnline . " where userid='" . $this->userID . "';") != "")
      {
        $this->sqla->query("update " . server::tableOnline . " set lasthellotime = now() where userid='".$this->userID."'");
        return messageTypes::LST;
      }
      else
      {
        return messageTypes::ERROR;
      }
    }

    /**
     * return messageTypes
     */
    private function connect()
    {
      if (!isset($_POST["offense_soldier"]))
      {
        return messageTypes::ERROR;
      }
      if (!isset($_POST["defense_soldier"]))
      {
        return messageTypes::ERROR;
      }
      if ($this->sqla->egy_mezo_kiolvas("select userid from " . server::tableOnline . " where userid='" . $this->userID . "';") == "")
      {
        $this->sqla->query("insert into " . self::tableData . " (soldier) values (" . ellenoriz($_POST["offense_soldier"]) . ");", true);
        $offensedata = $this->sqla->mysql_insert_id;

        $this->sqla->query("insert into " . self::tableData . " (soldier) values (" . ellenoriz($_POST["defense_soldier"]) . ");", true);
        $defensedata = $this->sqla->mysql_insert_id;

        $this->sqla->query("insert into " . self::tableOnline . " (userid, lasthellotime, offensedata, defensedata) values (" . $this->userID . ", now(), " . $offensedata . ", " . $defensedata . " );", true);
        return messageTypes::CONNECTACK;
      }
      else
      {
        return messageTypes::ERROR;
      }
    }

    public function clearOldActiveUsers()
    {
      
    }

    public function clearOldRegUsers()
    {
      
    }

    /**
     * Elvégzi az adatbázisműveleteket.
     */
    public function process()
    {

      switch ($this->messageOUT = $this->authentication(ellenoriz($_POST["user"]), ellenoriz($_POST["password"])))
      {
        case messageTypes::AUTHACCEPT:
          switch ($this->messageIN)
          {
            case messageTypes::HELLO:
              $this->messageOUT = $this->hello();
              break;
            case messageTypes::ATTACK:
              break;
            case messageTypes::CONNECT:
              $this->messageOUT = $this->connect();
              break;
            case messageTypes::DISCONNECT:
              break;
            case messageTypes::GETDATA:
              break;
            default:
              $this->messageOUT = messageTypes::UNKNOWN;
          }
          break;
      }
    }

    /**
     * return Array
     */
    private function lst()
    {
      $a = $this->sqla->osszes_rekord_kiolvas("select userid, name from " . server::tableOnline . " inner join " . server::tableUser . " on " . server::tableOnline . ".userid = " . server::tableUser . ".id");
      $ret = null;
      if ($a != "")
      {
        foreach ($a as $k => $v)
        {
          $ret["user" . $v["userid"]] = $v["name"];
        }
      }
      return $ret;
    }

    /**
     * @return Array Description A kimenetre kerülő tömb.
     */
    public function generateMessage()
    {
      $out["message"] = $this->messageOUT;
      switch ($this->messageOUT)
      {
        case messageTypes::LST:
          if (($a = $this->lst()) != "")
          {
            foreach ($a as $key => $value)
            {
              $out[$key] = $value;
            }
          }
          break;
      }
      return $out;
    }

    public function testpage()
    {
      ?>
      <form method="post" action="index.php">
        message<input type="text" name="message" value="<?php print(ellenoriz($_POST["message"])); ?>"/><br>
        user<input type="text" name="user" value="<?php print(ellenoriz($_POST["user"])); ?>"/><br>
        password<input type="text" name="password" value="<?php print(ellenoriz($_POST["password"])); ?>"/><br>
        offense_soldier<input type="text" name="offense_soldier" value="<?php print(ellenoriz($_POST["offense_soldier"])); ?>"/><br>
        defense_soldier<input type="text" name="defense_soldier" value="<?php print(ellenoriz($_POST["defense_soldier"])); ?>"/><br>
        <input type="submit" value="post"/>
      </form>
      <?php
    }

  }

}