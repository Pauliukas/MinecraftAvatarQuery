<?php
//ini_set("display_errors", 1);
//ini_set("track_errors", 1);
//ini_set("html_errors", 1);
//error_reporting(E_ALL);

//The following script is tested only with servers running on Minecraft 1.7.

$SERVER_IP = "sky.freecraft.eu"; //Insert the IP of the server you want to query. Query must be enabled in your server.properties file!
$SERVER_PORT = "25565"; //Insert the PORT of the server you want to query. Query must be enabled in your server.properties file!

$HEADS = "3D"; //"normal" / "3D"
$SHOW_FAVICON = "on"; //"off" / "on"

$TITLE = "My fancy Serverpage";
$TITLE_BLOCK_ONE = "General Information";
$TITLE_BLOCK_TWO = "Players";

//You can either insert the DNS (eg. play.hivemc.com) OR the IP itself (eg. 187.23.123.21). 
//Note: port is not neccesary when running the server on default port, otherwise use it!

//End config

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Get Data and Status API Checker
function get_data($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $data       = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array(
        'status' => $httpStatus,
        'data' => $data
    );
} 

//Query the data from the server using Minecraft API (also known as IamPhoenix's API)
$serverdata = get_data("http://mcapi.sweetcode.de/api/v2/?ip=" . $SERVER_IP . "&port=". $SERVER_PORT ."");
$userlistserver = get_data("http://mcapi.sweetcode.de/api/v1/query/?ip=" . $SERVER_IP . ":" . $SERVER_PORT ."");

//* DEBUG AREA
//var_dump($serverdata);
//echo "<br>";echo "<br>";
//var_dump($userlistserver);
//echo "<br>";echo "<br>";
//* DEBUG AREA

// Json Decode
$data_list     = json_decode($userlistserver["data"], true);
$data_general  = json_decode($serverdata["data"], true);

//Put the collected player information into an array for later use.
$array_list = $data_list[$SERVER_IP]['player']['list'];

$queryerror = "false";
if(isset($data_list['error']) || !empty($data_list['error']) ) {
	$queryerror = "true";
}

$haserror = "false";
if($data_general['status'] != "true") {
	$haserror = "true";
}


?>
<!DOCTYPE html>
<html>
	<head>
        <meta charset="utf-8">
        <title><?php echo htmlspecialchars($TITLE); ?></title>
        <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css">
    	<link href='http://fonts.googleapis.com/css?family=Lato:300,400' rel='stylesheet' type='text/css'>
    	<link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
    	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    	<script type="text/javascript" src="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
    	<script language="javascript">
   		jQuery(document).ready(function(){
 			$("[rel='tooltip']").tooltip();
     	});
		</script>
    	<style>
    	/*Custom CSS Overrides*/
    	body {
      		font-family: 'Lato', sans-serif !important;
    	}
    	</style>
    </head>
    <body>
	<div class="container">
        <h1><?php echo htmlspecialchars($TITLE); ?></h1><hr>       
		<div class="row">
			<div class="span4">
				<h3><?php echo htmlspecialchars($TITLE_BLOCK_ONE); ?></h3>
				<table class="table table-striped">
					<tbody>
						<tr>
							<td><b>IP</b></td>
							<td><?php echo $SERVER_IP; ?></td>
						</tr>
					<?php if($haserror == "false") { ?>
						<tr>
							<td><b>Version</b></td>
							<td><?php echo $data_general['version']; ?></td>
						</tr>
					<?php } ?>
					<?php if($haserror == "false") { ?>
						<tr>
							<td><b>Players</b></td>
							<td><?php echo "".$data_general['player']['currently']." / ".$data_general['player']['max']."";?></td>
						</tr>
					<?php } ?>
						<tr>
							<td><b>Status</b></td>
							<td><? if($haserror == "false") { echo "<i class=\"icon-ok-sign\"></i> Server is online"; } else { echo "<i class=\"icon-remove-sign\"></i> Server is offline";}?></td>
						</tr>
					<?php if($haserror == "false") { ?>
						<tr>
							<td><b>Latency</b></td>
							<td><?php echo "".$data_general['list']['ping']."ms"; ?></td>
						</tr>
					<?php } ?>
					<?php if($haserror == "false") { ?>
					<?php if ($SHOW_FAVICON == "on") { ?>
						<tr>
							<td><b>Favicon</b></td>
							<td><img src='http://mcapi.sweetcode.de/api/v2/?favicon&ip=<?php echo $SERVER_IP;?>&port=<?php echo $SERVER_PORT;?>' width="64px" height="64px" style="float:left;"/></td>
						</tr>
					<?php } ?>
					<?php } ?>
					</tbody>
				</table>
			</div>
			<div class="span8">
				<h3><?php echo htmlspecialchars($TITLE_BLOCK_TWO); ?></h3>
				<?php
				if($HEADS == "3D") {
					$url = "https://cravatar.eu/helmhead/";
				} else {
					$url = "https://cravatar.eu/helmavatar/";
				}
				if($queryerror == "false" && $haserror == "false") {
				//Take the username values from the array & grab the avatars from Minotar.				
				foreach($array_list as $key => $value) {
					$users .= "<a data-placement=\"top\" rel=\"tooltip\" style=\"display: inline-block;\" title=\"".$value."\">
								<img src=\"".$url.$value."/50\" size=\"40\" width=\"40\" height=\"40\" style=\"width: 40px; height: 40px; margin-bottom: 5px; margin-right: 5px; border-radius: 3px;\"/></a>";
				}
				//Display the avatars only when there are players online.
				if($data_general['player']['currently'] > 0) {
						print_r($users);
					}
				//If no avatars can be shown, display an error.
				else { 
					echo "<div class=\"alert\"> There are no players online at the moment!</div>";
				}
				} else {
					echo "<div class=\"alert\"> Query must be enabled in your server.properties file!</div>";
				}				
				?>
			</div>
		</div>
	</div>
	</body>
</html>
