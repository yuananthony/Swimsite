<?php
	//db variables
	define( 'DB_SERVER', 'localhost' );
	define( 'DB_USER',   'root' );
	define( 'DB_PW',	 'root' );
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
		//gets value of auto_increment to be used later
		$newMeetID = mysqli_insert_id($mysqli);

		$newMeetstmt->close();

		//SQL to add to TeamMeets

		//SQL to Prepare
		$newTeamMeetSQL = null;
		$newTeamMeetSQL = "INSERT INTO TeamMeets (TMTeamID, TMMeetID)" .
							" VALUES ( ? , ? )";

		//Preparing
		$newTeamMeetstmt = $mysqli->prepare($newTeamMeetSQL);

		//Binding Parameter
		$newTeamMeetstmt->bind_param("ii" , $_SESSION[ 'teamID' ], $newMeetID);

		//execute
		$newTeamMeetstmt->execute();

		$newTeamMeetstmt->close();

	}

	if( isset($_POST[ 'SearchMeet' ]) )
	{
		$meetResult = $_POST[ 'SelectMeet'];

		$meetResult_explode = explode('|', $meetResult);

		$_SESSION[ 'meetName' ] = $meetResult_explode[0];
		$_SESSION[ 'meetID' ] = $meetResult_explode[1];
		$_SESSION[ 'swimmerID' ] = null;
		$_SESSION[ 'swimmerName' ] = "None";
		$_SESSION[ 'eventID' ] = null;
		$_SESSION[ 'eventName' ] = "None";

		$_SESSION[ 'currentSelection' ] = "Swimmers";

	}

	if( isset($_POST[ 'SearchAllMeets' ]) )
	{
		//write SQL to get all meet IDs and put it into an array

		$_SESSION[ 'currentSelection' ] = "Swimmers";
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
		//gets the ID of the swimmer
		$newSwimmerID = mysqli_insert_id($mysqli);

		$newSwimmerstmt->close();

		//SWIMMERTEAMS SQL

		//SQL to Prepare
		$newSwimmerTeamsSQL = null;
		$newSwimmerTeamsSQL = "INSERT INTO SwimmerTeams (STTeamID, STSNID)" .
								" VALUES ( ? , ? )";

		//Preparing
		$newSwimmerTeamsstmt = $mysqli->prepare($newSwimmerTeamsSQL);

		//Binding Parameter
		$newSwimmerTeamsstmt->bind_param("ii", $_SESSION[ 'teamID' ], $newSwimmerID);

		//execute
		$newSwimmerTeamsstmt->execute();

		$newSwimmerTeamsstmt->close();
	}

	if(isset($_POST[ 'AddSwimmersOnTeam' ]) )
	{
		$teamIDToCopyFrom = $_POST[ 'AddSwimmersFromTeam' ];

		echo $teamIDToCopyFrom;
		//Finding all swimmers on a team
		//SQL to Prepare
		$findSwimmersSQL = null;
		$findSwimmersSQL = "SELECT SwimmerTeams.STSNID" .
							" FROM SwimmerTeams" .
							" WHERE SwimmerTeams.STTeamID = ? ";

		//Preparing
		$findSwimmersstmt = $mysqli->prepare($findSwimmersSQL);

		//Binding Parameter
		$findSwimmersstmt->bind_param("i", $teamIDToCopyFrom);

		//execute
		$findSwimmersstmt->execute();

		//iterating over results of find all swimmers on a team
		$findSwimmersstmt->bind_result($copySwimmerID);

		$swimmerIDArray = array();

		while($findSwimmersstmt->fetch())
		{
			$swimmerIDArray[] = $copySwimmerID;
		}

		//closing
		$findSwimmersstmt->close();

		//Inserting all swimmers from old team to new team
		//SQL to Prepare
		$insertSwimmersFromOldToNewSQL = null;
		$insertSwimmersFromOldToNewSQL = "INSERT INTO SwimmerTeams (STTeamID, STSNID)" .
										" VALUES ( ? , ? )";

		//Preparing
		$insertSwimmersFromOldToNewstmt = $mysqli->prepare($insertSwimmersFromOldToNewSQL);

		foreach($swimmerIDArray as $copiedSwimmerID)
		{

			//Binding Parameters for inserting all swimmers from old team to current team
			$insertSwimmersFromOldToNewstmt->bind_param("ii", $_SESSION[ 'teamID' ], $copiedSwimmerID);

			//execute inserting swimmer from old team to current team
			$insertSwimmersFromOldToNewstmt->execute();

		}

		unset($copiedSwimmerID);

		//closing
		$insertSwimmersFromOldToNewstmt->close();


	}

	if( isset($_POST[ 'SearchSwimmer' ]) )
	{
		$swimmerResult = $_POST[ 'SelectSwimmer'];

		$swimmerResult_explode = explode('|', $swimmerResult);

		$_SESSION[ 'swimmerName' ] = $swimmerResult_explode[0];
		$_SESSION[ 'swimmerID' ] = $swimmerResult_explode[1];
		$_SESSION[ 'eventID' ] = null;
		$_SESSION[ 'eventName' ] = "None";

		$_SESSION[ 'currentSelection' ] = "Events";
	}

	if( isset($_POST[ 'SearchAllSwimmeres' ]) )
	{
		$_SESSION[ 'currentSelection' ] = "Events";
	}
	if( isset($_POST[ 'Events' ]) )
	{
		$_SESSION[ 'currentSelection' ] = 'Events';
	}

	if(isset($_POST[ 'AddEvent' ]) )
	{
		$meetToCreate = $_POST[ 'selectEvent' ];
		$laneToAdd = $_POST[ 'newEventLane' ];
		$swimmerIDToSwim = $_POST[ 'selectSwimmerForEvent' ];

		//NEED TO WRITE SQL TO ADD EVENT THEN GET ID THEN ADD SWIMMERMEET AND EVENTMEET
		//SQL to Prepare
		$newEventSQL = null;

		if($meetToCreate === "50Free")
		{
			$newEventSQL = "INSERT INTO 50Free (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "100Free")
		{
			$newEventSQL = "INSERT INTO 100Free (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "200Free")
		{
			$newEventSQL = "INSERT INTO 200Free (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "500Free")
		{
			$newEventSQL = "INSERT INTO 500Free (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "100Fly")
		{
			$newEventSQL = "INSERT INTO 100Fly (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "100Breast")
		{
			$newEventSQL = "INSERT INTO 100Breast (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "100Back")
		{
			$newEventSQL = "INSERT INTO 100Back (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "200IM")
		{
			$newEventSQL = "INSERT INTO 200IM (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "25Free")
		{
			$newEventSQL = "INSERT INTO 25Free (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "25Fly")
		{
			$newEventSQL = "INSERT INTO 25Fly (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "25Back")
		{
			$newEventSQL = "INSERT INTO 25Back (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "25Breast")
		{
			$newEventSQL = "INSERT INTO 25Breast (Lane)" .
											" Values ( ? )";
		}
		else if($meetToCreate === "100IM")
		{
			$newEventSQL = "INSERT INTO 100IM (Lane)" .
											" Values ( ? )";
		}

		//Preparing
		$newEventstmt = mysqli->prepare($newEventSQL);

		//binding parameter
		$newEventstmt->bind_param("i", $laneToAdd);

		//execute
		$newEventstmt->execute();

		//get the id of the event
		$newEventID = mysqli_insert_id($mysqli);

		$newEventstmt->close();

		//SQL TO ADD SWIMMEREVENT AND EVENTMEET

		$swimmerEventSQL = null;
		$eventMeetSQL = null;

		if($meetToCreate === "50Free")
		{
			$swimmerEventSQL = "INSERT INTO 50FreeSwimmers (50FreeSID, 50FreeSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 50FreeMeets (50FreeEID, 50FreeMID)" .
											" Values ( ? , ? )"
		}
		else if($meetToCreate === "100Free")
		{
			$swimmerEventSQL = "INSERT INTO 100FreeSwimmers (100FreeSID, 100FreeSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 100FreeMeets (100FreeEID, 100FreeMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "200Free")
		{
			$swimmerEventSQL = "INSERT INTO 200FreeSwimmers (200FreeSID, 200FreeSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 200FreeMeets (200FreeEID, 200FreeMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "500Free")
		{
			$swimmerEventSQL = "INSERT INTO 500FreeSwimmers (500FreeSID, 500FreeSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 500FreeMeets (500FreeEID, 500FreeMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "100Fly")
		{
			$swimmerEventSQL = "INSERT INTO 100FlySwimmers (100FlySID, 100FlySwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 100FlyMeets (100FlyEID, 100FlyMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "100Breast")
		{
			$swimmerEventSQL = "INSERT INTO 100BreastSwimmers (100BreastSID, 100BreastSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 100BreastMeets (100BreastEID, 100BreastMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "100Back")
		{
			$swimmerEventSQL = "INSERT INTO 100BackSwimmers (100BackSID, 100BackSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 100BackMeets (100BackEID, 100BackMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "200IM")
		{
			$swimmerEventSQL = "INSERT INTO 200IMSwimmers (200IMSID, 200IMSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 200IMMeets (200IMEID, 200IMMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "25Free")
		{
			$swimmerEventSQL = "INSERT INTO 25FreeSwimmers (25FreeSID, 25FreeSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 25FreeMeets (25FreeEID, 25FreeMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "25Fly")
		{
			$swimmerEventSQL = "INSERT INTO 25FlySwimmers (25FlySID, 25FlySwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 25FlyMeets (25FlyEID, 25FlyMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "25Back")
		{
			$swimmerEventSQL = "INSERT INTO 25BackSwimmers (25BackSID, 25BackSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 25BackMeets (25BackEID, 25BackMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "25Breast")
		{
			$swimmerEventSQL = "INSERT INTO 25BreastSwimmers (25BreastSID, 25BreastSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 25BreastMeets (25BreastEID, 25BreastMID)" .
											" Values ( ? , ? )";
		}
		else if($meetToCreate === "100IM")
		{
			$swimmerEventSQL = "INSERT INTO 100IMSwimmers (100IMSID, 100IMSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 100IMMeets (100IMEID, 100IMMID)" .
											" Values ( ? , ? )";
		}

		//Preparing SwimmerEvent
		$swimmerEventstmt = $mysqli->prepare($swimmerEventSQL);

		//Binding Parameter
		$swimmerEventstmt->bind_param("ii", $swimmerIDToSwim, $newEventID);

		//execute
		$swimmerEventstmt->execute();

		//close
		$swimmerEventstmt->close();

		//Preparing eventMeet
		$eventMeetstmt = $mysqli->prepare($eventMeetSQL);

		//Binding Parameter
		$eventMeetstmt->bind_param("ii", $newEventID, $_SESSION[ 'meetID' ]);

		//execute
		$eventMeetstmt->execute();

		//close
		$eventMeetstmt->close();
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
						<p class="text-left"><strong>Team: <?php echo $_SESSION[ 'teamName' ] ?></strong></p>
						<p class="text-left"><strong>Meet: <?php echo $_SESSION[ 'meetName' ] ?></strong></p>
						<p class="text-left"><strong>Swimmer: <?php echo $_SESSION[ 'swimmerName' ] ?></strong></p>
						<p class="text-left"><strong>Event: <?php echo $_SESSION[ 'eventName' ] ?></strong></p>
						<?php
						//write code to add teams meets etc
							if($_SESSION[ 'currentSelection' ] === 'Teams')
							{
						?>
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
						<?php
							}
							else if($_SESSION[ 'currentSelection' ] === 'Meets')
							{
								if($_SESSION[ 'teamID' ] === null)
								{
						?>
									<h3> <strong>Please Select A Team Before Adding Or Searching A Meet </strong></h3>
						<?php
								}
								else
								{
									//MAKE SURE USER CAN NOT ADD SAME NAME MEETS
						?>
									<p> Add new Meet:</p>
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
							}
							else if($_SESSION[ 'currentSelection' ] === 'Swimmers')
							{
									if($_SESSION[ 'teamID' ] === null)
									{
						?>
										<h3><strong> Please Select A Team Before Adding Or Searching Swimmeres </strong></h3>
						<?php
									}
									else
									{
						?>
										<p> Add new Swimmer: </p>
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
														$teamNamesstmt->bind_result($returnedTeamNames, $returnedTeamIDs);

														while($teamNamesstmt->fetch())
														{

															echo "<option value=$returnedTeamIDs> $returnedTeamNames </option>";

														}

														$teamNamesstmt->free_result();
													?>
												</select>
											</div>
											<div class = "form-group">
												<input type="submit" name="AddSwimmersOnTeam" class="btn btn-default" value="Add all swimmers from team">
											</div>
										</form>
						<?php
									}
							}
							else if($_SESSION[ 'currentSelection' ] === 'Events')
							{
								if($_SESSION[ 'meetID' ] === null)
								{
						?>
									<h3><strong> Please Select a Team And A Meet Before Adding or Searching Events. </strong></h3>
						<?php
								}
								else
								{
						?>
								<p>Add what event and lane a swimmer will be swimming in</p>
								<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
									<div class = "form-group">
										<label for="selEvent">Select event:</label>
										<select class="form-control" id="selEvent" name="selectEvent">
											<option value = "50Free"> 50 Free </option>
											<option value = "100Free"> 100 Free </option>
											<option value = "200Free"> 200 Free </option>
											<option value = "500Free"> 500 Free </option>
											<option value = "100Fly"> 100 Fly </option>
											<option value = "100Breast"> 100 Breast </option>
											<option value = "100Back"> 100 Back </option>
											<option value = "200IM"> 200 IM </option>
											<option value = "25Free"> 25 Free </option>
											<option value = "25Fly"> 25 Fly </option>
											<option value = "25Back"> 25 Back </option>
											<option value = "25Breast"> 25 Breast </option>
											<option value = "100IM"> 100 IM </option>
										</select>
									</div>
									<div class = "form-group">
										<label for="selLane">Select lane:</label>
										<input type="number" class="form-control" id="selLane" name = "newEventLane" min = "1" max = "12">
									</div>
									<div class = "form-group">
										<label for="selSwimmerForEvent">Select swimmer:</label>
										<select class="form-control" id="selSwimmerForEvent" name="selectSwimmerForEvent">
											<?php
												//SQL to Prepare
												$swimmersNamesSQL = null;
												$swimmersNamesSQL = "SELECT Swimmers.SFName AS SwimmerFirstName, Swimmers.SLName AS SwimmerLastName, Swimmers.SNID AS SwimmerID" .
																						" FROM Swimmers INNER JOIN SwimmerTeams ON Swimmers.SNID = SwimmerTeams.STSNID" .
																						" WHERE SwimmerTeams.STTeamID = ?";

												//Preparing
												$swimmerNamesstmt = $mysqli->prepare($swimmersNamesSQL);

												//Binding Parameter
												$swimmerNamesstmt->bind_param("i", $_SESSION[ 'teamID' ]);

												//execute
												$swimmerNamesstmt->execute();

												//iterating over results
												$swimmerNamesstmt->bind_result($returnedSwimmerFirstName, $returnedSwimmerLastName, $returnedSwimmerID);

												while($swimmerNamesstmt->fetch())
												{
													echo "<option value=$returnedSwimmerID> $returnedSwimmerFirstName$returnedSwimmerLastName </option>";
												}

												$swimmerNamesstmt->close();

												//may want to have a button for all swimmers to search
											?>
										</select>
									<div class = "form-group">
										<input type="submit" name="AddEvent" class="btn btn-default" value="Add Event">
									</div>
								</form>
						<?php
								}

							}
						?>
			</section>

			<!--END OF ADD SECTION-->
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

				<?php
					}
					else if($_SESSION[ 'currentSelection' ] === 'Meets')
					{

						if($_SESSION[ 'teamID' ] !== null)
						{
					?>
							<br>
							<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
								<div class = "form-group">
									<label for="selMeet">Select meet to search from:</label>
										<select class="form-control" id="selMeet" name="SelectMeet">
					<?php

										//SQL to Prepare
										$meetNamesSQL = null;
										$meetNamesSQL = "SELECT Meets.MName AS Meet_Name, Meets.MeetID AS Meet_ID" .
														" FROM Meets INNER JOIN TeamMeets ON Meets.MeetID = TeamMeets.TMMeetID" .
														" WHERE TMTeamID = ?";

										//Preparing
										$meetNamesstmt = $mysqli->prepare($meetNamesSQL);

										//Binding Parameter
										$meetNamesstmt->bind_param("i", $_SESSION[ 'teamID' ]);

										//execute
										$meetNamesstmt->execute();

										//iterating over results
										$meetNamesstmt->bind_result($returnedMeetNames, $returnedMeetID);

										while($meetNamesstmt->fetch())
										{

											echo "<option value=$returnedMeetNames|$returnedMeetID> $returnedMeetNames </option>";

										}

										$meetNamesstmt->close();

										//may want to have a button for all meets to search
					?>
										</select>
								</div>
										<div class = "form-group">
											<input type="submit" name="SearchMeet" class="btn btn-default" value="Search this meet">
										</div>
							</form>

							<input type="submit" name="SearchAllMeets" class="btn btn-default" value="Search All Meets">

				<?php
						}
					}
					else if($_SESSION[ 'currentSelection' ] === 'Swimmers')
					{
						if($_SESSION[ 'teamID' ] !== null)
						{
				?>
							<br>
							<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
								<div class = "form-group">
									<label for="selSwimmer">Select swimmer to search from:</label>
										<select class="form-control" id="selSwimmer" name="SelectSwimmer">
				<?php
										//SQL to Prepare
										$swimmersNamesSQL = null;
										$swimmersNamesSQL = "SELECT Swimmers.SFName AS SwimmerFirstName, Swimmers.SLName AS SwimmerLastName, Swimmers.SNID AS SwimmerID" .
															" FROM Swimmers INNER JOIN SwimmerTeams ON Swimmers.SNID = SwimmerTeams.STSNID" .
															" WHERE SwimmerTeams.STTeamID = ?";

										//Preparing
										$swimmerNamesstmt = $mysqli->prepare($swimmersNamesSQL);

										//Binding Parameter
										$swimmerNamesstmt->bind_param("i", $_SESSION[ 'teamID' ]);

										//execute
										$swimmerNamesstmt->execute();

										//iterating over results
										$swimmerNamesstmt->bind_result($returnedSwimmerFirstName, $returnedSwimmerLastName, $returnedSwimmerID);

										while($swimmerNamesstmt->fetch())
										{
											echo "<option value=$returnedSwimmerFirstName$returnedSwimmerLastName|$returnedSwimmerID> $returnedSwimmerFirstName$returnedSwimmerLastName </option>";
										}

										$swimmerNamesstmt->close();

										//may want to have a button for all swimmers to search
				?>
										</select>
								</div>
										<div class ="form-group">
											<input type="submit" name="SearchSwimmer" class="btn btn-default" value="Search this swimmer">
										</div>
							</form>

							<input type="submit" name="SearchAllSwimmers" class="btn btn-default" value="Search All Swimmers">
				<?php
						}
					}
					else if($_SESSION[ 'currentSelection' ] === 'Events')
					{
						if(($_SESSION[ 'meetID' ] !== null) && ($_SESSION[ 'swimmerID' ] === null))
						{
							//sql to find all events in the meet
						}
						else if(($_SESSION[ 'meetID' ] !== null)  && ($_SESSION[ 'swimmerID' ] !== null))
						{
							//sql to find all events that a swimmer swam in a meet
						}
						else if(($_SESSION[ 'meetID' ] === null) && ($_SESSION[ 'swimmerID' ] !== null))
						{
							//sql to find all events that a swimmer on a team has swam
						}
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
