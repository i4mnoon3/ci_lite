<?php

// config
$db['default'] = array(
  'hostname' => 'localhost',
  'username' => 'root',
  'password' => '',
  'database' => 'test',
);
$enable_profiler = false;

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
    $this->where = null;
  }
  function select($select) {
    $this->select = $select;
    return $this;
  }
  function from($table_name) {
    $this->table_name = $table_name;
    return $this;
  }
  function where($col, $val) {
    if (!$this->where) {
      $this->where = array();
    }
    $this->where[] = array($col => $val);
  }
  function get($table_name) {
    $query = '';
    $query .= 'select ' . $this->select . "\n";
    if ($table_name) {
      $this->table_name = $table_name;
    }
    $query .= ' from ' . $this->table_name;
    if ($this->where) {
      $query .= "\n";
      $query .= 'where ';
      $w = '';
      $i = 0;
      foreach ($this->where as $where) {
        $key = array_keys($where)[0];
        $value = $where[$key];
        if ($i++ > 0) {
          $w .= '  and ';
        }
        $val = '';
        if (is_string($value)) {
          $val = "'" . mysqli_real_escape_string($this->con, $value) . "'";
        } else {
          $val = mysqli_real_escape_string($this->con, $val);
        }
        $w .= $key . ' = ' . $val . "\n";
      }
      $query .= $w;
    }
    $this->show_profiler($query);
    $this->result = $this->con->query($query);
    if (!$this->result) {
      die($this->con->error);
    }
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
      $this->where = $where; // TODO:
      $query .= "\n";
      $query .= ' where ';
      $w = '';
      $i = 0;
      foreach ($this->where as $where) {
        $key = array_keys($where)[0];
        $value = $where[$key];
        if ($i++ > 0) {
          $w .= '  and ';
        }
        $val = '';
        if (is_string($value)) {
          $val .= "'" . mysqli_real_escape_string($this->con, $value) . "'";
        } else {
          $val = mysqli_real_escape_string($this->con, $val);
        }
        $w .= $key . ' = ' . $val;
      }
      $query .= $w;
    }
    $this->show_profiler($query);
    $this->result = $this->con->query($query);
    if (!$this->result) {
      die($this->con->error);
    }
    return $this;
  }
  function show_profiler($query) {
    global $enable_profiler;
    if ($enable_profiler) {
      print_pre($query);
    }
  }
  function row() {
    $data = null;
    if ($row = mysqli_fetch_assoc($this->result)) {
      $data = (object) $row;
    }
    return $data;
  }
  function result() {
    $data = array();
    while ($row = mysqli_fetch_assoc($this->result)) {
      $data[] = (object) $row;
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
        $val .= "'" . mysqli_real_escape_string($this->con, $value) . "'";
      } else {
        $val .= mysqli_real_escape_string($this->con, $value);
      }
    }
    $query = '';
    $query .= 'insert into ' . $table_name . '(' . $col . ')' . "\n";
    $query .= ' values (' . $val . ')';
    $this->show_profiler($query);
    $this->result = $this->con->query($query);
    if (!$this->result) {
      die($this->con->error);
    }
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
      $col .= $key . ' = ' . $val . "\n";
    }
    $query = '';
    $query .= 'update ' . $table_name . "\n";
    $query .= ' set ' . $col;
    if ($where) {
      $query .= ' where ';
      $w = '';
      $i = 0;
      foreach ($where as $key => $value) {
        if ($i++ > 0) {
          $w .= '  and ';
        }
        $val = '';
        if (is_string($value)) {
          $val .= "'" . mysqli_real_escape_string($this->con, $value) . "'";
        } else {
          $val = mysqli_real_escape_string($this->con, $val);
        }
        $w .= $key . ' = ' . $val + "\n";
      }
      $query .= $w;
    }
    $this->show_profiler($query);
    $this->result = $this->con->query($query);
    if (!$this->result) {
      die($this->con->error);
    }
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
          $val .= "'" . mysqli_real_escape_string($this->con, $value) . "'";
        } else {
          $val = mysqli_real_escape_string($this->con, $val);
        }
        $w .= $key . ' = ' . $val;
      }
      $query .= $w;
    }
    $this->show_profiler($query);
    $this->result = $this->con->query($query);
    if (!$this->result) {
      die($this->con->error);
    }
  }
}

// models
class CI_Model {
  function __construct() {
    $this->db = new Db();
  }
}
