<?php

include_once('../ci_lite.php');
include_once('my.php');

$id = $_GET['id'];
$user_model = new User_model();
$user_model->delete($id);
redirect('users.php');
