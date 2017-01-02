<?php
	//db variables
	define( 'DB_SERVER', 'localhost' );
	define( 'DB_USER',   'root' );
	define( 'DB_PW',	 'root' );
	define( 'DB_NAME',   'swimming' );

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

	//checks if user is logged in and sends to index page if not
	if(!isset($_SESSION[ 'user' ] ) )
	{
		header('Location: index.php');
	}


	if( isset($_POST[ 'Teams' ]) )
	{
		$_SESSION[ 'currentSelection' ] = 'Teams';
	}

	//checks if user wants to create a new team and name is not empty
	if( isset($_POST[ 'NewTeamSubmit' ]) && !empty($_POST[ 'NewTeamName' ]) )
	{
		//write sql to add new team

		$newteam_Name = $_POST[ 'NewTeamName' ];

		//SQL to Prepare
		$newTeamSQL = null;
		$newTeamSQL = "INSERT INTO Team (Name, HeadCoach)" .
						" VALUES ( ? , ? )";

		//Preparing
		$newTeamstmt = $mysqli->prepare($newTeamSQL);

		//Binding Parameter
		$newTeamstmt->bind_param("si", $newteam_Name, $_SESSION[ 'user' ]);

		//Execute
		$newTeamstmt->execute();

		$newTeamstmt->close();
	}


	if(isset($_POST[ 'SearchTeam' ]) )
	{
		$teamResult = $_POST[ 'SelectTeam' ];

		$teamResult_explode = explode('|', $teamResult);

		$_SESSION[ 'teamName'] = $teamResult_explode[1];
		$_SESSION[ 'teamID' ] = $teamResult_explode[0];
		$_SESSION[ 'meetID' ] = null;
		$_SESSION[ 'meetName' ] = "None";
		$_SESSION[ 'swimmerID' ] = null;
		$_SESSION[ 'swimmerName' ] = "None";
		$_SESSION[ 'eventID' ] = null;
		$_SESSION[ 'eventName' ] = "None";

		$_SESSION[ 'currentSelection' ] = 'Meets';

		header('Location: meet.php');
	}

	if( isset($_POST[ 'Meets' ]) )
	{
		$_SESSION[ 'currentSelection' ] = 'Meets';
		header('Location: meet.php');
	}

	if( isset($_POST[ 'Swimmers' ]) )
	{
		$_SESSION[ 'currentSelection' ] = 'Swimmers';
		header('Location: swimmer.php');
	}

	if( isset($_POST[ 'Events' ]) )
	{
		$_SESSION[ 'currentSelection' ] = 'Events';
		header('Location: events.php');
	}

	if( isset($_POST[ 'Results' ]) )
	{
		$_SESSION[ 'currentSelection' ] = 'Results';

	}

	if(isset($_SESSION[ 'user' ]))
	{
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

			<style>
				body {background-color: grey;}
				p.header {padding-top: 5px;
					color: black;}

				p.body {color: white;}
			</style>
		</head>

		<body>

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
					<a class="navbar-brand page-scroll" href="logout.php">LogOut</a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li>

								<form method="POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
									<button type="submit" name="Teams" class="btn btn-link">
									<p class = "header">Teams</p>
									</button>
								</form>
						</li>
						<li>

								<form method="POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
									<button type="submit" name="Meets" class="btn btn-link">
									<p class = "header">Meets</p>
									</button>
								</form>
						</li>
						<li>
							<form method="POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
								<button type="submit" name="Swimmers" class="btn btn-link">
								<p class = "header">Swimmers</p>
								</button>
							</form>
						</li>
						<li>
							<form method="POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
								<button type="submit" name="Events" class="btn btn-link">
								<p class = "header">Events</p>
								</button>
							</form>
						</li>
						<li>
							<form method="POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
								<button type="submit" name="Results" class="btn btn-link">
								<p class = "header">Results</p>
								</button>
							</form>
						</li>
					</ul>
				</div>
				<!-- /.navbar-collapse -->
			</div>
			<!-- /.container-fluid -->
			</nav>

			<section class="bg-primary">
						<h2>Currently Selected <?php echo $_SESSION[ 'currentSelection' ]; ?> </h2>
						<hr>
						<p class="text-left"><strong>Team: <?php echo $_SESSION[ 'teamName' ] ?></strong></p>
						<p class="text-left"><strong>Meet: <?php echo $_SESSION[ 'meetName' ] ?></strong></p>
						<p class="text-left"><strong>Swimmer: <?php echo $_SESSION[ 'swimmerName' ] ?></strong></p>
						<p class="text-left"><strong>Event: <?php echo $_SESSION[ 'eventName' ] ?></strong></p>

							<p>Add new Team</p>
								<div class="container center_div">
									<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
										<div class = "form-group">
											<label for="teamName"> Team Name:</label>
											<input type="text" class="form-control" id="newTeamName" name = "NewTeamName" required maxlength = "30">
										</div>
										<input type="submit" class="btn btn-default" name="NewTeamSubmit" value="Add New Team">
									</form>
								</div>
			</section>

			<!--END OF ADD SECTION-->
			<!--this is going to be the body of the page with all the results-->

						<br>
						<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
										<div class = "form-group">
											<label for="selTeam">Select team to search from:</label>
											<select class="form-control" id="selTeam" name="SelectTeam">
												<?php

													//SQL to Prepare
													$teamNamesSQL = null;
													$teamNamesSQL = "SELECT Team.Name AS Team_Name, Team.TeamID AS Team_ID" .
																	" FROM Team" .
																	" WHERE Team.HeadCoach = ?";

													//Preparing
													$teamNamesstmt = $mysqli->prepare($teamNamesSQL);

													//Binding Parameter
													$teamNamesstmt->bind_param("i", $_SESSION[ 'user' ]);

													//execute
													$teamNamesstmt->execute();

													//iterating over results
													$teamNamesstmt->bind_result($returnedTeamNames, $returnedTeamID);

													while($teamNamesstmt->fetch())
													{

														echo "<option value=$returnedTeamID|$returnedTeamNames> $returnedTeamNames </option>";

													}

													$teamNamesstmt->free_result();
												?>
											</select>
										</div>
										<div class = "form-group">
											<input type="submit" name="SearchTeam" class="btn btn-default" value="Search this team">
										</div>
									</form>

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
<?php
	}
	else
	{
		echo "Please LogIn first.";
	}
?>
