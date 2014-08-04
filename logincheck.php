<?php
if (session_status() == PHP_SESSION_NONE) { /* Start session only if not started already */
    session_start();
}
include_once('dbconf.inc.php');
include_once('functions.php');
if(isset($_POST['username'])) { /* Check if post data is set, then assing variable */

	$username = $_POST['username'];
	$upwd = sha1($_POST['upwd']);
	$user_select_q = sprintf('select alias, type from user where alias = "%s" and pwd = "%s"', $username, $upwd);
	$query = sendQuery($user_select_q);
	$data = $query->fetch_row();
	if($data[0] === $username) {
		if(isset($_POST['remember_login'])) {
			setSession($username, $upwd, $data[1], 'true');
		} else {
			setSession($username, $upwd, $data[1]);
		}
		$q = "update user set lastonline=CURRENT_TIMESTAMP where alias='".$username."'";
		sendQuery($q);
		echo 'true';
	} else {
		if(isset($_SESSION['logged'])) {
			unset($_SESSION['logged']);
		}
		echo 'false';
	}
	$_POST = array();
} else {
	if(isset($_SESSION['logged'])) {
			unset($_SESSION['logged']);
		}
		echo 'false';
}

?>