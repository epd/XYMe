<?php
/**
 * @file
 * Install script.
 *
 * Use this to setup the schema for Users, Groups, and Permissions.
 */
require __DIR__ . '/lib/database.php';

class Install {

  /**
   * Implements setup().
   */
  static public function setup() {
    // Connect to the database
    $dbconn = self::connect();

    // Create our tables from schema
    self::createTables($dbconn);

    // Create administrative account
    self::createGroups($dbconn);
    self::addAdmin($dbconn);
  }

  /**
   * Implements connect().
   */
  static private function connect() {
    return Database::connect();
  }

  /**
   * Implements createTables().
   *
   * @param $dbconn
   * The database connection resource.
   */
  static private function createTables($dbconn) {

    // Create our groups table
    $groups_stmt = $dbconn->prepare('create table groups (group_id int(11) not
      null auto_increment primary key, name varchar(255))');

    // Creat our users table
    $users_stmt = $dbconn->prepare('create table users (user_id int(11) not null
      auto_increment primary key, group_id int(11), username varchar(255),
      password varchar(255), foreign key (group_id) references groups(group_id))
    ');

    // Create our roles table
    $roles_stmt = $dbconn->prepare('create table roles (role_id int(11) not
      null auto_increment primary key, name varchar(255))');

    // Create table that holds relation between groups and roles
    $gr_map_stmt = $dbconn->prepare('create table group_role_maps (group_id
      int(11), role_id int(11), foreign key (group_id) references groups
      (group_id), foreign key (role_id) references roles(role_id), primary key
      (group_id, role_id))');

    // Go ahead and execute our statements
    $groups_stmt->execute();
    $users_stmt->execute();
    $roles_stmt->execute();
    $gr_map_stmt->execute();
  }

  /**
   * Implements createGroups().
   *
   * @param $dbconn
   * The database connection resource.
   */
  static private function createGroups($dbconn) {
    $groups_stmt = $dbconn->prepare('insert into groups values(null, :name)');

    $groups_stmt->execute(array(':name' => 'admin'));
    $groups_stmt->execute(array(':name' => 'user'));
  }

  /**
   * Implements addAdmin().
   *
   * @param $dbconn
   * The database connection resource.
   */
  static private function addAdmin($dbconn) {
    require __DIR__ . '/config.php';

    // Admin account credentials
    $user = 'admin';
    $pass = 'admin';

    // Salt the password
    $salted = sha1($config['salt'] . $pass);

    // Insert our admin account
    $admin_stmt = $dbconn->prepare('insert into users values(null, :groupid,
      :username, :password)');
    $admin_stmt->execute(array(
      ':groupid' => 1,
      ':username' => $user,
      ':password' => $salted,
    ));
  }
}

// Perform the install
Install::setup();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Installation</title>
  </head>
  <body>
    <h1>Installation Complete</h1>
    <p><a href="/">Back to home</a></p>
  </body>
</html>
