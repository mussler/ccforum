<?php include_once('functions.php'); ?>
		<h1>Add new post</h1>
		<form class="form form2" action="" method="POST">
			<input type="text" name="title" id="subject" placeholder="Title" required><br>
			<textarea type="textarea" name="content" id="content" placeholder="Content" rows="25" cols="45" required></textarea><br>
			<input type="submit" value="Post!">
		</form>
        <div class="clearfix"></div>
<?php

if (isset($_POST['title']) && isset($_POST['content'])) {
			if ($_POST['title'] != '' && $_POST['content'] != '') {
				createPost($_POST['title'], $_POST['content']);
				
			} else {
				echo '<script>alert("Title and content cannot be empty");</script>';
			}
		}

?>
