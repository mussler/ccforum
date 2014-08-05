<?php
if(isset($_SESSION['logged'])){
$sqlGetPost = "select * from post where author=".$_GET['pa']." and clocked='".$_GET['pd']."'";
$queryPostData = sendQuery($sqlGetPost);
$rowc = $queryPostData->num_rows;
if($rowc == 0) {
	echo '<p>The post you are trying to reply to doesn\'t exist.</p>';

}else {
	$data = $queryPostData->fetch_row();
	

?>
<h2>Reply to: <?php echo $data[3]; ?></h2>
<p><code><?php echo $data[2]; ?></code></p>

<?php if(!isset($_SESSION['logged'])) {
	echo '<p>You must be logged in to post a reply.</p>';
} else{ ?>
<form class="form form2" id="postreplyform" method="post" action="">
<input type="hidden" name="replytitle" value="RE: <?php echo $data[3]; ?>">
<input type="hidden" name="replyauthor" value="<?php echo $_SESSION['logged'] ?>">
<input type="hidden" name="replyorgauthor" value="<?php echo $_GET['pa'] ?>">
<input type="hidden" name="replyorgdate" value="<?php echo $_GET['pd'] ?>">
<textarea name="replycontent" rows="25" cols="60" required></textarea>
<input type="submit" value="Post Reply">
</form>
<div class="clearfix"></div>
<?php }}
if(isset($_POST['replycontent'])){
	replyPost($_POST['replyauthor'],$_POST['replytitle'], $_POST['replycontent'], $_POST['replyorgauthor'], $_POST['replyorgdate']);
}
}
?>