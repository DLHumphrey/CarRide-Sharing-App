
<?php
session_start();

  define('DB_SERVER', 'localhost');
  define('DB_USERNAME', 'testuser');
  define('DB_PASSWORD', 'test123');
  define('DB_NAME', 'carsharing');

  $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
  if ($link === false) {
   echo "Connected unsuccessfully";
    die("Connection failed: " . mysqli_connect_error());
  } 

$make_err="";
$model_err="";
$year_err="";
$rate_err="";

if(empty(trim($_POST["make"]))){
$make_error = "Car make text cannot be empty.";
} else{
$make = trim($_POST["make"]);
$make_pattern = "#^[[:alpha:]]{2,20}$#";
   if(preg_match($make_pattern, $make)!=1){

$make_err = "The car make text length must be between 2 and 20 characters";
}//end of pregmatch
}//end validation

if(empty(trim($_POST["model"]))){
$model_error = "Car model text cannot be empty.";
} else{
$model = trim($_POST["model"]);
$model_pattern = "#^[[:alnum:]]{2,20}$#";
   if(preg_match($model_pattern, $model)!=1){

$model_err = "The car model text length must be between 2 and 20 characters";
}//end of pregmatch
}//end validation

if(empty(trim($_POST["carYear"]))){
$year_error = "Car year text cannot be empty.";
} else{
$year = trim($_POST["carYear"]);
$year_pattern = "#^19[789]\d|20[01]\d$#";
   if(preg_match($year_pattern, $year)!=1){

$year_err = "The year must be between 1970 and 2018";
}//end of pregmatch
}//end validation

if(empty(trim($_POST["rate"]))){
$rate_error = " Rate text cannot be empty.";
} else{
$rate = trim($_POST["rate"]);
 
   if($rate<1 || $rate >20){

$rate_err = "The rate must be between 1 and 20";
}//end of pregmatch
}//end validation

  $license = "";
  $license_err = "";
  if (isset($_POST['carInfoSubmit'])) {
    if(empty(trim($_POST['license']))) {
      $license_err = "License plate cannot be empty.";
    }
    else {
$param_license = trim($_POST['license']);
    }

 //   echo "$param_license"; TODO: set other error messages.
    if (empty($license_err) && empty($make_err) && empty($model_err) && empty($year_err) && empty($rate_err)) {
  $sql = "INSERT INTO CarInfo VALUES(?, ?, ?, ?, ?, ?)";
if($stmt = mysqli_prepare($link, $sql)) {
     mysqli_stmt_bind_param($stmt, "ssssid", $param_username, $param_license, $param_make, $param_model, $param_carYear, $param_rate);
   $param_username = $_SESSION['username'];
   
  if(empty(trim($_POST['make']))) {
$param_make = null;
   } else {
$param_make = trim($_POST['make']);
   }

   if(empty(trim($_POST['model']))) {
$param_model = null;
   } else {
$param_model = trim($_POST['model']);
   }

   if(empty(trim($_POST['carYear']))) {
$param_carYear= null;
   } else {
$param_carYear = trim($_POST['carYear']);
   }

   if(empty(trim($_POST['rate']))) {
$param_rate = null;
   } else {
$param_rate = trim($_POST['rate']);
   }
   echo $param_username, $param_license, $param_make, $param_model, $param_carYear, $param_rate;
   if(mysqli_stmt_execute($stmt)) {
header("location: welcome.php");
   } else {
echo "Data insertion error.";
   }
   mysqli_stmt_close($stmt);
        }

   }

  }
?>
<!DOCTYPE html>
<head>
  <title>UPB Car Sharing System</title>
</head>
<body>
<h2><?php echo $_SESSION['username']."'s";?> Car Information:</h2>
<form action="<?php echo $SERVER['PHP_SELF'];?>"method="post">
<div>
<label>Make</label>
<input type = "text" name = "make" value="<?php echo $make; ?>">
<span> <?php echo $make_err; ?> </span>
</div>

<div>
<label>Model</label>
<input type = "text" name = "model" value="<?php echo $model; ?>">
<span> <?php echo $model_err; ?> </span>
</div>

<div>
<label>Year</label>
<input type = "text" name = "carYear" value="<?php echo $year; ?>">
<span> <?php echo $year_err; ?> </span>
</div>
 
<div>
<label>License Plate</label>
<input type = "text" name = "license">
<span> <?php echo $license_err; ?> </span>
</div>
<div>
<label>Rate</label>
<input type = "text" name = "rate" value="<?php echo $rate; ?>">
<span> <?php echo $rate_err; ?> </span>
</div>

<div>
<input type = "submit" name = "carInfoSubmit" value = "Submit">
</div>

</form>
</body>
</html>

