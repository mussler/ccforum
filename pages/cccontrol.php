<?php 
include_once('functions.php');
if(isset($_GET['uid'])) {
	$uid = $_GET['uid'];
} else {

	$uid = getUID($_SESSION['logged']);
}
$user_data = sendQuery("select alias, type, lastonline from user where uid=$uid");
$user_data = $user_data->fetch_row();
$alias = $user_data[0];
$type = $user_data[1];
switch($type){
	case 'A':
	$type='Admin';
	break;
	case 'O':
	$type='Regular User';
	break;
}
$lo = $user_data[2];
$lo = date("jS F Y", strtotime($lo));
if($alias == $_SESSION['logged'] || $_SESSION['type'] == 'A') {
	$canEdit = true;
} else {
	$canEdit = false;
}
?>
<h2>Control Panel</h2>
<div id="panelimg"><img id="actualimg" src="<?php echo getImage($uid); ?>" alt="<?php echo "Avatar for $alias"; ?>">
<?php if($canEdit): ?>
<form id="imgchange" action="" method="POST" enctype="multipart/form-data">
<input type="hidden" name="imguid" value="<?php echo $uid; ?>">
<div id="imgbutton">Change Avatar</div>
<input type="file" name="browseimg" id="browseimg" accept="image/*" onchange="javascript:this.form.submit();">

      
</form>

<?php 

if(isset($_FILES['browseimg']) && isset($_POST['imguid'])) {
	$image = addslashes(file_get_contents($_FILES['browseimg']['tmp_name']));
	$imguser = $uid;
	$imgtype = $_FILES['browseimg']['type'];
	if(saveImage($image, $imgtype, $imguser)){
	$string = "<script>alert('Avatar changed');";
	$string .="location.assign('index.php?loc=panel&uid=$uid');</script>";
	echo $string;
	};
} 


endif; ?>
</div>
<div id="userinfo">
<?php if(isset($_POST['edituser']) && $canEdit) {
	echo '<form class="form form2" id="submitUserEdit" action="" method="POST">';
} ?>
<p>Username: <span>
<?php 
if(isset($_POST['edituser']) && $canEdit) {
	echo '<input type="text" name="submitUserEditAlias" value="'.$alias.'">';
} else {
echo $alias;
}?></span></p>
<p>Account Type: <span>
<?php if(isset($_POST['edituser']) && $canEdit && $_SESSION['type'] == 'A') {
	echo '<br><select name="submitUserEditType">';
	echo '<option value="A">Admin</option>';
	echo '<option value="O">User</option>';
	echo '</select>';
} else {
	echo $type; 
}?></span></p>
<?php 
if(isset($_POST['edituser']) && $canEdit) {
	echo '<p>Password: <span><input type="password" name="submitUserEditPassword" placeholder="Change password"></span></p>';
	echo '<p><span><input type="submit" id="editusersave" value="Save Changes"></span></p></form><br>';
}
?>
<p>Last seen: <span><?php echo $lo; ?></span></p>
</div>
<?php if($canEdit && !isset($_POST['edituser'])): ?>
<form class="form form2" id="edituser" method="POST" action="">
<input type="hidden" name="edituser" value="<?php echo $uid; ?>">
<input type="submit" value="Edit user">
</form>
<form class="form form2" id="deleteuser" method="POST" action="">
<input type="hidden" name="deleteuser" value="<?php echo $uid; ?>">
<input type="hidden" name="token" value="<?php echo md5($_SERVER['REMOTE_ADDR'].sha1($uid.$alias.$type.$lo));?>">
<input type="submit" value="Delete user" style="background-color:#F00; color: white;">
</form>
<script type="text/javascript">
$(document).ready(function(e) {
		$("#imgbutton").click(function(){
			$("#browseimg").click();
					});
});
</script>
<?php 
endif; 
if(isset($_POST['submitUserEditAlias'])) {
	if($_POST['submitUserEditAlias']==''){ // if any of fields are empty then it will show it 
	
	echo "<script>alert('Username cannot be empty')</script>";
	exit();
	}
	else {
	updateUser($uid, $_POST['submitUserEditAlias'], $_POST['submitUserEditType'], $_POST['submitUserEditPassword']);
	}
}
if(isset($_POST['deleteuser']) && isset($_POST['token'])) {
	$testToken = md5($_SERVER['REMOTE_ADDR'].sha1($_POST['deleteuser'].$alias.$type.$lo));
	if($_POST['token'] == $testToken) {
		error_reporting(-1);
   if ((($_SERVER['PHP_AUTH_USER'] != $_SESSION['logged']) || (sha1($_SERVER['PHP_AUTH_PW']) != sendQuery("select pwd from user where alias='".$_SESSION['logged']."'")->fetch_object()->pwd)) && $_SESSION['type'] != 'A') {
      header('WWW-Authenticate: Basic Realm="Login to confirm deletion"');
      header('HTTP/1.0 401 Unauthorized');
      print('<script>alert("You must provide the proper credentials!");</script>');
      exit;
   } else {


		deleteUser($_POST['deleteuser']);
   }
	} else {
		echo ('<script>alert("Invalid token"); Location.assign("index.php");</script>');

	}
}
	



?>
