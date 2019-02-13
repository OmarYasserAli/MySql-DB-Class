<?php

require_once 'core/init.php';

//$users= DB::getInstance()->query("SELECT username FROM users WHERE username = ?",  array('omar'));
//$users= DB::getInstance()->get('users',array('username', '=', 'omar'));
$users= DB::getInstance()->insert('users',array(
	'username' =>'ali',
	'password' => '123',
	'salt' =>'456'
));
if($users)
echo "Done";
else
echo "NO";
/*if(!$users->count())
	echo "No Users";
else
	echo $users->first()->username;
	*/
?>