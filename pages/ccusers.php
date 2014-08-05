<?php include_once('functions.php') ?>
	
	<h3>LIST OF USERS</h3>
	<table>
		<tr>
            <td width="150">User name</td>
			<td>Type</td>
            <td>Last seen</td>
			<td>Options</td>
            <td>Image</td>
		</tr>
	<?php
		$list_query = sendQuery("SELECT uid, alias, type, lastonline FROM user"); // select data from databese! users are database 
																		   //table name it can be different, but look carefully if it will not be same like in database, 
																		   //code will not work!!!!
		while($run_list = $list_query->fetch_assoc()){ // diplay data which are selected from database
			$u_id = $run_list['uid'];
			$u_uname = $run_list['alias'];
			$u_type = $run_list['type'];
			$u_lastonline = $run_list['lastonline'];
	?>
		<tr>
			<td><?php echo $u_uname; // this is data from database in web page it displays like 1,2 etc. ?></td>
			<td><?php echo $u_type; ?></td>
			<td>
			<?php echo $u_lastonline; ?>
			</td>
            <?php if(isset($_SESSION['logged'])) {
				if($_SESSION['type'] === 'A' || $_SESSION['logged'] === $u_uname) { ?>
					<td><a href="index.php?loc=panel&uid=<?php echo $u_id; // there you need write your thing, but EDIT is linked with options.php?>">Manage user</a></td>
			<?php } else {
						echo '<td>Options not available</td>';
				}
			} 
			else {
				echo '<td>Log in to see the options</td>';
				}
			?>
            <td><img src="crashImageView.php&pid=<?php echo $u_id;?>" alt="some text"/></td>
		</tr>
	<?php
		} // close while and if
	?>
	</table>
 <?php //user delete
 if(isset($_GET['del'])) {
	 deleteUser($_GET['del'], $_GET['un']);
 }
 
 
 ?>
