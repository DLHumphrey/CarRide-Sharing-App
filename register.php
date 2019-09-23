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
  $confirm_password_err = "";

  $first_name_err = "";
  $last_name_err = "";
  $email_err = "";
  $phone_err = "";
  $addr_err = "";
  $id_card_num_err = "";

  if($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["username"])) {
    $username_err = 'Please enter username:';
   }  else {
    $sql = "SELECT Username FROM User WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
     mysqli_stmt_bind_param($stmt, "s", $param_username);
     $param_username = trim($_POST["username"]);
     if (mysqli_stmt_execute($stmt)){
      mysqli_stmt_store_result($stmt);
	
	if (mysqli_stmt_num_rows($stmt)==1) {
	  $username_err = "This username already exists.";
        } else { //end if stmt_num_row
	  $username = trim($_POST["username"]); //$username is input username
	}
     } //end if mysqli_stmt_execute
    } else { //end if $stmt

    } //end if ($stmt = mysqli_prepare
  
   } //end if empty($_POST["username"]) 

   if (empty(trim($_POST["password"]))) {
      $password_err = 'Please enter password:';
   }  else { //TODO: check password length and classes:lower/upper/number/special
    $password =  trim($_POST["password"]);
    $pwPattern = "#^(?:(?=.*[a-z])(?:(?=.*[A-Z])(?=.*[\d\W])|(?=.*\W)(?=.*\d))|(?=.*\W)(?=.*[A-Z])(?=.*\d)).{6,20}$#"; //pw reg ex
    if (preg_match($pwPattern, $password)!=1) {
       $password_err = "Password must contain at least 1 lowercase letter, 1 uppercase letter, 1 number, 1 special character, and be between 6 and 20 characters.";
    } //end preg match
   }

   if (empty(trim($_POST["confirm_password"]))) {
      $confirm_password_err = 'Please enter confirm password:';
   }  else { //TODO: check password length and classes:lower/upper/number/special
    $confirm_password =  trim($_POST["confirm_password"]);
    if ($confirm_password != $password) {
     $confirm_password_err = 'Passwords do not match.';
    }

   }
   
   //TODO: validate other input values (use reg exp and stuff)

   if (empty(trim($_POST["fName"]))) {
      $first_name_err = 'Please enter a First Name:';
   }  else {
    $first_name =  trim($_POST["fName"]);
    $fnPattern = "#^[[:alpha:]]{2,20}$#"; // reg ex
    if (preg_match($fnPattern, $first_name)!=1) {
       $first_name_err = "Please enter an alphabetic first name between 2 and 20 characters.";
    } //end preg match
   } //end first name validation
   
   if (empty(trim($_POST["lName"]))) {
      $last_name_err = 'Please enter a Last Name:';
   }  else {
    $last_name =  trim($_POST["lName"]);
    $lnPattern = "#^[[:alpha:]]{2,20}$#"; // reg ex
    if (preg_match($lnPattern, $last_name)!=1) {
       $last_name_err = "Please enter an alphabetic last name between 2 and 20 characters.";
    } //end preg match
   } //end  last name validation

   if (empty(trim($_POST["email"]))) {
      $email_err = 'Please enter an Email:';
   }  else {
    $email =  trim($_POST["email"]);
    $emailPattern = "#^[[:alnum:].]{1,20}@pitt.edu$#"; // reg ex
    if (preg_match($emailPattern, $email)!=1) {
       $email_err = "Please enter a valid email address ending with '@pitt.edu'.";
    } //end preg match
   } //end  email validation

   if (empty(trim($_POST["phone"]))) {
      $phone_err = 'Please enter a Phone Number:';
   }  else {
    $phone =  trim($_POST["phone"]);
    $phonePattern = "#^(\+[[:digit:]]{1,2}[[:space:]])?\(?\d{3}\)?[[:space:].-][[:digit:]]{3}[[:space:].-][[:digit:]]{4}$#"; // reg ex
    if (preg_match($phonePattern, $phone)!=1) {
       $phone_err = "Please enter a valid phone number using the format '000-000-0000'.";
    } //end preg match
   } //end phone validation

   if (empty(trim($_POST["addr"]))) {
      $addr_err = 'Please enter a valid Address:';
   }  else {
    $addr =  trim($_POST["addr"]);
    $addrPattern = "#^\d{1,5}\s(\b\w*\b\s){1,4}\w*$#"; // reg ex
    if (preg_match($addrPattern, $addr)!=1) {
       $addr_err = "Please enter a valid Address including a house number followed by street name.";
    } //end preg match
   } //end address validation

   if (empty(trim($_POST["idNum"]))) {
      $id_card_num_err = 'Please enter a valid ID Card Number (1-9999999):';
   }  else {
    $id_card_num =  trim($_POST["idNum"]);
    $idNumPattern = "#^[[:digit:]]{1,7}$#"; // reg ex
    if (preg_match($idNumPattern, $id_card_num)!=1) {
       $id_card_num_err = "Please enter a valid ID Card Number between 1 and 9999999.";
    } //end preg match
   } //end  id card num validation



   if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($phone_err) && empty($addr_err) && empty($id_card_num_err)) {
    $sql = "INSERT INTO User VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
     mysqli_stmt_bind_param($stmt, "sssssssss", $param_username, $param_password, $param_fname, $param_lname, $param_email, $param_phone, $param_addr, $param_idcard, $param_idnum);
     $param_username = $username;
     $param_password = password_hash($password, PASSWORD_DEFAULT);
    
     if(empty(trim($_POST["fName"]))){
      $param_fname = null;
     } else {
      $param_fname = trim($_POST["fName"]);
     }

     if(empty(trim($_POST["lName"]))){
      $param_lname = null;
     } else {
      $param_lname = trim($_POST["lName"]);
     }

     if(empty(trim($_POST["email"]))){
      $param_email = null;
     } else {
      $param_email = trim($_POST["email"]);
     }

     if(empty(trim($_POST["phone"]))){
      $param_phone = null;
     } else {
      $param_phone = trim($_POST["phone"]);
     }

     if(empty(trim($_POST["addr"]))){
      $param_addr = null;
     } else {
      $param_addr = trim($_POST["addr"]);
     }

     if(empty(trim($_POST["idCard"]))){
      $param_idcard = null;
     } else {
      $param_idcard = trim($_POST["idCard"]);
     }

     if(empty(trim($_POST["idNum"]))){
      $param_idnum = null;
     } else {
      $param_idnum = trim($_POST["idNum"]);
     }

     if(mysqli_stmt_execute($stmt)){
      //echo "Continue...";
      header("location: login.php");
     } else {
      echo "Something is wrong in stmt execute";
     }

    } //end if $stmt

   } //end if errors

  } //end if($_SERVER["REQUEST_METHOD"]

?>

<!DOCTYPE html>
<head>
 <title>UPB Car Sharing System</title>
</head>
<body>
<div>
 <h2>Register</h2>
 <p>Please fill in this form to create an account.</p>
 <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
  <div>
   <label>Username</label>
   <input type="text" name="username" value="<?php echo $username; ?>"
   <span><?php echo $username_err; ?></span>
  </div>
  <div>
   <label>Password</label>
   <input type="password" name="password" value="<?php echo $password; ?>">
   <span><?php echo $password_err; ?></span>
  </div>
  <div>
   <label>Confirm Password</label>
   <input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
   <span><?php echo $confirm_password_err; ?></span>
  </div>
  <div>
   <label>First Name</label>
   <input type="text" name="fName" value="<?php echo $first_name; ?>">
   <span><?php echo $first_name_err; ?></span>
  </div>
  <div>
   <label>Last Name</label>
   <input type="text" name="lName" value="<?php echo $last_name; ?>">
   <span><?php echo $last_name_err; ?></span>
  </div>
  <div>
   <label>Email</label>
   <input type="text" name="email" value="<?php echo $email; ?>">
   <span><?php echo $email_err; ?></span>
  </div>
  <div>
   <label>Phone</label>
   <input type="text" name="phone" value="<?php echo $phone; ?>">
   <span><?php echo $phone_err; ?></span>
  </div>
  <div>
   <label>Address</label>
   <input type="text" style="width: 500px" name="addr" value="<?php echo $addr; ?>">
   <span><?php echo $addr_err; ?></span>
  </div>
  <div>
   <label>ID Card Type:</label>
   <input type="radio" name="idCard" value="Driver License" checked>Driver License
   <input type="radio" name="idCard" value="Student Card">Student/Employee Card
   <input type="radio" name="idCard" value="Passport">Passport
  </div>
  <div>
   <label>ID Card Number:</label>
   <input type="text" name="idNum" value="<?php echo $id_card_num; ?>">
   <span><?php echo $id_card_num_err; ?></span>
  </div>
  <div>
   <input type="submit" value="Submit">
   <input type="reset" value="Reset">
  </div>
 </form>
</div>
<p>Already have an account? <a href="login.php">Login now.</a></p>
</body>
</html>
