<?php
session_start();

setcookie('mail', '');
setcookie('security', '');
session_unset();
session_destroy();

header('Location:' . (isset($_POST['redir']) ? $_POST['redir'] : '/'));
?>