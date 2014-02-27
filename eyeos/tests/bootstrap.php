<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26/02/14
 * Time: 12:36
 */

$_SERVER['HTTP_REAL_SERVER_NAME'] = '';
$_SESSION['eyeos_username']="test user";
$_SESSION['eyeos_password']="test password";

session_save_path('./tmp/');

chdir(dirname(__FILE__));

require_once("../../settings.php");
require_once("../system/bootstrap/Bootstrap.php");

Bootstrap::initTests();

?>