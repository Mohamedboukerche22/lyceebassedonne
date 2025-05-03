<?php
require 'config.php';

session_destroy();
redirect('login.php');
?>