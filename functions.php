<?php
error_reporting(-1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$green_flag_URI = array('showpost', 'register', 'users');
if(!isset($_SESSION['logged'])){
	if(isset($_GET['loc'])) {
		if(!in_array($_GET['loc'], $green_flag_URI)) {
	echo '<script>alert("You are not logged in!");
	location.assign("index.php");</script>';
	}
	}
}
include_once('dbconf.inc.php');
function setSession($username,$password,$utype,$cookie=null){ // create session and cookie
   $_SESSION['logged']=$username;
   $_SESSION['type']=$utype;
   
   
    if($cookie=='true'){
        $cookiehash = md5(sha1($username.$password));
		setcookie("ccforum",$cookiehash,time()+36*24*365, false);
    }
}

function CheckCookieLogin() { // Check for cookie, refresh it if found and setup session data
	if(isset($_COOKIE['ccforum'])) {
    $cname = $_COOKIE['ccforum'];
    if (!empty($cname)) {   
        $sql = "select alias, type from user where md5 ( Sha1 (Concat(alias,pwd))) = '$cname'";
		$query = sendQuery($sql);
		$data = $query->fetch_row();
		if(!empty($data[0])){
        $_SESSION['logged'] = $data[0];
		$_SESSION['type'] = $data[1];
        $_SESSION['cookie'] = $cname;
        // reset expiry date
        setcookie("ccforum",$cname,time()+36*24*365,false);
		} else {
			setcookie("ccforum", "", time()-10);
			unset($_COOKIE['ccforum']);
		}
    }
	}
	
}

function canReply($opa, $opd, $paa) { // Check if it's possible to reply (post is last in thread)
	$sql1 = "select uid from user where alias='".$paa."'";
	$query1 = sendQuery($sql1);
	$row1 = $query1->fetch_row();
	$sql2 = sprintf("select * from thread where authororg=%s and clockedorg='%s' and author=%s", $opa, $opd, $row1[0]);
	$query2 = sendQuery($sql2);
	$rowc2 = $query2->num_rows;
	if($rowc2 == 0) {
		return true;
	} else {
		return false;
	}
	return false;
	
}

function loadPosts() { // Displays posts, only first posts in thread
	printf("<h2>Posts</h2>");
	$sql = "SELECT p.author, p.clocked, p.content, p.subject from post p where not exists (select * from thread t where p.author = t.author and p.clocked = t.clocked)";
	$sql2 = "select alias from user where uid=%s";
	$query = sendQuery($sql);
	printf("<ul>");
	while($row = $query->fetch_row()) {
		$s = sprintf($sql2, $row[0]);
		$query2 = sendQuery($s);
		$row2 = $query2->fetch_row();
		printf("<li><h4><a href='index.php?loc=showpost&pa=%s&pd=%s'>%s</a></h4><span class='postauthor'><i>Posted by: </i><b>%s</b>.<br><i>Date added: </i><b>%s.</b></li>", $row[0],$row[1], $row[3], $row2[0], date("jS F Y, H:i", strtotime($row[1])) );
		
	}
	printf("</ul>");
}

function showPost($pa, $pd) { // Display post and call showThread
	$sqlP = sprintf("select * from post where author=%s and clocked='%s'", $pa, $pd);
	$sqlU = sprintf("select alias from user where uid=%s", $pa);
	$queryP = sendQuery($sqlP);
	$queryU = sendQuery($sqlU);
	$rowc = $queryP->num_rows;
	if($rowc === 0) {
		$result = 'The selected post doesn\'t exist';
	} else {
	$rowP = $queryP->fetch_row();
	$rowU = $queryU->fetch_row();
	$result = sprintf("<ul><li><h3>%s</h3> posted by <b>%s</b> on %s <p>%s</p>", $rowP[3], $rowU[0], date("jS F Y, H:i", strtotime($rowP[1])), $rowP[2]);
	if(isset($_SESSION['logged'])){
	if($rowU[0] == $_SESSION['logged'] || $_SESSION['type'] == 'A') {
	$result .= sprintf("<p><a href='index.php?loc=editpost&pa=%s&pd=%s'>Edit Post</a></p>", $pa, $pd);
	}
	if($_SESSION['type']  == 'A') {
	$result .= sprintf("<p><a href='index.php?loc=deletepost&pa=%s&pd=%s'>Delete Post</a></p>", $pa, $pd);
	}
	}
	}
	$result .= '</li>';
	$resultT = showThread($pa, $pd);
	if(empty($resultT) && $rowc != 0) {
		$result .= '<p>No one replied to this post.</p>';
		if(isset($_SESSION['logged'])){
		$result .= sprintf('<p><a href="index.php?loc=reply&pa=%s&pd=%s">Write a Reply</a>', $pa, $pd);
		}
	} else {
		$result .= $resultT;
	}
	$result .= '</ul>';
	
	echo $result;
}

function showThread($pa, $pd) { //Recursive thread display.
	$sql = sprintf("select * from post p join (select * from thread where authororg = %s and clockedorg = '%s') s on p.author = s.author and p.clocked = s.clocked", $pa, $pd);
	$query = sendQuery($sql);
	$rowc = $query->num_rows;
	if($rowc === 0) {
		return;
	} else {
		$row = $query->fetch_row();
		$sqlU = sprintf("select alias from user where uid=%s", $row[0]);
		$queryU = sendQuery($sqlU);
		$rowU = $queryU->fetch_row();
		$result = sprintf("<li><h4>%s</h4> posted by <b>%s</b> on %s <p>%s</p>", $row[3], $rowU[0], date("jS F Y, H:i", strtotime($row[1])), $row[2]);
		if(isset($_SESSION['logged'])){
		if($rowU[0] == $_SESSION['logged'] || $_SESSION['type'] == 'A'){
	$result .= sprintf("<p><a href='index.php?loc=editpost&pa=%s&pd=%s'>Edit Post</a></p>", $row[0], $row[1]);
	}
	if($_SESSION['type']  == 'A') {
	$result .= sprintf("<p><a href='index.php?loc=deletepost&pa=%s&pd=%s'>Delete Post</a></p>", $row[0], $row[1]);
	}
		}
	$result .= '</li>';
	$resultT = showThread( $row[0], $row[1]);
	if(empty($resultT)) {
		if(isset($_SESSION['logged'])){
		$result .= sprintf('<p><a href="index.php?loc=reply&pa=%s&pd=%s">Write a Reply</a>', $row[0], $row[1]);
		}
	} else {
		$result .= $resultT;
	}
	return $result;
		
	}
	
}

function replyPost($ra, $rt, $rc, $roa, $rod){ // Add reply to post, create post and thread, returns to index if successful
	if(canReply($roa, $rod, $ra)) {	
	
	$q1 = sendQuery("select uid from user where alias='".$ra."'");
	$r1 = $q1->fetch_row();
	$timestamp = date('Y-m-d H:i:s');
	$sql1 = sprintf("insert into post values(%s, '%s', '%s', '%s')", $r1[0], $timestamp, $rc, $rt);
	$sql2 = sprintf("insert into thread values(%s, '%s', %s, '%s')", $roa, $rod, $r1[0], $timestamp);
	$q2 = sendQuery($sql1);
	$q3 = sendQuery($sql2);
	if($q1 && $q2 && $q3) {
	header("Location: index.php");
	}
	}
	
}

function editPost($pa, $pd, $pc, $pt) { // Updates the post, returns to index if successful
	$sqlU = sprintf("update post set content='%s', subject='%s' where author=%s and clocked='%s'", $pc, $pt, $pa, $pd);
	$result = sendQuery($sqlU);
	if($result) {
    header("Location: index.php");
	}
}

function deleteUser($uid) {
	
	$sql = "DELETE FROM user WHERE uid='$uid' "; // this part will delete data from database
	$ua = getAlias($uid);
	if(sendQuery($sql)) {
		$string = "<script>alert('User Has been Deleted');";
		if($_SESSION['type'] == 'A' && $_SESSION['logged'] != $ua) {
			$string .=" location.assign('index.php?loc=users');</script>";
			} else {
				$string .=" location.assign('logout.php?f=y');</script>";
			}
		echo $string;		

	}
	
}

function updateUser($uid, $ualias, $utype=null, $upwd=null) {
		$sql1 = "update user set ";
		$sql1 .= "alias='".$ualias."'";
		if($utype != null) {
			$sql1 .= ", type='".$utype."'";
		}
		if($upwd != null) {
			$sql1 .= ", pwd=sha1('".$upwd."')";
		}
		$sql1 .= " where uid=".$uid;
		echo $sql1;
		
		if(sendQuery($sql1)) {
			if($_SESSION['logged'] == $ualias) {
				echo "<script>alert('User has been updated'); location.assign('index.php?loc=panel&uid=".$uid."');</script>";
			} else {
				echo "<script>alert('User has been updated'); location.assign('logout.php?f=y');</script>";
			}
			
			}
		
}
function registerUser($alias, $pwd, $type) {
	$sql = sprintf("insert into user(alias, pwd, lastonline, type) values('%s', sha1('%s'), CURRENT_TIMESTAMP, '%s')", $alias, $pwd, $type);
	if(sendQuery($sql)) {
		echo "<script>alert('User has been registered. You can log in now.'); location.assign('index.php');</script>";
	}
}

function createPost($title, $content) {
	$timestamp = date('Y-m-d H:i:s');
	$uid = getUID($_SESSION['logged']);
	$sql = sprintf("insert into post values(%s, '%s', '%s', '%s')", $uid, $timestamp, $content, $title);
		if(sendQuery($sql)) {
		echo "<script>alert('Post added'); location.assign('index.php?loc=showpost&pa=$uid&pd=$timestamp');</script>";
	}
}

function getUID($alias) {
	$q = sendQuery("select uid from user where alias='$alias'");
	$r = $q->fetch_row();
	return $r[0];
}
function getAlias($uid) {
	$q = sendQuery("select alias from user where uid='$uid'");
	$r = $q->fetch_row();
	return $r[0];
}
function getUType($uid) {
	$q = sendQuery("select type from user where uid='$uid'");
	$r = $q->fetch_row();
	return $r[0];
}
function getImage($uid) {
		$sql  = "select mimetype, imageitself from image where owner = ".$uid;
	 	$arr = sendQuery($sql);
	 	$img = $arr->fetch_array();
		if(empty($img)){ $result = "‪‪C:\wamp\www\ccforum\default.jpg"; } else { 
		$result = "data:".$img['mimetype'].";base64,".base64_encode( $img['imageitself'] );}
	 	return $result;
		
}

function saveImage($img, $type, $userid) {
	$sql  = "  insert into image";
  $sql .= " (mimetype, owner, imageitself) values ";
  $sql .= sprintf("('%s','%s','%s')",
    $type,
    $userid,
    $img);
	$sql .= " on duplicate key update mimetype='".$type."', imageitself='".$img."'";
  if(sendQuery($sql)) {
	  return true;
  } else {
	  false;
  }
	
}
function sendQuery($query) { /* Executes query, catches errors, returns result, print erros if in debug mode (&debug) */
try {
$mysqli = new mysqli(HOST,USER, UPWD, DBNAME);
 if (mysqli_connect_error())
    {
        throw new Exception(mysqli_connect_error());
    }
	$result = $mysqli->query($query);
	 if (!$result)
    {
        throw new Exception($mysqli->error);
    }
$mysqli->close();
return $result;

} catch (Exception $e) {
	if(isset($_GET['debug'])) {
		echo "Debug mode on.<br>".$e->getMessage();
		return false;
	
	}
	
}
}
?>