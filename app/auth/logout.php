<?php
session_start();
session_destroy();
header("Location: course/6_CRM/app/auth/login.php");
exit();