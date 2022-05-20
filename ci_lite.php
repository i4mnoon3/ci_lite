<?php

// config
$db['default'] = array(
  'hostname' => 'localhost',
  'username' => 'root',
  'password' => '',
  'database' => 'test',
);

// html_helper
function anchor($url, $text) {
  return "<a href='$url'>$text</a>";
}
function redirect($url) {
  header("Location: $url");
  die();
}

// form_helper
function form_open($action, $attributes = '') {
  return "<form method='post' action='$action'>";
}
function form_close() {
  return "</form>";
}
function form_input($name, $value = '', $attributes = '') {
  return "<input type='text' name='$name' value='$value' $attributes>";
}
function form_password($name, $value, $attributes = '') {
  return "<input type='password' name='$name' value='$value' $attributes>";
}
function form_textrea($name, $value, $attributes = '') {
  return "<textarea name='$name' $attributes>$value</textarea>";
}
function form_submit($value, $attributes = '') {
  return "<input type='submit' value='$value' $attributes>";
}

// libraries
class Db {
  function __construct() {
    global $db;
    list($hostname, $username, $password, $database) = array(
      $db['default']['hostname'],
      $db['default']['username'],
      $db['default']['password'],
      $db['default']['database'],
    );
    $this->con = new mysqli($hostname, $username, $password, $database);
    $this->select = '*';
  }
  function select($select) {
    $this->select = $select;
    return $this;
  }
  function from($table_name) {
    $this->table_name = $table_name;
    return $this;
  }
  function get($table_name) {
    $query = '';
    $query .= 'select ' . $this->select;
    if ($table_name) {
      $this->table_name = $table_name;
    }
    $query .= ' from ' . $this->table_name;
    $this->result = $this->con->query($query);
    return $this;
  }
  function get_where($table_name, $where = null) {
    $query = '';
    $query .= 'select ' . $this->select;
    if ($table_name) {
      $this->table_name = $table_name;
    }
    $query .= ' from ' . $this->table_name;
    if ($where) {
      $query .= ' where ';
      $w = '';
      $i = 0;
      foreach ($where as $key => $value) {
        if ($i++ > 0) {
          $w .= ', ';
        }
        $val = '';
        if (is_string($value)) {
          $val .= "'" . $value . "'";
        }
        $w .= $key . ' = ' . $val;
      }
      $query .= $w;
    }
    $this->result = $this->con->query($query);
    return $this;
  }
  function row() {
    $data = null;
    if ($row = mysqli_fetch_assoc($this->result)) {
      $data = (object)$row;
    }
    return $data;
  }
  function result() {
    $data = array();
    while ($row = mysqli_fetch_assoc($this->result)) {
      $data[] = (object)$row;
    }
    return $data;
  }
  function insert($table_name, $data) {
    $this->table_name = $table_name;
    $i = 0;
    $col = '';
    $val = '';
    foreach ($data as $key => $value) {
      if ($i++ > 0) {
        $col .= ', ';
        $val .= ', ';
      }
      $col .= $key;
      if (is_string($value)) {
        $val .= "'$value'";
      } else {
        $val .= $value;
      }
    }
    $query = '';
    $query .= 'insert into ' . $table_name . '(' . $col . ')';
    $query .= ' values (' . $val . ')';
    return $this->con->query($query);
  }
  function update($table_name, $data, $where = null) {
    $this->table_name = $table_name;
    $i = 0;
    $col = '';
    foreach ($data as $key => $value) {
      if ($i++ > 0) {
        $col .= ', ';
      }
      $val = '';
      if (is_string($value)) {
        $val .= "'$value'";
      } else {
        $val .= $value;
      }
      $col .= $key . ' = ' . $val;
    }
    $query = '';
    $query .= 'update ' . $table_name;
    $query .= ' set ' . $col;
    if ($where) {
      $query .= ' where ';
      $w = '';
      $i = 0;
      foreach ($where as $key => $value) {
        if ($i++ > 0) {
          $w .= ', ';
        }
        $val = '';
        if (is_string($value)) {
          $val .= "'" . $value . "'";
        }
        $w .= $key . ' = ' . $val;
      }
      $query .= $w;
    }
    return $this->con->query($query);
  }
  function delete($table_name, $where = null) {
    $this->table_name = $table_name;
    $query = '';
    $query .= 'delete from ' . $table_name;
    if ($where) {
      $query .= ' where ';
      $w = '';
      $i = 0;
      foreach ($where as $key => $value) {
        if ($i++ > 0) {
          $w .= ', ';
        }
        $val = '';
        if (is_string($value)) {
          $val .= "'" . $value . "'";
        }
        $w .= $key . ' = ' . $val;
      }
      $query .= $w;
    }
    return $this->con->query($query);
  }
}

// models
class CI_Model {
  function __construct() {
    $this->db = new Db();
  }
}
