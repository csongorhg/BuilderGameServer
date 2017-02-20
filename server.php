<?php

require_once("_sql.php");

namespace BuilderGameServer
{

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

    /**
     *
     * @var messageTypes
     */
    private $commandIN = null;

    /**
     *
     * @var messageTypes
     */
    private $commandOUT = null;

    function __construct()
    {
      $this->sqla = new sql("buildergameserver");
    }

    /**
     * Elvégzi a felhasználónév és jelszó ellenőrzését.
     * Amennyiben a felhasználónév foglalt, AUTHUSERNAMEFAILED üzenetet állít be. Ha a felhasználónév és jelszó páros nem jó, akkor AUTHFAILED. Egyébként AUTHACCEPT
     * @param type $user
     * @param type $pass
     * @return boolean
     */
    private function authentication($user, $pass)
    {
      if ((!isset($_POST["user"]) || (!isset($_POST["password"]))))
      {
        return FALSE;
      }
      $this->userID = $this->sqla->egy_mezo_kiolvas("select id from ".server::tableUser." where name='" . ellenoriz($_POST["user"]) . "' and password='" . sha1(ellenoriz($_POST["password"])) . "'");
      if ($this->userID == "")
      {
        if ($this->$this->sqla->egy_mezo_kiolvas("select id from ".server::tableUser." where name='" . ellenoriz($_POST["user"]) . "'"))
        {
          $this->commandOUT = messageTypes::AUTHUSERNAMEFAILED;
        }
        else
        {
          //user felvitele
          $this->sqla->query("insert into ".server::tableUser." (name, password) values ('" . ellenoriz($_POST["user"]) . "', '" . sha1(ellenoriz($_POST["password"])) . "')", true);
          $this->userID = $this->sqla->mysql_insert_id;
          $this->commandOUT = messageTypes::AUTHACCEPT;
        }
      }
      $this->commandOUT = messageTypes::AUTHACCEPT;
      return $this->userID != "";
    }

    private function authenticationError()
    {
      return "message=" . messageTypes::AUTHFAILED;
    }

    private function authenticationAccept()
    {
      return "message=" . messageTypes::AUTHACCEPT;
    }

    public function clearOldActiveUsers(){
      
    }
    
    public function clearOldRegUsers(){
      
    }
    
    
    /**
     * Elvégzi az adatbázisműveleteket.
     */
    public function process()
    {
      
    }

    /**
     * @return String Description A kimenetre kerülő string.
     */
    public function generateMessage()
    {
      switch ($this->commandOUT)
      {
        case messageTypes::ATTACKACCEPT:

          break;

        default:
          return null;
      }
    }

  }

}