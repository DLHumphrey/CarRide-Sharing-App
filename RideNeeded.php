<?php
session_start();
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'testuser');
define('DB_PASSWORD', 'test123');
define('DB_NAME', 'carsharing');
 
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
	$warning_msg = "";
$result=null;
    if(isset($_POST['exit'])) {
	 //TODO: close current page and return to main menu FINISHED
   	 header("location: welcome.php");
   	 mysqli_close($link);  
    } //end todo close
    if(isset($_POST['rideSubmit'])) {
	$dtPattern="#^20[0-1][0-9]-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]$#";

//TODO: Validate datetimes and set warning_msg; FINISHED
    $format = 'Y-m-d H:i:s';
    $param_start = DateTime::createFromFormat($format, $_POST['start'].':00')->format('Y-m-d H:i:s');
    $param_end = DateTime::createFromFormat($format, $_POST['end'].':00')->format('Y-m-d H:i:s');
    if ($param_start >= $param_end) {
         $warning_msg="End datetime must be larger than start datetime!";
    } //end todo validate

    $current_time = date('Y-m-d H:i:s', time());
    if ($param_start < $current_time){
	$warning_msg="Start time must be later than the current time. You can't check rides needed in the past.";
    }

	if (empty($warning_msg)) {        
	$sql = "SELECT Username, Start, End FROM RideNeeded WHERE Start >= '$param_start' and End <= '$param_end'";
	$result = mysqli_query($link, $sql);	
	} //end of warning_msg;
  } //if set  
?> 
<!DOCTYPE html>
<head>
    <title>UPB Car Sharing System</title>
</head>
<body>
	<h2>Check carrides needed.</h2>

        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
           <div>
                <label style="width: 500px">Start Datetime (format: YYYY-MM-DD HH:MI): </label>
                <input type="text" name="start">
            </div> 
           <div>
                <label style="width: 500px">End Datetime (format: YYYY-MM-DD HH:MI): </label>
                <input type="text" name="end">
            </div> 
            <div> 
		<h4><?php echo $warning_msg; ?></h4>
            </div> 
<br>
<?php
if ($result != null) {
//TODO: output RideNeeded and User information.
      echo '<table border=1>'; 
      echo '<tr> <th> Start </th>';
      echo ' <th> End </th>';
      echo ' <th> UserName </th>';
      echo ' <th> Phone </th>';
      echo ' <th> Email </th></tr>';     

	while ($row = mysqli_fetch_assoc($result)) {
      if (mysqli_num_rows($result) >0 ) {

	$rowUsername = $row["Username"];
	$sql1 = "SELECT PhoneNumber, Email FROM User WHERE UserName = '$rowUsername'";
	$result1 = mysqli_query($link, $sql1);
	$row1 = mysqli_fetch_assoc($result1);

	echo '<tr><td>', $row["Start"], 
'</td> <td>', $row["End"], 
'</td> <td>', $rowUsername,
'</td> <td>', $row1["PhoneNumber"],  
'</td> <td>', $row1["Email"], 
'</td> </tr>'; 
	}
}
echo '</table>';
      } else {
        echo "0 results";
      }
?> 
</br>
	    <input type="submit" name="rideSubmit" value="Submit">
            <input type="submit" name="exit" value="Exit">
        </form>
</body>
</html>
