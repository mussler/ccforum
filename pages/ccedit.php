<?php
if(isset($_SESSION['logged'])){
	$sqlGetPost = "select * from post where author=".$_GET['pa']." and clocked='".$_GET['pd']."'";
	$queryPostData = sendQuery($sqlGetPost);
	$rowc = $queryPostData->num_rows;
		if($rowc == 0) {
			echo '<p>The post you are trying to edit doesn\'t exist.</p>';
		} else {
			$data = $queryPostData->fetch_row();
	

?>

<h2>Editing: <?php echo $data[3]; ?></h2>
<?php 			$prefixPostPos = strrpos($data[3], "RE: ");
					if($prefixPostPos === false) {
						$postTitle = $data[3];
						$postPrefix = '';
					} else {
						$postTitle = substr($data[3], $prefixPostPos+4);
						$postPrefix = substr($data[3], 0, $prefixPostPos+4);
					}

?>
<form class="form form2" id="posteditform" method="post" action="">
  <input type="hidden" name="posteditauthor" value="<?php echo $data[0]; ?>">
  <input type="hidden" name="posteditdate" value="<?php echo $data[1]; ?>">
  <input type="hidden" name="postedittitleprefix" value="<?php echo $postPrefix; ?>">
  <input type="text" name="postedittitle" value="<?php echo $postTitle; ?>">
  <textarea name="posteditcontent" rows="25" cols="60" required><?php echo $data[2]; ?></textarea>
  <input type="submit" value="Save Changes">
</form>
<div class="clearfix"></div>
<?php 		}
	if(isset($_POST['posteditcontent'])){
		editPost($_POST['posteditauthor'],$_POST['posteditdate'], $_POST['posteditcontent'], $_POST['postedittitleprefix'].$_POST['postedittitle']);
	}
	} else {
		echo '<p>You must be logged in to post a reply.</p>';
	}
	


?>
