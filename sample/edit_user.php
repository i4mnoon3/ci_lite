<?php

include_once('../ci_lite.php');
include_once('my.php');

$user_model = new User_model();
$id = $_GET['id'];

if ($_POST) {
  $user = user_form();
  $user_model->update($user, $id);
  redirect('users.php');
}

$user = $user_model->read($id);

?>

<?php echo form_open('edit_user.php?id=' . $id); ?>
<p>Name
  <?php echo form_input('name', $user->username); ?>
</p>
<p>
  <?php echo form_submit('submit', 'Save changes'); ?>
  or <?php echo anchor('users.php', 'cancel'); ?>
</p>
<?php echo form_close(); ?>
