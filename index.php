<?php
include('functions.php');

if(!isset($_SESSION['cookie']) && empty($_SESSION['logged'])) {
    CheckCookieLogin();
}

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
<title>Crash Course - Message Board v0.2</title>
</head>
<body>
<div id="wrapper">
  <header>
    <div id="logo"> <img src="logo.png" style="margin: 2em auto;
display: block;"> </div>
    <nav>
      <div class="pure-menu pure-menu-open pure-menu-horizontal">
        <ul>
          <li><a href="index.php">Home</a></li>
          <?php if(isset($_SESSION['logged'])): ?>
          <li><a href="index.php?loc=newpost">New Post</a></li>
          <?php if($_SESSION['type'] === 'A'): ?>
          <li><a href="index.php?loc=panel">Admin Panel</a></li>
          <?php else: ?>
          <li><a href="index.php?loc=panel">User Panel</a></li>
          <?php endif; ?>
          <li><a href="index.php?loc=users">List of Users</a></li>
          <li><a href="logout.php?f=y">Logout</a></li>
          <?php else: ?>
          <li><a href="index.php?loc=register">Register</a></li>
          <li><a href="index.php?loc=users">List of Users</a></li>
          <?php endif; ?>
        </ul>
      </div>
      <article id="logbox">
        <?php 
 if(isset($_SESSION['logged'])):
 ?>
        <section id="loggedonbox">
          <p>You are logged in as <?php echo $_SESSION['logged']; ?>.
            <?php if($_SESSION['type'] === 'A') echo '<br>You are an Admin.'; ?>
          <form class="form" id="logoutform" method="post" action="">
            <input type="submit" value="Logout">
          </form>
        </section>
        <?php else: ?>
        <section id="loginbox">
          <form class="form" id="loginform" method="post" action="">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" name="upwd" id="upwd" placeholder="Password" required>
            <div class="clearfix"></div>
            <span>
            <input type="checkbox" name="remember_login" id="remember_login">
            <label for="remember_login">Remember me</label>
            </span>
            <input type="submit" value="Login">
            <div class="clearfix"></div>
          </form>
          <p>Don't have an account?<br>
            Register <a href="index.php?loc=register">here.</a></p>
          <div class="clearfix"></div>
        </section>
        <?php endif; ?>
      </article>
    </nav>
  </header>
  <section>
    <div id="phpdiv">
    <?php 
	if(!isset($_GET['loc'])) {
	loadPosts(); 
	} else {
		switch($_GET['loc']) {
			case 'showpost':
			if (isset($_GET['pa']) && isset($_GET['pd'])) {
			showpost($_GET['pa'], $_GET['pd']);
			} else {
				goto defaultlabel;
			}
			break;
			case 'reply':
			if (isset($_GET['pa']) && isset($_GET['pd'])) {
			include('pages/ccreply.php');			
			} else {
				goto defaultlabel;
			}
			break;
			case 'editpost':
			if (isset($_GET['pa']) && isset($_GET['pd'])) {
			include('pages/ccedit.php');			
			} else {
				goto defaultlabel;
			}
			break;
			case 'edituser':
			if(isset($_GET['uid'])) {
				include('pages/ccedituser.php');
			} else {
				goto defaultlabel;
			}
			break;
			case 'users':
			include('pages/ccusers.php');
			break;
			case 'register':
			include('pages/ccregister.php');
			break;
			case 'newpost':
			include('pages/ccnewpost.php');
			break;
			default:
			defaultlabel:
			echo '<b>Wrong URL format</b>';
			break;
		}
		
	}?>
    
    
    </div>
  </section>
  <footer>&copy; 2014</footer>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 
<script src="js.js"></script>
</body>
</html>
