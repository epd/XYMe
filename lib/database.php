<?php
/** 
 * @file
 * Establish database connection via PDO
 */

class Database {

  /**
   * Implements connect().
   */
  public static function connect() {
    require __DIR__ . '/../config.php';

    // Try to connect via PDO to MySQL database
    try {
      return new PDO('mysql:host=localhost;dbname='.$config['db'], $config['user'], $config['pass']);
    }

    // Else, error occurred
    catch (Exception $e) {
      echo "Error: " . $e->getMessage();
    }
  }
}
