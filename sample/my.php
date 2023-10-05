<?php

function user_form() {
  return array(
    'username' => $_POST['username'],
  );
}

class User_model extends CI_Model {
  function __construct() {
    parent::__construct();
  }
  function read($id) {
    return $this->db->get_where('users', array('id' => $id))->row();
  }
  function find_all() {
    return $this->db->get('users')->result();
  }
  function save($user) {
    $this->db->insert('users', $user);
  }
  function update($user, $id) {
    $this->db->update('users', $user, array('id' => $id));
  }
  function delete($id) {
    $this->db->delete('users', array('id' => $id));
  }
}
