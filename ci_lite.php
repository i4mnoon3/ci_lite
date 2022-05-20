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
function form_textarea($name, $value, $attributes = '') {
  return "<textarea name='$name' $attributes>$value</textarea>";
}
function form_submit($value, $attributes = '') {
  return "<input type='submit' value='$value' $attributes>";
}
function form_dropdown($name, $options, $value, $attributes) {
  $o = '';
  foreach ($options as $k => $v) {
    $selected = $k == $value ? 'selected' : '';
    $o .= "<option value='$k' $selected>$v</option>";
  }
  return "<select name='$name' $attributes>$o</select>";
}
function post($key) {
  if (!empty($_POST[$key])) {
    return $_POST[$key];
  }
  return '';
}

// session_helper
function session($name, $value = '') {
  if ($value) {
    $_SESSION[$name] = $value;
  }
  if (isset($_SESSION[$name])) {
    return $_SESSION[$name];
  }
  return null;
}

// date_helper
function now($format = 'Y-m-d H:i:s') {
  $date = new DateTime();
  return $date->format($format);
}

// other helper
function print_pre($a) {
  echo '<pre>';
  print_r($a);
  echo '</pre>';
}
function output_enable_profiler($enable) {
  global $enable_profiler;
  $enable_profiler = $enable;
}
function guid() {
  if (function_exists('com_create_guid') === true) {
    return trim(com_create_guid(), '{}');
  }
  return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
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
