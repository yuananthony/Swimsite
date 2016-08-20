<?php
	//db variables
	define( 'DB_SERVER', 'localhost' );
	define( 'DB_USER',   'root' );
	define( 'DB_PW',	 '' );
	define( 'DB_NAME',   'swimming' );
	/*NOTES TO SELF: 
		Use $_SESSION variables to avoid variables changing
		After a post method whole page refreshes so if variables are initialized as null and changed
		they will be set back to null after the POST method.
		
		Only add swimmers to teams and not meets (need to get rid of later)
	*/
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
	
	if(!isset($_SESSION[ 'user' ] ) )
	{
		header('Location: index.php');
	}
	
	//these variables need to be sessino variables
	$teams = null;
	$meets = null;
	$swimmers = null;
	$events = null;
	$results = null;
	
	
	if( isset($_POST[ 'Teams' ]) )
	{
		//works now need to create variables and for teams and stuff (for now just single condition and all)
		
		$_SESSION[ 'currentSelection' ] = 'Teams';
	}
	
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
	
	//STILL NEED TO GET TEAMID
	if(isset($_POST[ 'SearchTeam' ]) )
	{
		$_SESSION[ 'team' ] = $_POST[ 'SelectTeam' ];
		
		$_SESSION[ 'currentSelection' ] = 'Meets';
	}
	
	if( isset($_POST[ 'Meets' ]) )
	{
		
		$_SESSION[ 'currentSelection' ] = 'Meets';
	}
	
	if(isset($_POST[ 'NewMeetSubmit' ]) )
	{
		//sql to add new meet
		
		$newmeet_Name = $_POST[ 'NewMeetName' ];
		$newmeet_Date = $_POST[ 'NewMeetDate' ];
		
		//SQL to Prepare
		$newMeetSQL = null;
		$newMeetSQL = "INSERT INTO Meets (Date, MName)" .
						" VALUES ( ? , ? )";
						
		//Preparing
		$newMeetstmt = $mysqli->prepare($newMeetSQL);
		
		//Binding Parameter
		$newMeetstmt->bind_param("ss", $newmeet_Date, $newmeet_Name);
		
		//execute
		$newMeetstmt->execute();
		
		$newMeetstmt->close();
		
		//MUST ADD TEAMMEETS SQL LATER
		
	}
	
	if( isset($_POST[ 'Swimmers' ]) )
	{
		$_SESSION[ 'currentSelection' ] = 'Swimmers';
	}

	if(isset($_POST[ 'AddSwimmertoTeamsubmit' ]) )
	{
		$newSwimmerFirstName = $_POST[ 'newSwimmerFirstName' ];
		$newSwimmerLastName = $_POST[ 'newSwimmerLastName' ];
		$newSwimmerAge = $_POST[ 'newSwimmerAge' ];
		$newSwimmerYear = $_POST[ 'newSwimmerYear' ];
		
		//SQL to Prepare
		$newSwimmerSQL = null;
		$newSwimmerSQL = "INSERT INTO Swimmers (SFName, SLName, age, year)" .
							" VALUES ( ? , ? , ? , ? )";
							
		//Preparing
		$newSwimmerstmt = $mysqli->prepare($newSwimmerSQL);
		
		//Binding Parameter
		$newSwimmerstmt->bind_param("ssis", $newSwimmerFirstName, $newSwimmerLastName, $newSwimmerAge, $newSwimmerYear);
		
		//execute
		$newSwimmerstmt->execute();
		
		$newSwimmerstmt->close();
		
		//MUST ADD SWIMMERTEAMS SQL LATER
	}
	
	if(isset($_POST[ 'AddSwimmertoTeamsubmit' ]) )
	{
		$teamToCopyFrom = $_POST[ 'AddSwimmersFromTeam' ];
		
		//SQL to Prepare (FIND ALL SWIMMERS TAHT WERE ON THE TEAM) NEED TO FIX THIS LATER
		$findSwimmersSQL = null;
		$findSwimmersSQL = "SELECT Team.TeamID AS TeamID" .
							" FROM Team" .
							" WHERE Team.Name = ?";
							
		//Preparing
		$findSwimmersstmt = $mysqli->prepare($findSwimmersSQL);
		
		//Binding Parameter
		$findSwimmersstmt->bind_param("s", $teamToCopyFrom);
		
		//execute
		$findSwimmersstmt->execute();
		
		//iterating over results
		$findSwimmersstmt->bind_result($copyFromTeamID);
		
		
	}
	if( isset($_POST[ 'Events' ]) )
	{
		
		$_SESSION[ 'currentSelection' ] = 'Events';
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
					<!--<input type="submit" name="logout" class="navbar-brand page-scroll" value="LogOut">-->
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
						<p><strong>Team: <?php echo $_SESSION[ 'team' ] ?> Meet: Swimmer: etc</strong></p>
						<?php
						//write code to add teams meets etc 
							if($_SESSION[ 'currentSelection' ] === 'Teams')
							{
								echo "Add new Team:";
						?>
								<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
									<div class = "form-group">
										<label for="teamName"> Team Name:</label>
										<input type="text" class="form-control" id="newTeamName" name = "NewTeamName" required maxlength = "30">
									</div>
									<input type="submit" class="btn btn-default" name="NewTeamSubmit" value="Add New Team">
								</form>
						<?php
							}
							else if($_SESSION[ 'currentSelection' ] === 'Meets')
							{
								echo "Add new Meet:";
						?>
								<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
									<div class = "form-group">
										<label for="meetName"> Meet Name:</label>
										<input type="text" class="form-control" id="newMeetName" name = "NewMeetName" required maxlength = "30">
									</div>
									<div class = "form-group">
										<label for="meetDate"> Meet Date:</label>
										<input type="date" class="form-control" id="newMeetDate" name = "NewMeetDate" required>
									</div>
									<input type="submit" class="btn btn-default" name="NewMeetSubmit" value="Add new Meet">
								</form>
						<?php
							}
							else if($_SESSION[ 'currentSelection' ] === 'Swimmers')
							{
									echo "Add new swimmer:";
									
						?>									
									<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
										<div class = "form-group">
											<label for="swimmerFirstName"> First Name:</label>
											<input type="text" class="form-control" id="newSwimmerFirstName" name = "newSwimmerFirstName" required maxlength = "30">
										</div>
										<div class = "form-group">
											<label for="swimmerLastName"> Last Name:</label>
											<input type="text" class="form-control" id="newSwimmerLastName" name = "newSwimmerLastName" required maxlength = "30">
										</div>
										<div class = "form-group">
											<label for="swimmerAge"> Age:</label>
											<input type="number" class="form-control" id="newSwimmerAge" name = "newSwimmerAge" min = "1" max = "99">
										</div>
										<div class = "form-group">
											<label for="swimmerYear"> Year:</label>
											<input type="text" class="form-control" id="newSwimmerYear" name = "newSwimmerYear" maxlength = "20">
										</div>
										<button type="submit" name="AddSwimmertoTeamsubmit" class="btn btn-default"> Add Swimmer to Team </button>									
									</form>
									
									<br>
									
									<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
										<div class = "form-group">
											<label for="selTeam">Select team to copy swimmers from:</label>
											<select class="form-control" id="selTeam" name="AddSwimmersFromTeam">
												<?php
												
													//SQL to Prepare
													$teamNamesSQL = null;
													$teamNamesSQL = "SELECT Team.Name AS Team_Name" .
																	" FROM Team" .
																	" WHERE Team.HeadCoach = ?";
																	
													//Preparing
													$teamNamesstmt = $mysqli->prepare($teamNamesSQL);
													
													//Binding Parameter
													$teamNamesstmt->bind_param("i", $_SESSION[ 'user' ]);
													
													//execute
													$teamNamesstmt->execute();
													
													//iterating over results
													$teamNamesstmt->bind_result($returnedTeamNames);
													
													while($teamNamesstmt->fetch())
													{
													
														echo "<option value=$returnedTeamNames> $returnedTeamNames </option>";
												
													}
													
													$teamNamesstmt->free_result();
												?>
											</select>
										</div>
										<div class = "form-group">
											<input type="submit" name="AllSwimmersOnTeam" class="btn btn-default" value="Add all swimmers from team">
										</div>
									</form>
						<?php
								
							}
							else if($_SESSION[ 'currentSelection' ] === 'Events')
							{
								echo "Select which events will be swimming";
							}
						?>
			</section>
	
			<!--this is going to be the body of the page with all the results-->
			
				<?php
					if($_SESSION[ 'currentSelection' ] === 'Teams')
					{
				?>
						<br>
						<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
										<div class = "form-group">
											<label for="selTeam">Select team to search from:</label>
											<select class="form-control" id="selTeam" name="SelectTeam">
												<?php
												
													//SQL to Prepare
													$teamNamesSQL = null;
													$teamNamesSQL = "SELECT Team.Name AS Team_Name" .
																	" FROM Team" .
																	" WHERE Team.HeadCoach = ?";
																	
													//Preparing
													$teamNamesstmt = $mysqli->prepare($teamNamesSQL);
													
													//Binding Parameter
													$teamNamesstmt->bind_param("i", $_SESSION[ 'user' ]);
													
													//execute
													$teamNamesstmt->execute();
													
													//iterating over results
													$teamNamesstmt->bind_result($returnedTeamNames);
													
													while($teamNamesstmt->fetch())
													{
													
														echo "<option value=$returnedTeamNames> $returnedTeamNames </option>";
												
													}
													
													$teamNamesstmt->free_result();
												?>
											</select>
										</div>
										<div class = "form-group">
											<input type="submit" name="SearchTeam" class="btn btn-default" value="Search this team">
										</div>
									</form>
		
				<?php
					}
				?>
			<section>
				<div class="container">
					<div class="row">
						
					</div>
				</div>
			</section>
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