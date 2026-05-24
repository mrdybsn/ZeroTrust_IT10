<?php
require_once 'includes/auth.php';
logout();
header('Location: /zero_trust/index.php?msg=logged_out');
exit;
?>
