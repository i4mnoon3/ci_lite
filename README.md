# ci_lite

## Usage

```php
<?php

include_once('ci_lite.php');

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

$user_model = new User_model();
$users = $user_model->find_all();

?>

<h3>Users</h3>

<?php foreach ($users as $user): ?>
  <li><?php echo $user->id; ?>: <?php echo $user->username; ?>
    <?php echo anchor('edit_user.php?id=' . $user->id, 'Edit'); ?>
    <?php echo anchor('delete_user.php?id=' . $user->id, 'Delete'); ?>
  </li>
<?php endforeach; ?>

```