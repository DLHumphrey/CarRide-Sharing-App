<?php
session_start();
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
 header("location: login.php");
 exit;
}

if(isset($_GET['provide'])){
 header("location: ProvideARide.php");
 exit;
}

if(isset($_GET['available'])){
 header("location: RideAvailable.php");
 exit;
}

if(isset($_GET['need'])){
 header("location: NeedARide.php");
 exit;
}

if(isset($_GET['needed'])){
 header("location: RideNeeded.php");
 exit;
}

?>

<!DOCTYPE html>
<head>
  <title>UPB Car Sharing System</title>
</head>
<body>
<h2>Hi, <b><?php echo $_SESSION['username']; ?></b>. Welcome to our site.</h2>
<form >
<h3>Continue as a car owner:</h3>
<input type="submit" value="Check rides needed" name="needed" style="font-family: sans-serif; width: 500px; font-size: 32px;">
<input type="submit" value="Provide a ride" name="provide" style="font-family: sans-serif; width: 500px; font-size: 32px;">
<br><br>
<h3>Continue as a car rider:</h3>
<input type="submit" value="Check rides available" name="available" style="font-family: sans-serif; width: 500px; font-size: 32px;">
<input type="submit" value="Need a ride" name="need" style="font-family: sans-serif; width: 500px; font-size: 32px;">
</form>
<br><br>
<p><a href="logout.php">Sign Out of Your Account</a></p>
</body>
</html>
