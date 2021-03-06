<!-- Used Bootstrap and Google api documentation as sources -->
<!-- Alexander Monaco and Shreyas Mehta
CS4640 -->

<?php session_start();
?>

<?php


if (isset($_SESSION['user'])) //prevents user from visiting this page if they are already logged in
{
	header('Location: home.php');
}


function reject($entry)//rejection function
{
   exit();
}

function error_msg($error)//prints error message if there is an issue with logging in
{
	$msg = "";
	switch($error){
		case "credentials":
			$msg = "Invalid username/password combination. Please try again.";
			break;
		case "usertaken":
			$msg = "Username already taken. Please try another username.";
			break;
		case "emailtaken":
			$msg = "Email already taken. Please try another email.";
			break;
		case "notloggedin":
			$msg = "You must be logged in to use this service. Please login or create an account on this page.";
			break;
	}
	echo "<script>alert('$msg');</script>";
}

if( isset($_GET["error"])){//gets error
	error_msg($_GET["error"]);
}


if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST["action"] == "signin")//signin request
{
	require("load_user_db.php");
   $user = trim($_POST['signinuser']);
   $pass = trim($_POST['signinpass']);
   
   $user = strtolower($user);


	$query = "SELECT * FROM user_info WHERE username='$user' AND password='$pass'";
	$statement = $db->prepare($query);
	$statement->execute();
	$result = $statement->fetch();

	$statement->closeCursor();

	if (strlen($result['username']) > 0 && strlen($result['password']) > 0){ //checks if username/password combo is valid
         $_SESSION['user'] = $user;

         // $hash_pwd = md5($pwd);
		 // $hash_pwd = password_hash($pwd, PASSWORD_DEFAULT);
		 // $hash_pwd = password_hash($pwd, PASSWORD_BCRYPT);

         $_SESSION['pwd'] = $pass;

		 $_SESSION['email'] = $result['email'];
		 $_SESSION['phone'] = $result['phone'];

         header('Location: home.php');
	}else{
		header('Location: signin.php?error=credentials'); //rejects if not an account
	}

}else if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST["action"] == "create"){ //create account request
	require("load_user_db.php");

	$user = trim($_POST['createuser']);
	$pass = trim($_POST['createpass']);
	$email = trim($_POST['email']);
	$phone = trim($_POST['createphone']);
	
	$user = strtolower($user);

	$query = "SELECT * FROM user_info WHERE username='$user'";
	$statement = $db->prepare($query);
	$statement->execute();
	$result = $statement->fetch();

	$statement->closeCursor();
	if (strlen($result['username']) > 0){
		header('Location: signin.php?error=usertaken');

	}else{//checks if email is duplicate
		$query2 = "SELECT * FROM user_info WHERE email='$email'";
		$statement2 = $db->prepare($query2);
		$statement2->execute();
		$result2 = $statement2->fetch();

		$statement2->closeCursor();
		$result2 = $statement2->fetch();

		echo $result2;

		if (strlen($result['email']) > 0){
			header('Location: signin.php?error=emailtaken');
		}
	}
	$query3 = "INSERT INTO `user_info`(`username`, `password`, `email`, `phone`) VALUES ('$user','$pass','$email','$phone')";
	$statement3 = $db->prepare($query3);
	$statement3->execute();
	$statement3->closeCursor();

	$_SESSION['user'] = $user;

	 // $hash_pwd = md5($pwd);
	 // $hash_pwd = password_hash($pwd, PASSWORD_DEFAULT);
	 // $hash_pwd = password_hash($pwd, PASSWORD_BCRYPT);

	 $_SESSION['pwd'] = $pass;

	 $_SESSION['email'] = $email;
	 $_SESSION['phone'] = $phone;


	$query4 = "CREATE TABLE $user (Name varchar(11), Birthday date NOT NULL, Email tinyint(1) NOT NULL DEFAULT 0, SMS tinyint(1) NOT NULL DEFAULT 0, Desk tinyint(1) NOT NULL DEFAULT 0)"; //creates table for each user for storing birthdays
	$statement4 = $db->prepare($query4);
	$statement4->execute();
	$statement4->closeCursor();
	 // redirect the browser to another page using the header() function to specify the target URL
	 header('Location: home.php');
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">  <!-- required to handle IE -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://apis.google.com/js/platform.js" async defer></script>
        <meta name="google-signin-client_id" content="http://localhost.apps.googleusercontent.com">
        <title>Sign In</title>
    </head>
    <body>
        <style>
            .container{
                padding-bottom: 100px;
            }
            .container2{
                padding-bottom: 0px;
            }
            .jumbotron{
                background:url("Images/calendar1.png");
                background-position: center;
                background-size: contain;
            }
            .display-4{
                text-align: center;
            }
            .display-6{
                text-align: center;
            }
            .p{
                text-align: left;
            }
            .btn-success{
                align-content: center;
            }
            .g-signin2 > div{
              margin: auto;
            }
        </style>
        <!-- Main header lable and about us button -->
        <div class = "container2">
            <div class = "jumbotron">
                <div class= "text-center">
                    <br><br>
                    <h1 class="display-2">Welcome to Birthday Reminder</h1>
                        <hr/>
                        <!-- Help from w3schools.com on how to link to document onClick below -->
                    <button type="button" class="btn btn-info btn-lg" onclick="document.location='aboutus.html'">About Us</button>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
					<button type="button" class="btn btn-info btn-lg" onclick="location.href='http://localhost:4200/'">Contact Us</button>
        		</div>
            </div>
       </div>
       <br>
       <div class = "container">
           <!-- sign in username/password inputs and google sign in option -->
           <h4 class="display-4">Sign in</h4>
           <br>
		   <form id="signin_form" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
			   <div class="input-group input-group-lg">
				   <div class="input-group-prepend">
					   <span class="input-group-text">Username</span>
				   </div>
				   <input type="text" class="form-control" name="signinuser" id="signinuser" oninput="signInBtnDis()">
			   </div>
			   <br>
			   <div class="input-group input-group-lg">
				   <div class="input-group-prepend">
					   <span class="input-group-text">Password</span>
				   </div>
				   <input type="text" class="form-control" name="signinpass" id="signinpass" oninput="signInBtnDis()">
			   </div>
			   <input class="btn btn-block btn-outline-success" disabled=true id="signinbtn" type="submit" value="Sign in">
			   <input name="action" value="signin" hidden>
			</form>
           <br>
           <!--<button type="button" class="btn btn-block btn-outline-success" id="signinbtn" disabled=true onclick="validateUser()">Sign In</button>-->
           <br>


           <h4 class="display-6">Or</h4>
           <br>
           <div class="g-signin2" data-onsuccess="onSignIn" data-width="400" data-height="60" data-longtitle="true"></div>
           <br><hr/><br>
           <!-- New account creation -->
           <h4 class="display-4">Create New Account</h4>
           <br>
		   <form id="createaccount" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
			   <div class="input-group input-group-lg">
				   <div class="input-group-prepend">
					   <span class="input-group-text">Username</span>
				   </div>
				   <input type="text" class="form-control" name="createuser" id="createuser" oninput="createBtnDis()">
			   </div>
			   <br>
			   <div class="input-group input-group-lg">
				   <div class="input-group-prepend">
					   <span class="input-group-text" >Email</span>
				   </div>
				   <input type="email" class="form-control" name="email" id="email" oninput="createBtnDis()">
			   </div>
			   <br>
			   <div class="input-group input-group-lg">
				   <div class="input-group-prepend">
					   <span class="input-group-text">Password</span>
				   </div>
				   <input type="password" class="form-control" name="createpass" id="createpass" oninput="createBtnDis()">
			   </div>
			   <p class="font-italic" id="passrules">Must be at least 8 chars long</p>
			   <p class="text-danger" id="errorcreate"> </p>
			   <div class="input-group input-group-lg">
				   <div class="input-group-prepend">
					   <span class="input-group-text">Tel</span>
				   </div>
				   <input type="tel" class="form-control" name="createphone" id="createphone" placeholder="(optional)">
			   </div>
			   <br>
			   <!--<button type="button" class="btn btn-block btn-outline-primary" onclick="passValid()" id="createBtn" disabled=true>Create Account</button>-->
			   <input class="btn btn-block btn-outline-primary" id="createbtn" type="submit" value="Create account">
			   <input name="action" value="create" hidden>
		   </form>



    </div>
    <script>
        document.getElementById("signinuser").focus(); //focus on first input on startup
        document.getElementById("createspinner").style.display = "none"; //hide loading spinner

        //Listens to input to user and pass. Enable/disable sign in button based on filled/empty fields
        function signInBtnDis(){
            user=document.getElementById("signinuser");
            pass=document.getElementById("signinpass");
            if(user.value!="" && pass.value!=""){
                document.getElementById("signinbtn").disabled=false;
            }else{
                document.getElementById("signinbtn").disabled=true;
            }
        }

        //Listens to input to user, email, and pass. Enable/disable create account button based on filled/empty fields
        function createBtnDis(){
            username=document.getElementById("createuser");
            pass=document.getElementById("createpass");
            email=document.getElementById("email");
            if(username.value!="" && pass.value!="" && email.value!=""){
                document.getElementById("createBtn").disabled=false;
            }else{
                document.getElementById("createBtn").disabled=true;
            }
        }
        //"Create account" password length validation (further rules in future)
        function passValid() { //currently only validates length (might add regex for capital/nums/special chars)
            pass=document.getElementById("createpass").value;
            if(pass.length<8){
                document.getElementById("errorcreate").innerHTML="Please enter a password that adheres to the rules shown above";
            } else{
                document.getElementById("errorcreate").innerHTML="";
                var spin= document.createElement("SPAN");
                spin.className="spinner-border spinner-border-sm";
                spin.role="status";
                document.getElementById("createBtn").innerHTML="Loading "
                document.getElementById("createBtn").appendChild(spin);
            }
        }
        //"Sign in" that validates username and password and signs in if valid
            //currently just loads for a second and then logs in
        function validateUser() { //currently no database to use to validate input
            user=document.getElementById("signinuser").value;
            pass=document.getElementById("signinpass").value;
            var spin= document.createElement("SPAN");
            spin.className="spinner-border spinner-border-sm";
            spin.role="status";
            document.getElementById("signinbtn").innerHTML="Loading "
            document.getElementById("signinbtn").appendChild(spin);
            // Insert code to check database to validate user information
            setTimeout(() => { document.location='profile.html'} ,800)
        }
    </script>




   </body>
 </html>
