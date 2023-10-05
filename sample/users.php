<?php

include_once('../ci_lite.php');
include_once('my.php');

$user_model = new User_model();

if ($_POST) {
  $user = user_form();
  $user_model->save($user);
}

$users = $user_model->find_all();
?>

<?php echo form_open('users.php'); ?>
<p>Name
  <?php echo form_input('username'); ?>
</p>
<p>
  <?php echo form_submit('submit', 'Save changes'); ?>
</p>
<?php echo form_close(); ?>

<h3>Users</h3>

<?php foreach ($users as $user): ?>
  <li><?php echo $user->id; ?>: <?php echo $user->username; ?>
    <?php echo anchor('edit_user.php?id=' . $user->id, 'Edit'); ?>
    <?php echo anchor('delete_user.php?id=' . $user->id, 'Delete'); ?>
  </li>
<?php endforeach; ?>
