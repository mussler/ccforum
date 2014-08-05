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


function loadPosts() { // Displays posts, only first posts in thread
	printf("<h2>Posts</h2>");
	$sql = "SELECT p.author, p.clocked, p.content, p.subject from post p where not exists (select * from thread t where p.author = t.author and p.clocked = t.clocked)";
	$query = sendQuery($sql);
	printf("<ul>");
	while($row = $query->fetch_row()) {
		$s = getAlias($row[0]);;
		printf("<li><h4><a href='index.php?loc=showpost&pa=%s&pd=%s'>%s</a></h4><span class='postauthor'><i>Posted by: </i><b>%s</b>.<br><i>Date added: </i><b>%s.</b></li>", $row[0],$row[1], $row[3], $s, date("jS F Y, H:i", strtotime($row[1])) );
		
	}
	printf("</ul>");
}

function isTopic($pa, $pd) { //Returns true if the post is a Topic, false if not
	$sql1 = "SELECT p.author, p.clocked, p.content, p.subject from post p where not exists (select * from thread t where p.author = t.author and p.clocked = t.clocked)";
	$sql2 = "select author, clocked from (".$sql1.") z where z.author=".$pa." and z.clocked='".$pd."'";
	$query = sendQuery($sql2);
}

function userPostDelete($pa, $pd){
	if(isTopic($pa, $pd)) {
		echo '<script>alert("You don\'t have permission to delete a topic!"); location.assign("index.php"); </script>';
	} else {
		if(sendQuery("update post set content='<i>Post deleted by user.</i>', udel=true where author=".$pa." and clocked='".$pd."'")) {
			echo '<script>alert("Post deleted."); location.assign("index.php"); </script>';
		}
	}
}

function adminPostDelete($pa, $pd) {
	if(sendQuery("delete from post where author=".$pa." and clocked='".$pd."'")) {
			echo '<script>alert("Post deleted."); location.assign("index.php"); </script>';
		}
}



function showPost($pa, $pd) { // Display post and call showThread
	$sqlP = sprintf("select * from post where author=%s and clocked='%s'", $pa, $pd);
	$uAlias = getAlias($pa);
	$queryP = sendQuery($sqlP);
	$rowc = $queryP->num_rows;
	if($rowc === 0) {
		$result = 'The selected post doesn\'t exist';
	} else {
	$rowP = $queryP->fetch_row();
	$result = sprintf("<ul><li><h3>%s</h3> posted by <b>%s</b> on %s <p>%s</p>", $rowP[3], $uAlias, date("jS F Y, H:i", strtotime($rowP[1])), $rowP[2]);
	if(isset($_SESSION['logged'])){
	if($uAlias == $_SESSION['logged'] || $_SESSION['type'] == 'A') {
	$result .= sprintf("<p><a href='index.php?loc=editpost&pa=%s&pd=%s'>Edit Post</a></p>", $pa, $pd);
	}
	if($_SESSION['type']  == 'A') {
	$result .= sprintf("<p><a href='index.php?loc=deletepost&pa=%s&pd=%s'>Delete Post</a></p>", $pa, $pd);
	}
	if(isset($_SESSION['logged'])){
		$result .= sprintf('<p><a href="index.php?loc=reply&pa=%s&pd=%s">Write a Reply</a>', $pa, $pd);
		}
	}
	}
	$result .= '</li>';
	$resultT = showThread($pa, $pd);
	if(empty($resultT) && $rowc != 0) {
		$result .= '<p>No one replied to this post.</p>';
		
	} else {
		$result .= $resultT;
	}
	$result .= '</ul>';
	
	echo $result;
}

function showThread($pa, $pd) { //Recursive thread display.
	$sql = sprintf("select * from post p join (select * from thread where authororg = %s and clockedorg = '%s') s on p.author = s.author and p.clocked = s.clocked", $pa, $pd);
	$sql2 = ("select subject from post where author=".$pa." and clocked='".$pd."'");
	$query = sendQuery($sql);
	$query2 = sendQuery($sql2);
	$subjectReply = $query2->fetch_object()->subject;
	$rowc = $query->num_rows;
	if($rowc === 0) {
		return;
	} else {
		$result = '<ul>';

								while($row = $query->fetch_row()) {
										
									
										$uAlias = getAlias($row[0]);
										$result .= sprintf("<li><h4>%s</h4> posted by <b>%s</b> on %s as a reply to: <b>%s</b><p>%s</p>", $row[3], $uAlias, date("jS F Y, H:i", strtotime($row[1])), $subjectReply, $row[2]);
								if(isset($_SESSION['logged'])){
									if(($uAlias == $_SESSION['logged'] && !$row[4]) || $_SESSION['type'] == 'A'){
										
										$result .= sprintf("<p><a href='index.php?loc=editpost&pa=%s&pd=%s'>Edit Post</a></p>", $row[0], $row[1]);
										$result .= sprintf("<p><a href='index.php?loc=deletepost&pa=%s&pd=%s'>Delete Post</a></p>", $row[0], $row[1]);
										}
		if(!$row[4]) {								
		$result .= sprintf('<p><a href="index.php?loc=reply&pa=%s&pd=%s">Write a Reply</a>', $row[0], $row[1]);
		}
		}
									
										
										$result .= showThread( $row[0], $row[1]);
										$result .= '</li>';
										}
	}
	
		



			$result .= '</ul>';

	return $result;
		
	}
	


function replyPost($ra, $rt, $rc, $roa, $rod){ // Add reply to post, create post and thread, returns to index if successful
	
	$userid = getUID($ra);
	$timestamp = date('Y-m-d H:i:s');
	$sql1 = sprintf("insert into post(author, clocked, content, subject) values(%s, '%s', '%s', '%s')", $userid, $timestamp, $rc, $rt);
	$sql2 = sprintf("insert into thread values(%s, '%s', %s, '%s')", $roa, $rod, $userid, $timestamp);
	$q2 = sendQuery($sql1);
	$q3 = sendQuery($sql2);
	if($q2 && $q3) {
	header("Location: index.php");
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
		
		if(sendQuery($sql1)) {
			if($_SESSION['logged'] != $ualias) {
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
	$sql = sprintf("insert into post(author, clocked, content, subject) values(%s, '%s', '%s', '%s')", $uid, $timestamp, $content, $title);
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
		if(empty($img)){ 
		$result = "‪‪default.jpg"; 
		} else { 
		$result = "data:".$img['mimetype'].";base64,".base64_encode( $img['imageitself'] );
		}

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