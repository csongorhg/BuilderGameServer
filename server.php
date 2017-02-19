<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of server
 *
 * @author tuskeb
 */

namespace BuilderGameServer
{

  class messageTypes
  {

    /**
     * A kliens küld HELLO üzeneteket 5 másodpercenként. A szerver visszaküldi az online játékosok listáját. Ha 20 mp-ig nem küld valaki hello üzenetet, akkor offline lesz.
     */
    const HELLO = 10;

    /**
     * A kliens megtámad egy másik, online klienst.
     */
    const ATTACK = 20;

    /**
     * A kliens offline állapotba álítja magát, azaz mégsem akar harcolni.
     */
    const DISCONNECT = 30;

    /**
     * A kliens elküldi kapcsolódási kérelmét, saját adatait a támadáshoz és védekezéshez használt erőkkel.
     */
    const CONNECT = 40;

    /**
     * A kliens lekéri a módosult adatokat.
     */
    const GETDATA = 50;

    /**
     * A kliens nyugtázza a megkapott adatokat. A nyugta válaszaként a szerver tájékoztatja a klienst, hogy a másik fél is nyugtázta-e. Amennyiben igen, feldolgozza a kapott adatokat. Ha nem, párszor újra nyugtáz.
     */
    const GETDATAACK = 51;

    /**
     * A szerver visszautasítja a kapcsolatot, jelszó vagy felhasználónév hiba.
     */
    const AUTHFAILED = 60;

    /**
     * A szerver elfogadja a kapcsolatot. Minden adatküldés előtt ellenőrzi a hozzáférést.
     */
    const AUTHACCEPT = 60;

  }

  class server
  {

    private $sqla;
    /**
     *
     * @var type messageType
     */  
    private $command;

    function __construct()
    {
      $this->sqla = new sql("buildergameserver");
    }

    public function authentication($user, $pass)
    {
      return true;
    }
    
    

  }

}