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
    header('Location: search.php');
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
	}

	if(isset($_POST[ 'AddEvent' ]) )
	{
		$meetToCreate = $_POST[ 'selectEvent' ];
		$orderInMeet = $_POST[ 'newEventOrder'];
		$laneToAdd = $_POST[ 'newEventLane' ];
		$swimmerIDToSwim = $_POST[ 'selectSwimmerForEvent' ];
		$eventName = null;

		//NEED TO WRITE SQL TO ADD EVENT THEN GET ID THEN ADD SWIMMERMEET AND EVENTMEET
		//SQL to Prepare
		$newEventSQL = null;

		//IF NEW MEETS ARE ADDED ADD HERE
		if($meetToCreate === "50Free")
		{
			$newEventSQL = "INSERT INTO 50Free (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "50Free";
		}
		else if($meetToCreate === "100Free")
		{
			$newEventSQL = "INSERT INTO 100Free (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "100Free";
		}
		else if($meetToCreate === "200Free")
		{
			$newEventSQL = "INSERT INTO 200Free (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "200Free";
		}
		else if($meetToCreate === "500Free")
		{
			$newEventSQL = "INSERT INTO 500Free (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "500Free";
		}
		else if($meetToCreate === "100Fly")
		{
			$newEventSQL = "INSERT INTO 100Fly (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "100Fly";
		}
		else if($meetToCreate === "100Breast")
		{
			$newEventSQL = "INSERT INTO 100Breast (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "100Breast";
		}
		else if($meetToCreate === "100Back")
		{
			$newEventSQL = "INSERT INTO 100Back (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "100Back";
		}
		else if($meetToCreate === "200IM")
		{
			$newEventSQL = "INSERT INTO 200IM (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "200IM";
		}
		else if($meetToCreate === "25Free")
		{
			$newEventSQL = "INSERT INTO 25Free (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "25Free";
		}
		else if($meetToCreate === "25Fly")
		{
			$newEventSQL = "INSERT INTO 25Fly (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "25Fly";
		}
		else if($meetToCreate === "25Back")
		{
			$newEventSQL = "INSERT INTO 25Back (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "25Back";
		}
		else if($meetToCreate === "25Breast")
		{
			$newEventSQL = "INSERT INTO 25Breast (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "25Breast";
		}
		else if($meetToCreate === "100IM")
		{
			$newEventSQL = "INSERT INTO 100IM (Lane, Name)" .
											" Values ( ? , ? )";

			$eventName = "100IM";
		}

		//Preparing
		$newEventstmt = $mysqli->prepare($newEventSQL);

		//binding parameter
		$newEventstmt->bind_param("is", $laneToAdd, $eventName);

		//execute
		$newEventstmt->execute();

		//get the id of the event
		$newEventID = mysqli_insert_id($mysqli);

		$newEventstmt->close();

		//SQL TO ADD SWIMMEREVENT AND EVENTMEET

		$swimmerEventSQL = null;
		$eventMeetSQL = null;

		//IF NEW MEETS ARE ADDED ADD HERE
		if($meetToCreate === "50Free")
		{
			$swimmerEventSQL = "INSERT INTO 50FreeSwimmers (50FreeSID, 50FreeSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 50FreeMeets (50FreeEID, 50FreeMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "100Free")
		{
			$swimmerEventSQL = "INSERT INTO 100FreeSwimmers (100FreeSID, 100FreeSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 100FreeMeets (100FreeEID, 100FreeMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "200Free")
		{
			$swimmerEventSQL = "INSERT INTO 200FreeSwimmers (200FreeSID, 200FreeSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 200FreeMeets (200FreeEID, 200FreeMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "500Free")
		{
			$swimmerEventSQL = "INSERT INTO 500FreeSwimmers (500FreeSID, 500FreeSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 500FreeMeets (500FreeEID, 500FreeMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "100Fly")
		{
			$swimmerEventSQL = "INSERT INTO 100FlySwimmers (100FlySID, 100FlySwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 100FlyMeets (100FlyEID, 100FlyMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "100Breast")
		{
			$swimmerEventSQL = "INSERT INTO 100BreastSwimmers (100BreastSID, 100BreastSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 100BreastMeets (100BreastEID, 100BreastMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "100Back")
		{
			$swimmerEventSQL = "INSERT INTO 100BackSwimmers (100BackSID, 100BackSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 100BackMeets (100BackEID, 100BackMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "200IM")
		{
			$swimmerEventSQL = "INSERT INTO 200IMSwimmers (200IMSID, 200IMSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 200IMMeets (200IMEID, 200IMMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "25Free")
		{
			$swimmerEventSQL = "INSERT INTO 25FreeSwimmers (25FreeSID, 25FreeSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 25FreeMeets (25FreeEID, 25FreeMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "25Fly")
		{
			$swimmerEventSQL = "INSERT INTO 25FlySwimmers (25FlySID, 25FlySwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 25FlyMeets (25FlyEID, 25FlyMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "25Back")
		{
			$swimmerEventSQL = "INSERT INTO 25BackSwimmers (25BackSID, 25BackSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 25BackMeets (25BackEID, 25BackMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "25Breast")
		{
			$swimmerEventSQL = "INSERT INTO 25BreastSwimmers (25BreastSID, 25BreastSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 25BreastMeets (25BreastEID, 25BreastMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
		}
		else if($meetToCreate === "100IM")
		{
			$swimmerEventSQL = "INSERT INTO 100IMSwimmers (100IMSID, 100IMSwims)" .
											" Values ( ? , ? )";

			$eventMeetSQL = "INSERT INTO 100IMMeets (100IMEID, 100IMMID, OrderInMeet)" .
											" Values ( ? , ? , ? )";
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
		$eventMeetstmt->bind_param("iii", $newEventID, $_SESSION[ 'meetID' ], $orderInMeet);

		//execute
		$eventMeetstmt->execute();

		//close
		$eventMeetstmt->close();

		//SQL TO Prepare
		$meetEventSQL = null;
		$meetEventSQL = "INSERT IGNORE INTO MeetEvents" .
										" Values ( ? , ? )";

		//Preapring MeetEvent
		$meetEventstmt = $mysqli->prepare($meetEventSQL);

		//Binding Parameter
		$meetEventstmt->bind_param("is", $_SESSION[ 'meetID' ], $meetToCreate);

		//execute
		$meetEventstmt->execute();

		//close
		$meetEventstmt->close();
	}

	if(isset($_POST[ 'SearchEvents']))
	{
		if(!empty($_POST[ 'selectSwimmerEvent' ]))
		{
			foreach($_POST['selectSwimmerEvent'] as $swimmereventid)
			{
				echo $swimmereventid."</br>";
			}
		}
		$_SESSION[ 'currentSelection' ] = 'Results';
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
				body {background-color: #5f9bce;}
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
											<!--IF NEW MEETS ARE ADDED ADD HERE-->
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
										<label for"selOrder">Order In Meet</label>
										<input type = "number" class = "form-control" id = "selOrder" name = "newEventOrder">
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
						?>
			</section>

			<!--END OF ADD SECTION-->
			<!--this is going to be the body of the page with all the results-->

						<form class = "form-inline" method = "POST" action = "<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>">
						<table class="table table-responsive">
							<thead>
								<tr>
									<th>Order</th>
									<th>Event</th>
									<th>FirstName</th>
									<th>LastName</th>
									<th>Time</th>
									<th>DQ</th>
									<th>Lane</th>
									<th>Select</th>
								</tr>
							</thead>
							<tbody>

					<?php
						if(($_SESSION[ 'meetID' ] !== null) && ($_SESSION[ 'swimmerID' ] === null))
						{

									//meet events
									$findMeetEventsSQL = null;
									$findMeetEventsSQL = "SELECT MeetEvents.MEEventName AS EventName" .
																				" FROM MeetEvents" .
																				" WHERE MeetEvents.MEMeetID = ?";

									//Preparing Meet events
									$findMeetEventsstmt = $mysqli->prepare($findMeetEventsSQL);

									//Binding
									$findMeetEventsstmt->bind_param("i", $_SESSION[ 'meetID' ]);

									//execute
									$findMeetEventsstmt->execute();

									//bind results
									$findMeetEventsstmt->bind_result($MeetName);

									$findEventArray = [];

									while($findMeetEventsstmt->fetch())
									{
										//IF NEW MEETS ARE ADDED ADD HERE
										//write sql for EACH event and if statement to swtch between them
										if($MeetName === "50Free")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 50FreeMeets.OrderInMeet AS EOrder, 50Free.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 50Free.Time AS ETime, 50Free.DQ AS DQ, 50Free.Lane AS Lane, 50Free.50FreeEventID AS EventID" .
																				" FROM 50FreeMeets INNER JOIN 50Free ON 50Free.50FreeEventID = 50FreeMeets.50FreeEID" .
																				" INNER JOIN 50FreeSwimmers ON 50FreeSwimmers.50FreeSwims = 50Free.50FreeEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 50FreeSwimmers.50FreeSID" .
																				" WHERE 50FreeMeets.50FreeMID = ? ";


										}
										else if($MeetName === "100Free")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 100FreeMeets.OrderInMeet AS EOrder, 100Free.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 100Free.Time AS ETime, 100Free.DQ AS DQ, 100Free.Lane AS Lane, 100Free.100FreeEventID AS EventID" .
																				" FROM 100FreeMeets INNER JOIN 100Free ON 100Free.100FreeEventID = 100FreeMeets.100FreeEID" .
																				" INNER JOIN 100FreeSwimmers ON 100FreeSwimmers.100FreeSwims = 100Free.100FreeEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 100FreeSwimmers.100FreeSID" .
																				" WHERE 100FreeMeets.100FreeMID = ? ";
										}
										else if($MeetName === "200Free")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 200FreeMeets.OrderInMeet AS EOrder, 200Free.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 200Free.Time AS ETime, 200Free.DQ AS DQ, 200Free.Lane AS Lane, 200Free.200FreeEventID AS EventID" .
																				" FROM 200FreeMeets INNER JOIN 200Free ON 200Free.200FreeEventID = 200FreeMeets.200FreeEID" .
																				" INNER JOIN 200FreeSwimmers ON 200FreeSwimmers.200FreeSwims = 200Free.200FreeEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 200FreeSwimmers.200FreeSID" .
																				" WHERE 200FreeMeets.200FreeMID = ? ";
										}
										else if($MeetName === "500Free")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 500FreeMeets.OrderInMeet AS EOrder, 500Free.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 500Free.Time AS ETime, 500Free.DQ AS DQ, 500Free.Lane AS Lane, 500Free.500FreeEventID AS EventID" .
																				" FROM 500FreeMeets INNER JOIN 500Free ON 500Free.500FreeEventID = 500FreeMeets.500FreeEID" .
																				" INNER JOIN 500FreeSwimmers ON 500FreeSwimmers.500FreeSwims = 500Free.500FreeEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 500FreeSwimmers.500FreeSID" .
																				" WHERE 500FreeMeets.500FreeMID = ? ";
										}
										else if($MeetName === "100Fly")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 100FlyMeets.OrderInMeet AS EOrder, 100Fly.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 100Fly.Time AS ETime, 100Fly.DQ AS DQ, 100Fly.Lane AS Lane, 100Fly.100FlyEventID AS EventID" .
																				" FROM 100FlyMeets INNER JOIN 100Fly ON 100Fly.100FlyEventID = 100FlyMeets.100FlyEID" .
																				" INNER JOIN 100FlySwimmers ON 100FlySwimmers.100FlySwims = 100Fly.100FlyEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 100FlySwimmers.100FlySID" .
																				" WHERE 100FlyMeets.100FlyMID = ? ";
										}
										else if($MeetName === "100Breast")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 100BreastMeets.OrderInMeet AS EOrder, 100Breast.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 100Breast.Time AS ETime, 100Breast.DQ AS DQ, 100Breast.Lane AS Lane, 100Breast.100BreastEventID AS EventID" .
																				" FROM 100BreastMeets INNER JOIN 100Breast ON 100Breast.100BreastEventID = 100BreastMeets.100BreastEID" .
																				" INNER JOIN 100BreastSwimmers ON 100BreastSwimmers.100BreastSwims = 100Breast.100BreastEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 100BreastSwimmers.100BreastSID" .
																				" WHERE 100BreastMeets.100BreastMID = ? ";
										}
										else if($MeetName === "100Back")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 100BackMeets.OrderInMeet AS EOrder, 100Back.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 100Back.Time AS ETime, 100Back.DQ AS DQ, 100Back.Lane AS Lane, 100Back.100BackEventID AS EventID" .
																				" FROM 100BackMeets INNER JOIN 100Back ON 100Back.100BackEventID = 100BackMeets.100BackEID" .
																				" INNER JOIN 100BackSwimmers ON 100BackSwimmers.100BackSwims = 100Back.100BackEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 100BackSwimmers.100BackSID" .
																				" WHERE 100BackMeets.100BackMID = ? ";
										}
										else if($MeetName === "200IM")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 200IMMeets.OrderInMeet AS EOrder, 200IM.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 200IM.Time AS ETime, 200IM.DQ AS DQ, 200IM.Lane AS Lane, 200IM.200IMEventID AS EventID" .
																				" FROM 200IMMeets INNER JOIN 200IM ON 200IM.200IMEventID = 200IMMeets.200IMEID" .
																				" INNER JOIN 200IMSwimmers ON 200IMSwimmers.200IMSwims = 200IM.200IMEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 200IMSwimmers.200IMSID" .
																				" WHERE 200IMMeets.200IMMID = ? ";
										}
										else if($MeetName === "25Free")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 25FreeMeets.OrderInMeet AS EOrder, 25Free.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 25Free.Time AS ETime, 25Free.DQ AS DQ, 25Free.Lane AS Lane, 25Free.25FreeEventID AS EventID" .
																				" FROM 25FreeMeets INNER JOIN 25Free ON 25Free.25FreeEventID = 25FreeMeets.25FreeEID" .
																				" INNER JOIN 25FreeSwimmers ON 25FreeSwimmers.25FreeSwims = 25Free.25FreeEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 25FreeSwimmers.25FreeSID" .
																				" WHERE 25FreeMeets.25FreeMID = ? ";
										}
										else if($MeetName === "25Fly")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 25FlyMeets.OrderInMeet AS EOrder, 25Fly.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 25Fly.Time AS ETime, 25Fly.DQ AS DQ, 25Fly.Lane AS Lane, 25Fly.25FlyEventID AS EventID" .
																				" FROM 25FlyMeets INNER JOIN 25Fly ON 25Fly.25FlyEventID = 25FlyMeets.25FlyEID" .
																				" INNER JOIN 25FlySwimmers ON 25FlySwimmers.25FlySwims = 25Fly.25FlyEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 25FlySwimmers.25FlySID" .
																				" WHERE 25FlyMeets.25FlyMID = ? ";
										}
										else if($MeetName === "25Back")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 25BackMeets.OrderInMeet AS EOrder, 25Back.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 25Back.Time AS ETime, 25Back.DQ AS DQ, 25Back.Lane AS Lane, 25Back.25BackEventID AS EventID" .
																				" FROM 25BackMeets INNER JOIN 25Back ON 25Back.25BackEventID = 25BackMeets.25BackEID" .
																				" INNER JOIN 25BackSwimmers ON 25BackSwimmers.25BackSwims = 25Back.25BackEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 25BackSwimmers.25BackSID" .
																				" WHERE 25BackMeets.25BackMID = ? ";
										}
										else if($MeetName === "25Breast")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 25BreastMeets.OrderInMeet AS EOrder, 25Breast.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 25Breast.Time AS ETime, 25Breast.DQ AS DQ, 25Breast.Lane AS Lane, 25Breast.25BreastEventID AS EventID" .
																				" FROM 25BreastMeets INNER JOIN 25Breast ON 25Breast.25BreastEventID = 25BreastMeets.25BreastEID" .
																				" INNER JOIN 25BreastSwimmers ON 25BreastSwimmers.25BreastSwims = 25Breast.25BreastEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 25BreastSwimmers.25BreastSID" .
																				" WHERE 25BreastMeets.25BreastMID = ? ";
										}
										else if($MeetName === "100IM")
										{
											$findEventSQL = null;
											$findEventSQL = "SELECT 100IMMeets.OrderInMeet AS EOrder, 100IM.Name AS EventName, Swimmers.SFName AS FirstName, Swimmers.SLName AS LastName, 100IM.Time AS ETime, 100IM.DQ AS DQ, 100IM.Lane AS Lane, 100IM.100IMEventID AS EventID" .
																				" FROM 100IMMeets INNER JOIN 100IM ON 100IM.100IMEventID = 100IMMeets.100IMEID" .
																				" INNER JOIN 100IMSwimmers ON 100IMSwimmers.100IMSwims = 100IM.100IMEventID" .
																				" INNER JOIN Swimmers ON Swimmers.SNID = 100IMSwimmers.100IMSID" .
																				" WHERE 100IMMeets.100IMMID = ? ";
										}

										$findEventArray[] = $findEventSQL;

									}//end while loop

									$findMeetEventsstmt->close();

										foreach($findEventArray as $eventsToSearch)
										{

											//Prepare findEventSQL
											$findEventstmt = $mysqli->prepare($eventsToSearch);

											//bind
											$findEventstmt->bind_param("i", $_SESSION[ 'meetID' ]);

											//execute
											$findEventstmt->execute();

											$findEventstmt->bind_result($eventOrder, $nameOfEvent, $firstNameOfSwimmer, $lastNameOfSwimmer, $timeOfSwimmer, $isDQ, $laneNumber, $selectedEventID);

											while($findEventstmt->fetch())
											{
										?>
													<tr>
														<td><?php echo $eventOrder ?></td>
														<td><?php echo $nameOfEvent ?></td>
														<td><?php echo $firstNameOfSwimmer ?></td>
														<td><?php echo $lastNameOfSwimmer ?></td>
														<td><?php echo $timeOfSwimmer ?></td>
														<td><?php echo $isDQ ?></td>
														<td><?php echo $laneNumber ?></td>
														<td>
															<div class = "checkbox">
																<input type="checkbox" style="vertical-align: middle; margin: -7px;" name="selectSwimmerEvent[]" value= <?php echo $selectedEventID ?>>
															</div>
														</td>
													</tr>
									<?php
											}//end while loop
										}//end foreach loop

										unset($findEventArray);

			}//end meetid not null swimmerid null
			else if(($_SESSION[ 'meetID' ] !== null)  && ($_SESSION[ 'swimmerID' ] !== null))
			{
				//sql to find a swimmer events in a given meet
			}
			else if(($_SESSION[ 'meetID' ] === null) && ($_SESSION[ 'swimmerID' ] !== null))
			{
				//sql to find all events that a swimmer on a team has swam
			}
				?>
					</tbody>
					</table>
					<div class = "form-group">
						<input type="submit" class="btn btn-default" name="SearchEvents" value="Search Selected Events">
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
