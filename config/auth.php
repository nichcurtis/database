<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'driver'			=>	'database',
	'hash_method'		=>	'sha256',
	'hash_key'		=>	NULL,
	'lifetime'		=>	1209600,
	'session_type'		=>	Session::$default,
	'session_key'		=>	'auth_user',

	// database config group to use for Auth Database driver
	'db_instance'		=>	'default',
	'table_name'		=>	'users'
);
