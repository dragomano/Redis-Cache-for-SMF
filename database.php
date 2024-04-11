<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && ! defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (! defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if (version_compare(PHP_VERSION, '8.0', '<')) {
	die('This mod needs PHP 8.0 or greater. You will not be able to install/use this mod. Please, contact your host and ask for a php upgrade.');
}

if (SMF === 'SSI')
	echo 'Database changes are complete!';
