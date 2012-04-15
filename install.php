<?php
require 'database.php';

class Install {
  function setup() {
    $dbconn = self::connect();
    self::createTables($dbconn);
    self::createGroups($dbconn);
    self::addAdmin($dbconn);
  }

  private function connect() {
    return Database::connect();
  }

  private function createTables($dbconn) {
    $groups_stmt = $dbconn->prepare('create table groups (group_id int(11) not
      null auto_increment primary key, name varchar(255))');
    $users_stmt = $dbconn->prepare('create table users (user_id int(11) not null
      auto_increment primary key, group_id int(11), username varchar(255),
      password varchar(255), foreign key (group_id) references groups(group_id))
    ');
    $roles_stmt = $dbconn->prepare('create table roles (role_id int(11) not
      null auto_increment primary key, name varchar(255))');
    $gr_map_stmt = $dbconn->prepare('create table group_role_maps (group_id
      int(11), role_id int(11), foreign key (group_id) references groups
      (group_id), foreign key (role_id) references roles(role_id), primary key
      (group_id, role_id))');

    $groups_stmt->execute();
    $users_stmt->execute();
    $roles_stmt->execute();
    $gr_map_stmt->execute();
  }

  private function createGroups($dbconn) {
    $groups_stmt = $dbconn->prepare('insert into groups values(null, :name)');

    $groups_stmt->execute(array(':name' => 'admin'));
    $groups_stmt->execute(array(':name' => 'user'));
  }

  private function addAdmin($dbconn) {
    require 'config.php';
    $user = 'admin';
    $pass = 'admin';

    $salted = sha1($config['salt'] . $pass);

    $admin_stmt = $dbconn->prepare('insert into users values(null, :groupid,
      :username, :password)');
    $admin_stmt->execute(array(
      ':groupid' => 1,
      ':username' => $user,
      ':password' => $salted,
    ));
  }
}

Install::setup();