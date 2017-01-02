<?php
	//db variables
	define( 'DB_SERVER', 'localhost' );
	define( 'DB_USER',   'root' );
	define( 'DB_PW',	 'root' );
	define( 'DB_NAME',   'swimming' );

	//registration variables
	$isAccountCreated = null;
	$isUsernameAvailable = null;
	$ruser_Name = null;
	$passwordMatch = null;
	$usernameMatch = null;
	$isPassMatch = null;

//blowfish
	function cryptPass($input, $rounds = 14)
	{
		$salt = "";
		$saltChars = array_merge(range('A','Z'), range('a','z'), range(0,9));

		for($i = 0; $i < 22; $i++)
		{
			$salt .= $saltChars[array_rand($saltChars)];
		}

		return crypt($input, sprintf('$2y$%02d$', $rounds) . $salt);
	}

	session_start();

	//DB Connection

	//change before putting website up
	error_reporting( E_ALL );
	mysqli_report( MYSQLI_REPORT_STRICT );

	try
	{
		$mysqli = new mysqli( DB_SERVER, DB_USER, DB_PW, DB_NAME );
		$connected = true;
	}
	catch (Exception $e)
	{
		$connected = false;
	}

	//Character Set UTF-8
	if($connected)
	{
		if (!$mysqli->set_charset('utf8'))
		{
			$connected = false;
		}
	}

//may want to get an email later for security
//may want to validate email and make sure only one is used/unique

//check if user is trying to register
if( isset($_POST[ 'rsubmit' ]) )
{
	//check if username and password are entered
	if( ( isset( $_POST[ 'ruser_Name' ] ) && !empty($_POST[ 'ruser_Name' ]) ) &&
			( isset( $_POST[ 'r_Password' ] ) && !empty($_POST[ 'r_Password' ]) )
		)
			{
				//checks if the first and second passwords match
				if(	strcmp($_POST[ 'c_Password' ], $_POST[ 'r_Password' ]) === 0)
				{

					$ruser_Name = $_POST[ 'ruser_Name' ];
					$r_Password = $_POST[ 'r_Password' ];
					//	$remail = $_POST[ 'remail' ];

					//sql to check if username is available
					$rsql = null;
					$rsql =  "SELECT Users.Username AS Usernames" .
							" FROM Users" .
							" WHERE Users.Username = ?";

					//Preparing
					$rstmt = $mysqli->prepare( $rsql );

					//binding parameter
					$rstmt->bind_param( "s", $ruser_Name);

					//execute
					$rstmt->execute();

					//bind results
					$unresults = null;
					$rstmt->bind_result($unresults);

					//store results to get properties
					$rstmt->store_result();

					//variable for username availability
					$isUsernameAvailable = $rstmt->num_rows === 0;

					//free results
					$rstmt->free_result();

					//checks if username is available and adds new user to table
					if($isUsernameAvailable)
					{
						$hasedPass = cryptPass($r_Password);

						//write sql to create username and password
						$isql = null;
						$isql = "INSERT INTO users (Username, Password)" .
								" VALUES ( ? , ? )";

						//preparing
						$istmt = $mysqli->prepare( $isql );

						//binding parameter
						$istmt->bind_param( "ss", $ruser_Name, $hasedPass);

						//execute
						$istmt->execute();

						$istmt->close();

						$isAccountCreated = TRUE;
					}
				}
				//The first and second passwords do not match
				else
				{
						$isPassMatch = FALSE;
				}
			}

}

//checks if user is trying to log in to account
if( isset($_POST[ 'lsubmit' ]) )
{
	//checks for both username and password to be entered
	if( ( isset( $_POST[ 'user_Name' ] ) && !empty($_POST[ 'user_Name' ]) ) &&
		( isset( $_POST[ 'password' ] ) && !empty($_POST[ 'password' ]) ) )
		{
			$user_Name = $_POST[ 'user_Name' ];
			$password = $_POST[ 'password' ];

			//gets the hased password that is in the database for this user
			//SQL to Prepare
			$sql = null;
			$sql = 'SELECT Users.Password AS Password' .
					' FROM Users' .
					' WHERE Users.Username = ?';

			//Preparing
			$stmt = $mysqli->prepare( $sql );

			//binding parameter
			$stmt->bind_param( "s", $user_Name) ;

			//execute
			$stmt->execute();

			//bind results
			$pwresults = null;
			$stmt->bind_result($pwresults);

			//store results to get properties
			$stmt->store_result();

			//checks if anything was returned
			if($stmt->num_rows === 1)
			{

				while($stmt->fetch())
				{

					//checking password match and if matches then log in
					if(crypt($password, $pwresults) === $pwresults)
					{
						//get UID for later
						//SQL to Prepare
						$uidsql = null;
						$uidsql = 'SELECT Users.UID AS UserID' .
									' FROM Users' .
									' WHERE Users.Username = ?';

						//Preparing
						$uidstmt = $mysqli->prepare( $uidsql );

						//Binding Parameter
						$uidstmt->bind_param("s", $user_Name) ;

						//execute
						$uidstmt->execute();

						//bind results
						$uidresults = null;
						$uidstmt->bind_result($uidresults);

						//store results to get properties
						$uidstmt->store_result();

						//sets all the variables to the initial settings
						//everything is set to null and first page to start in is Teams
						while($uidstmt->fetch())
						{
							$_SESSION[ 'user' ] = $uidresults;
							$_SESSION[ 'currentSelection' ] = "Teams";
							$_SESSION[ 'teamID' ] = null;
							$_SESSION[ 'teamName' ] = "None";
							$_SESSION[ 'meetID' ] = null;
							$_SESSION[ 'meetName' ] = "None";
							$_SESSION[ 'swimmerID' ] = null;
							$_SESSION[ 'swimmerName' ] = "None";
							$_SESSION[ 'eventID' ] = null;
							$_SESSION[ 'eventName' ] = "None";
						}


						header('Location: search.php');

						$passwordMatch = TRUE;
					}
					//password entered does not match with one in database
					else
					{
						$passwordMatch = FALSE;
					}
				}

			}
			//username entered does not exist in database
			else
			{
				$usernameMatch = FALSE;
			}

			$stmt->free_result();
		}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Creative - Start Bootstrap Theme</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">

    <!-- Custom Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css" type="text/css">

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="css/animate.min.css" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/creative.css" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body id="page-top">

    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="#page-top">Home</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
						<a class="page-scroll" href="#register">Register</a>
					</li>
					<li>
                        <a class="page-scroll" href="#about">About</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#login">LogIn</a>
                    </li>
					<!--
                    <li>
                        <a class="page-scroll" href="#contact">Contact</a>
                    </li>
					-->
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

    <header>
        <div class="header-content">
            <div class="header-content-inner">
                <h1>KEEP TRACK OF TIMES AND MORE!</h1>
                <hr>
                <p><strong>Start by creating an account now!</strong></p>
            </div>
        </div>
    </header>


<!-- This section is used to create a new account -->
	<section class="bg-primary" id="register">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
				<?php
					if($isAccountCreated)
					{
						echo '<h3>Created new Account!</h3>';
						echo "<br>";
						echo "User Name is: " . $ruser_Name;
						echo "<br>";
						echo "<br>";
					}
					else if($isUsernameAvailable === FALSE)
					{
						echo $ruser_Name . " is already taken please try again.";
					}
					else if($isPassMatch === FALSE)
					{
						echo "The passwords do not match please try again.";
					}
				?>
            <h2 class="section-heading">Register</h2>
						<form method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
						  <div class="form-group">
							<label for="userName">User Name</label>
							<input type="text" class="form-control" id="newUserName" placeholder="User Name" name= "ruser_Name" maxlength="30">
						  </div>
						  <div class="form-group">
							<label for="password">Password</label>
							<input type="password" class="form-control" id="newPassword" placeholder="Password" name= "r_Password" maxlength="100">
						</div>
						<div class="form-group">
						<label for="confirmPassword">Confirm Password</label>
						<input type="password" class="form-control" id="confirmPassword" placeholder="Password" name= "c_Password" maxlength="100">
					</div>
						<!--
						  <div class="form-group">
							<label for="email"> Email </label>
							<input type="email" class="form-control" id="newEmail" placeholder="Email" name= "remail" maxlength="254">
						</div>-->
						  <input type="submit" name="rsubmit" class="btn btn-default" value="Register">
						</form>
                    <hr class="light">
                    <p class="text-faded">Making an account lets you create teams, view your times, and message your teams.</p>
				   </div>
            </div>
        </div>
    </section>

    <section id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <h2 class="section-heading">We've got what you need!</h2>
                    <hr class="light">
                    <p>Making an account lets you create teams, view your times, and message your teams.</p>
                </div>
            </div>
        </div>
    </section>


<!-- This is the section to login -->
	<section class="bg-primary" id="login">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
				<?php
					if($passwordMatch===FALSE)
					{
						echo '<h3>PASSWORDS DO NOT MATCH TRY AGAIN</h3>';
						echo "<br>";
					}
					else if($usernameMatch===FALSE)
					{
						echo '<h3> USERNAME DOES NOT EXIST TRY AGAIN </h3>';
						echo "<br>";
					}
				?>
                    <h2 class="section-heading">Log In</h2>
						<form method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>" >
						  <div class="form-group">
							<label for="userName">User Name</label>
							<input type="text" class="form-control" id="userName" placeholder="User Name" name= "user_Name" maxlength="30">
						  </div>
						  <div class="form-group">
							<label for="password">Password</label>
							<input type="password" class="form-control" id="password" placeholder="Password" name="password" maxlength="100">
						  </div>
						  <input type="submit" name="lsubmit" class="btn btn-default" value="LogIn">
						</form>
			   </div>
            </div>
        </div>
    </section>

	<!--
    <section id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <h2 class="section-heading">Let's Get In Touch!</h2>
                    <hr class="primary">
                    <p>Questions, problems, or feedback? Give us a call or send us an email and we will get back to you as soon as possible!</p>
                </div>
                <div class="col-lg-4 col-lg-offset-2 text-center">
                    <i class="fa fa-phone fa-3x wow bounceIn"></i>
                    <p>123-456-6789</p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fa fa-envelope-o fa-3x wow bounceIn" data-wow-delay=".1s"></i>
                    <p><a href="mailto:your-email@your-domain.com">feedback@startbootstrap.com</a></p>
                </div>
            </div>
        </div>
    </section>
	-->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/jquery.fittext.js"></script>
    <script src="js/wow.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/creative.js"></script>

</body>

</html>
