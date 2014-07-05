<?php

/**
 * Globale Konfiguration
 *
 * Die globale Konfiguration für den Datenbankzugriff.
 * Diese Klasse wird verwendet für den Import und die API.
 *
 * @author Luca Keidel
 * @author David Bohn
 */

namespace API;

/**
 * Konfigurationsklasse
 */
class Config{

  /**
   * @var string $db_host Adresse der Datenbank
   */
  public static $db_host = 'localhost';

  /**
   * @var string $db_user Nutzername für die Datenbank
   */
  public static $db_user = 'me';

  /**
   * @var string $db_pass Passwort für den Datenbanknutzer
   */
  public static $db_pass = 'me';

  /**
   * @var string $db_name Name der Datenbank
   */
  public static $db_name = 'bundesliga';


}
?>
