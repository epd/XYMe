<?php
class Database {
  public static function connect() {
    require 'config.php';
    try {
      return new PDO('mysql:host=localhost;dbname='.$config['db'], $config['user'], $config['pass']);
    }
    catch (Exception $e) {
      echo "Error: " . $e->getMessage();
    }
  }
}