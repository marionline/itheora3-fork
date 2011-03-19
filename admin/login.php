<?php
session_start();
if(isset($_SESSION['login'])){
    header('Location: index.php');
    exit;
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
	<title>Itheora3-fork Login form</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body>
	<p><a href="../">Go to demo page</a></p>
	<h1>Itheora3-fork Admin Login</h1>
	<form action="index.php" method="post">
	    <div style="text-align : left; margin-left : 100px">
		<p><strong>Username: </strong><br /><input name="admin_username" type="text" value="" /></p>
		<p><strong>Password: </strong><br /><input name="admin_pass" type="password" /></p>
		<p><input class="submit" type="submit" value="Send" /></p>
	    </div>
	</form>
    </body>
</html>
<?php
}
