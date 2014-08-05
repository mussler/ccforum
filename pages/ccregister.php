<?php include_once('functions.php'); ?>
		<FORM class="form form2" ACTION="" METHOD="POST">
			<h1>Welcome to the registration page</h1>
			Please input the registration details to create an account here<br>
	
		<table>
			<tr>
				<td>User type :</td><td>
				<select id="types" name="types">
      				<option value="A">Admin</option>
      				<option value="O">User</option>
   				 </select>
   				</td>
   			</tr>
   			<tr>
				<td>User name :</td><td><input name="alias" type="text" size"20" required></input></td>
			</tr>
			<tr>
				<td>Email :</td><td><input name="regemail" type="email" size"20" required></input></td>
			</tr>
			<tr>
				<td>Password :</td><td><input name="pwd" type="password" size"20" required></input></td>
			</tr>
			<tr>
				<td>Retype password :</td><td><input name="pwd2" type="password" size"20" required></input></td>
			</tr>
            <tr>
            	<td><label for="file">Filename:</label></td>
                <td><form action="upload_file.php" method="post" enctype="multipart/form-data">
				<input type="file" name="file" id="file"><br>
				<input type="submit" name="submit" value="Submit"></form>
                </td>
            </tr>

		</table>
		
		<input type="submit" name="register" value="Register me!"></input>
		</FORM>
<?php 
if(isset($_POST['types']) && isset($_POST['alias']) && isset($_POST['regemail']) && isset($_POST['pwd']) && isset($_POST['pwd2']) ) {
	if($_POST['pwd'] == $_POST['pwd2']){
		
	registerUser($_POST['alias'], $_POST['pwd'], $_POST['types']);
		
		} else {
			echo '<script>alert("Passwords does not match")</script>';
		}
} else if(isset($_POST['types'])) {
	echo '<script>alert("Some fields are missing!")</script>';
}

?>
<?php
	$file = $_FILES['file'];
	$name = $file['name'];
	$path = "/uploads/" . basename($name);
if (move_uploaded_file($file['tmp_name'], $path)) {
    // Move succeed.
} else {
    // Move failed. Possible duplicate?
}
?>