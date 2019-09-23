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

  if(isset($_POST['exit'])) {
    header("location: welcome.php");
    mysqli_close($link); 
  }

  if(isset($_POST['rideSubmit']))  {
//    echo $_POST['start'],$_POST['end'];
  $dtPattern = "#^2018-(0[1-9]|1[0-2])-([0-2][1-9]|3[0-1]) ([0-1][0-9]|2[0-3]):([0-5][0-9])$#";	
  if (preg_match($dtPattern, $_POST['start'])!=1 || preg_match($dtPattern, $_POST['end'])!=1) {
	$warning_msg="Input format should be: YYYY-MM-DD HH:MI, Must be in 2018.";
  }   
  else {
//TODO: validate year,month, day, hour FINISHED
    $format = 'Y-m-d H:i:s';
    $param_start = DateTime::createFromFormat($format, $_POST['start'].':00')->format('Y-m-d H:i:s');
    $param_end = DateTime::createFromFormat($format, $_POST['end'].':00')->format('Y-m-d H:i:s');
    if ($param_start >= $param_end) {
         $warning_msg="End datetime must be larger than start datetime!";
    } 
    $current_time = date('Y-m-d H:i:s', time());
    if ($param_start < $current_time){
	$warning_msg="Start time must be later than the current time. You can't check rides available in the past.";
    }
    if (empty($warning_msg)) {
	$sql = "SELECT UserName, License, Start, End FROM RideAvailable WHERE Start >= '$param_start' AND End <= '$param_end'";     
	$result = mysqli_query($link, $sql);   	  
//echo  mysqli_num_rows($result);  debug
        }
    }

  }
  mysqli_stmt_close();
  
?>
<!DOCTYPE html>
<head>
  <title>UPB Car Sharing System</title>
</head>
<body>
 <h2>Check available carrides.</h2>
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
	<h4><?php echo $warning_msg; ?></h4>
    </div>
    <br>
    <?php
      if ($result != null) {
      echo '<table border=1>'; 
      echo '<tr> <th> Start </th>';
      echo ' <th> End </th>';
      echo ' <th> UserName </th>';
      echo ' <th> Phone </th>';
      echo ' <th> Email </th>';
      echo ' <th> Car </th>';
      echo ' <th> Rate </th> </tr>';      

	while ($row = mysqli_fetch_assoc($result)) {
      if (mysqli_num_rows($result) >0 ) {

	$rowUsername = $row['UserName'];
	$sql1 = "SELECT PhoneNumber, Email FROM User WHERE UserName = '$rowUsername'";
	$result1 = mysqli_query($link, $sql1);
	$row1 = mysqli_fetch_assoc($result1);

	$rowLicense = $row['License'];
	$sql = "SELECT Make, Model, Year, Rate FROM CarInfo WHERE License = '$rowLicense'";
	$result2 = mysqli_query($link, $sql);
	$row2 = mysqli_fetch_assoc($result2);
	
	echo '<tr><td>', $row["Start"], '</td> <td>', $row["End"], '</td> <td>', $rowUsername,
'</td> <td>', $row1["PhoneNumber"],  
'</td> <td>', $row1["Email"], 
'</td> <td>', $row2["Year"]." ".$row2["Make"]." ".$row2["Model"],
'</td> <td>', $row2["Rate"], 
'</td> </tr>'; 
	}
}
echo '</table>';
      } else {
        echo "0 results";
      }

    ?>
    <input type="submit" name="rideSubmit" value="Submit">
    <input type="submit" name="exit" value="Exit">
 </form>
</body>
</html>
