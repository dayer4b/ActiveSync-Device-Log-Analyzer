<?php

require_once('logclass.php');


/*
* converts from UTC to central time, makes a pretty format.
*/
function formatTime($time){
	$timestamp = strtotime($time) - 5*3600;
	return date("l, F jS @ g:i:sa",$timestamp);
}




$logPath;
$thisLogEntry;

if(isset($_GET['log'])){
	$logPath = $_GET['log'];
}

if(isset($_GET['logEntry'])){
	$thisLogEntry = $_GET['logEntry'];
}


$log = new Log($logPath);

$thisRequestTime = $log->requestTime($thisLogEntry);
$thisResponseTime = $log->responseTime($thisLogEntry);

?>

<div class='request data'>
<h3>Request Body</h3>
<h4><?php print formatTime($thisRequestTime); ?></h4>
	<p><a href='<?php print $log->requestBodyXMLPath($thisLogEntry); ?>'>file</a></p>
	<p class='message'></p>
	<pre>
		<code>
		<?php print $log->requestBodyXML($thisLogEntry); ?>
		</code>
	</pre>
</div>



<div class='response data'>
<h3>Response Body</h3>
<h4><?php print formatTime($thisResponseTime); ?></h4>
	<p><a href='<?php print $log->responseBodyXMLPath($thisLogEntry); ?>'>file</a></p>
	<p class='message'></p>
	<pre>
		<code>
		<?php print $log->responseBodyXML($thisLogEntry); ?>
		</code>
	</pre>
</div>

<?php 


?>