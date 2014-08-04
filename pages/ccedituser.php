<?php
	include_once('functions.php'); 	

		$edit_id = $_GET['uid'];
		
		$edit_query = "select uid, alias, type from user where uid='$edit_id'"; // select current user data from database
		
		$run_edit = sendQuery($edit_query); 
		
		while ($edit_row = $run_edit->fetch_assoc()){ // this while is responsible about current user data which in next steps are write in form
				
			$id = $edit_row['uid'];
			$username = $edit_row['alias'];
			$type = $edit_row['type'];

		}
	
?>
	<form id="form" method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="uid" value="<?php echo $id; ?>">
		Username: </br> <input type="text" name="username" value="<?php echo $username;?>" /></br></br>
        Type: </br> <input type="text" name="type" value="<?php echo $type;?> "/></br></br> <!-- there can make different: with type="radio" and one php if part -->
		Change password?: </br> <input type="password" name="password" value="" /></br></br>
		
		<input type="submit" name="update" value="Edit" />	
	</form>
	<?php
	
	if(isset($_POST['update'])){// this if is responsible about data update in database
		$update_id = $_POST['uid'];
		$username2 = $_POST['username'];
		$type2 = $_POST['type'];
		$password = $_POST['password'];
	
	if($username2=='' or $type2==''){ // if any of fields are empty then it will show it 
	
	echo "<script>alert('Username and type cannot be empty!')</script>";
	exit();
	}
	else {
		updateUser($update_id, $username2, $type2, $password);
		

	
	}
	}




	
?>