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
  
  $username = $_SESSION['username'];
  $license = "";
  $warning_msg = "";


  $sql = "SELECT UserName, License FROM CarInfo WHERE UserName = ?";
  if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_username);
    $param_username =  $_SESSION['username'];
    
    if (mysqli_stmt_execute($stmt)) {
       mysqli_stmt_store_result($stmt);
       if (mysqli_stmt_num_rows($stmt) == 1) {
	 mysqli_stmt_bind_result($stmt, $username, $license);
         mysqli_stmt_fetch($stmt);
	 //echo $username, $license; //debug
       } else {
	  header("location: CarInfo.php");
          exit;
       }

    }    


  }
  mysqli_stmt_close($stmt);
//  echo $username;
  if(isset($_POST['exit'])) {
    mysqli_close($link);
    header("location: welcome.php");
  }
  
  if(isset($_POST['rideSubmit']))  {
    //echo $_POST['start'],$_POST['end']; //debug
    $dtPattern="#^2018-(0[1-9]|1[0-2])-([0-2][1-9]|3[0-1]) ([0-1][0-9]|2[0-3]):([0-5][0-9])$#";
    if (preg_match($dtPattern, $_POST['start'])!=1 || preg_match($dtPattern, $_POST['end'])!=1) {
        $warning_msg="Input format should be: YYYY-MM-DD HH:MI, Must be in 2018.";
    }
    else {
        //TODO: validate year, month, day, hour FINISHED
        $format = 'Y-m-d H:i:s';
        $param_start = DateTime::createFromFormat($format, $_POST['start'].':00')->format('Y-m-d H:i:s');
        $param_end = DateTime::createFromFormat($format, $_POST['end'].':00')->format('Y-m-d H:i:s');
        if ($param_start >= $param_end) {
            $warning_msg="End datetime must be larger than start datetime";
        }
//TODO: Compare start/end with now. "You can't provide a ride in the past." FINISHED
	$current_time = date('Y-m-d H:i:s', time());
	if ($param_start < $current_time){
	    $warning_msg="Start time must be later than the current time. You can't provide a ride in the past.";
	}

        if (empty($warning_msg)) {
            $sql = "INSERT INTO RideAvailable VALUES (?, ?, ?, ?)";
            if($stmt = mysqli_prepare($link, $sql)) {
               mysqli_stmt_bind_param($stmt, "ssss", $username, $license, $param_start, $param_end);
               mysqli_stmt_execute($stmt);
            }
        }
    }
    mysqli_stmt_close();
    mysqli_close($link);
}//if isset

?>

<!DOCTYPE html>
<head>
  <title>UPB Car Sharing System</title>
</head>
<body>
 <h2><?php echo $_SESSION['username']."'s";?> providing a carride.</h2>
 <br>
 <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
    <div>
	<label style="width: 600px"> Start Datetime (format: YYYY-MM-DD HH:MI):</label>
	<input type="text" name="start">
    </div>
    <div>
	<label style="width: 600px"> End Datetime (format: YYYY-MM-DD HH:MI):</label>
	<input type="text" name="end">
    </div>
    <div>
        <h4><?php echo $warning_msg?></h4>
    </div>
    <br>
    <input type="submit" name="rideSubmit" value="Submit">
    <input type="submit" name="exit" value="Exit">
 </form>
</body>
</html>
