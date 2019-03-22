<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/resources/config.php');

/* ROUTES */
require(__FILEPATH__.'resources/routes/admin.php');
require(__FILEPATH__.'resources/routes/pages.php');
require(__FILEPATH__.'resources/routes/account.php');
require(__FILEPATH__.'resources/routes/browse.php');
require(__FILEPATH__.'resources/routes/application.php');
require(__FILEPATH__.'resources/routes/app_static_controller.php');
require(__FILEPATH__.'resources/routes/app_user_controller.php');

//Run Slim
$app->run();
?>
