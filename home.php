<!-- Alexander Monaco & Shreyas Mehta
CS 4640
-->

<?php session_start(); // make sessions available ?>
<?php

if (!isset($_SESSION['user']))
{
	header('Location: signin.php?error=notloggedin');
}
	require("load_user_db.php");
	$user=$_SESSION['user'];

	//get all contacts in user's table
    $query = "SELECT * FROM $user ORDER BY 'Birthday' ASC";
    $statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();

function printResults($results){
	$finalresultarray=array();

	//get today's date
	$today1= date("m/d");
	$today = new DateTime($today1);

	//go through each contact and if birthday is within a week from now, add to result array.
	//determine if in week by getting difference between today and birthday
	foreach($results as $row){
		$daymonthbday=substr(str_replace("-", "/", $row['Birthday']), 5);
		$timestamp = date($daymonthbday);
	    $bday = new DateTime($timestamp);
		//help from https://www.php.net/manual/en/datetime.diff.php
		$interval=$today->diff($bday);
		$diffint=intval($interval->format('%R%a'));
		if($diffint<=6 && $diffint>=-1){
			$timestamp = strtotime($row['Birthday']);
		    $formatted_date = date("m-d-Y", $timestamp);
			$finalresultarray[$row['Name']]=$formatted_date;
		}
    }
	//echo resulting bdays as list items (echo'd in html)
	if($finalresultarray!=null){
		asort($finalresultarray);
		$keys=array_keys($finalresultarray);
		foreach ($keys as $name) {
			echo "<li class=\"list-group-item\">".$name.": ".$finalresultarray[$name]."</li>";
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Birthday Reminder Profile</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />
	<link rel="stylesheet" href="main.css" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<!-- loads html files for navbar and calendar -->
	<script>
	$(function(){
		$("#header").load("navbar_header.html");
		$("#cal_temp").load("calendar.html");
		});
	</script>
	<link rel="stylesheet" href="calendar.css" />
</head>

<body>
<!-- header at the top of the page that welcomes back user -->
<div id="header"></div>
	<div class = "container">
		<div class= "text-center">
			<br>
			<h1>Welcome back, <?php echo $_SESSION['user'];?>!</h1>
			<hr/>
			<h3>Upcoming Birthdays</h3>
		</div>
	</div>

<div class="container">
<br>
	<div class="row justify-content-center">
	<!-- calendar section -->

		<div class="col-4">

		<!-- list view section; will have multiple pages when back-end is implemented. -->
			<div class= "text-center">Birthdays in the next week</div>
				<ul class="list-group">
				  <?php printResults($results); ?>
				</ul>
				<div class="d-flex justify-content-center">
				<ul class="pagination">
					<li class="page-item"><a class="page-link" href="#">Previous</a></li>
					<li class="page-item"><a class="page-link" href="#">Next</a></li>
				</ul>
				</div>
		</div>
	</div>
	<br>
	<!-- button that takes you to address book -->
	<div class="row justify-content-md-center">
		<div class="col col-lg-4">
			<button type="button" id="address_button" class="btn btn-block btn-primary btn-lg">Create/Edit a Contact</button>
		</div>
		<script type="text/javascript">
			var adr_button = document.getElementById("address_button");
			adr_button.onclick = () => window.location.href= "newdate.php";
		</script>
	</div>
</div>
<br>
</body>
</html>
