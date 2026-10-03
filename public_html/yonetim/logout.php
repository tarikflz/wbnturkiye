<?php
require dirname(__DIR__) . '/_app.php';

logout();
header('Location: /yonetim/login.php');
exit;
