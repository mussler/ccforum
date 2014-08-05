<?php include_once('functions.php'); ?>
		<FORM class="form form2" ACTION="" METHOD="POST">
			<h1>Welcome to the registration page</h1>
            <p>
			Please input the registration details to create an account here</p>
	
		<table>
			<tr>
				<td>User name :</td><td><input name="alias" type="text" size"20" required></input></td>
			</tr>
			<tr>
				<td>Password :</td><td><input name="pwd" type="password" size"20" required></input></td>
			</tr>
			<tr>
				<td>Retype password :</td><td><input name="pwd2" type="password" size"20" required></input></td>
			</tr>
            

		</table>
		
		<input type="submit" name="register" value="Register me!"></input>
		</form>
        <div class="clearfix"></div>
<?php 
if(isset($_POST['alias']) && isset($_POST['pwd']) && isset($_POST['pwd2']) ) {
	if($_POST['pwd'] == $_POST['pwd2']){
		
	registerUser($_POST['alias'], $_POST['pwd']);
				
		} else {
			echo '<script>alert("Passwords does not match")</script>';
		}
}

?>