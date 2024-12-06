<?php
Session_start();
Session_destroy();
echo "<script> location.href='login.php'; </script>";
?>