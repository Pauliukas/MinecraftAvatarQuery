<?php
//The following script is tested only with servers running on Minecraft 1.7.

$SERVER_IP="127.0.0.1"; //Insert the IP of the server you want to query. Query must be enabled in your server.properties file!

//You can either insert the DNS (eg. play.hivemc.com) OR the IP itself (eg. 187.23.123.21). 
//Note: port is not neccesary when running the server on default port, otherwise use it!

///////////////////////////////////
//For debuging, uncomment to use.

//ini_set("display_errors", 1);
//ini_set("track_errors", 1);
//ini_set("html_errors", 1);
//error_reporting(E_ALL);
///////////////////////////////////

//Get data check API status.
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
//Query the data from the server using Minecraft API (former IamPhoenix API).
$serverdata = get_data("http://minecraft-api.com/v1/get/".$SERVER_IP."");

//JSON Decode
$data_general  = json_decode($serverdata["data"], true);

//Insert players into an array after JSON decode for later use.
$data_players = array();
foreach($data_general["players"]["sample"] as $player ){
     $data_players[] = $player['name'];
}
?>
<!DOCTYPE html>
<html>
	<head>
        <meta charset="utf-8">
        <title>MC PHP Query</title>
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
        <h1>MC PHP Query</h1><hr>       
		<div class="row">
			<div class="span4">
				<h3>General Information</h3>
				<table class="table table-striped">
					<tbody>
					<tr>
					<td><b>IP</b></td>
					<td><?php echo $SERVER_IP; ?></td>
					</tr>
					<?php if ($serverdata["status"] == "200" && $data_general['error'] == "") { ?>
					<tr>
					<td><b>Version</b></td>
					<td><?php echo $data_general['version']; ?></td>
					</tr>
					<?php } ?>
					<?php if ($serverdata["status"] == "200" && $data_general['error'] == "") { ?>
					<tr>
					<td><b>Players</b></td>
					<td><?php echo "".$data_general['players']['online']." / ".$data_general['players']['max']."";?></td>
					</tr>
					<?php } ?>
					<tr>
					<td><b>Status</b></td>
					<td><? if($data_general['status'] == 'true') { echo "<i class=\"icon-ok-sign\"></i> Server is online"; } else { echo "<i class=\"icon-remove-sign\"></i> Server is offline";}?></td>
					</tr>
					<?php if ($serverdata["status"] == "200" && $data_general['error'] == "") { ?>
					<tr>
					<td><b>Latency</b></td>
					<td><?php echo "".$data_general['latency']."ms"; ?></td>
					</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
			<div class="span8">
				<h3>Players</h3>
					<?php
					if ($data_general['error'] == "") {
					//Take the username values from the array & grab the avatars from Minotar.				
					foreach($data_players as $key => $value){$players .= "<a data-placement=\"top\" rel=\"tooltip\" style=\"display: inline-block;\" title=\"".$value."\">
					<img src=\"https://minotar.net/avatar/".$value."/50\" size=\"40\" width=\"40\" height=\"40\" style=\"width: 40px; height: 40px; margin-bottom: 5px; margin-right: 5px; border-radius: 3px;\"/></a>";}
					//Display the avatars only when there are players online.
					if($data_general['players']['online'] > 0) {
						print_r($players);
						}
					//If no avatars can be shown, display an error.
					else { 
						echo "<div class=\"alert\"> There are no players online at the moment!</div>";
						}
					} else{
						echo "<div class=\"alert\"> Query must be enabled in your server.properties file!</div>";
					}				
					?>
			</div>
		</div>
	</div>
	</body>
</html>