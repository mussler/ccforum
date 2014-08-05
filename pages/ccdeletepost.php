<?php
include_once("functions.php");
if(isset($_SESSION['logged']) && isset($_GET['pa']) && isset($_GET['pd'])){
	if($_SESSION['type'] == 'O') {
		if(getUID($_SESSION['logged']) == $_GET['pa']) {
			 userPostDelete($_GET['pa'], $_GET['pd']);
		}
		
	} else {
		 adminPostDelete($_GET['pa'], $_GET['pd']);
	}
	
	
}
?>

