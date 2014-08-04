<?php
if (session_status() == PHP_SESSION_NONE) { /* Start session only if not started already */
    session_start();
}
if(isset($_SESSION['logged'])) { /* Clear session data */
	$_SESSION = array();
if (isset($_COOKIE['ccforum'])) { /* If cookie is set, expire it then unset it */
    setcookie("ccforum", "", time()-10);
	unset($_COOKIE['ccforum']);
}
session_destroy(); /* Destroy settion */
}

if(isset($_GET['f'])) { /* Return to index */
	header("Location: index.php");
}
?>