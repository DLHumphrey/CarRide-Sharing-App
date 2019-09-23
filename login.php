<?php
  define('DB_SERVER', 'localhost');
  define('DB_USERNAME', 'testuser');
  define('DB_PASSWORD', 'test123');
  define('DB_NAME', 'carsharing');

  $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
  if ($link === false) {
   echo "Connected unsuccessfully";
    die("Connection failed: " . mysqli_connect_error());
  }
  
  $username = $password = "";
  $username_err = $password_err = "";

  if($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["username"])) {
      $username_err = 'Please enter username:';
   }  else {
    $username =  $_POST["username"];
   }
 
   if (empty($_POST["password"])) {
      $password_err = 'Please enter password:';
   }  else { 
    $password =  $_POST["password"];
   }
 // echo "username:".$username;
   if (empty($username_err) && empty ($password_err)) {
    $sql = "SELECT Username, Password FROM User WHERE Username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
	mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $username;
    
    if (mysqli_stmt_execute($stmt)) {
	mysqli_stmt_store_result($stmt);
	
	if (mysqli_stmt_num_rows($stmt)==1) {
	  //check password.
//	 echo "User found.";
	 mysqli_stmt_bind_result($stmt, $username, $hashed_password);
	 if (mysqli_stmt_fetch($stmt)) {
//	  echo "User:".$username;
	if(password_verify($password, $hashed_password)) {
	  echo "Login successul";	
	  session_start();
	  $_SESSION['username'] = $username;
	  header("location: welcome.php");
	} else {
	   $password_err = 'The password input is incorrect.';
	}
	
	} else {
	  echo "Error in fetch";
	}

        } else { //end if stmt_num_row
	  $username_err = 'No account found with the username.';
	}
	
    } else {//end if (mysqli_stmt_execute
       
     }

     } else { // end if stmt
     } 

  } //end if empty.


  } //end  if($_SERVER["REQUEST_METHOD"]



    mysqli_close($link);
?>
<!DOCTYPE html>
<head>
  <title>UPB Car Sharing System</title>
</head>
<body>
<div>
  <h2>Login</h2>
  <p>Please fill in your credential to login.</p>
  <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
   <div>
      <label>Username</label>
      <input type="text" name="username" value="<?php echo $username; ?>">
      <span><?php echo $username_err; ?></span>
   </div>
   <div>
      <label>Password</label>
      <input type="password" name="password">
      <span><?php echo $password_err; ?></span>
   </div>
   <div>
     <input type="submit" value="Login">
   </div>
  </form>
</div>
<p>Don't have an account? <a href="register.php">Register now.</a> </p>
</body>
</html>
