<?php

require('logclass.php');

/*
 * converts from UTC to central time, makes a pretty format.
 */
function formatTime($time){
	$timestamp = strtotime($time) - 5*3600;
	return date("l, F jS @ g:i:sa",$timestamp);
}

?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!-- Consider adding an manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/b/378 -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>ActiveSync device log analyzer</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

  <!-- CSS: implied media=all -->
  <!-- CSS concatenated and minified via ant build script-->
  <link rel="stylesheet" href="css/style.css">
  <!-- end CSS-->

  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

  <!-- All JavaScript at the bottom, except for Modernizr / Respond.
       Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
       For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
  <script src="js/libs/modernizr-2.0.6.min.js"></script>
</head>

<body>

  <div id="container">
    <header>
      <h1><a href='.'>ActiveSync device log analyzer</a></h1>
    </header>
    <div id="main" role="main">

<div id='options'>


</div>

<div id='logs'>
<?php

if(!isset($_GET['log'])){

$logs_path = "../logs/";


// open this directory 
$myDirectory = opendir($logs_path);

// get each entry
while($entryName = readdir($myDirectory)) {
  if($entryName!=="." && $entryName!==".."){
    $dirArray[] = $entryName;
  }
}

// close directory
closedir($myDirectory);

//  count elements in array
$indexCount = count($dirArray);
Print ("$indexCount files<br>\n");

// sort 'em
sort($dirArray);

// print 'em
print("<TABLE border=1 cellpadding=5 cellspacing=0 class=whitelinks>\n");
print("<TR><TH>Filename</TH><th>Filetype</th><th>Filesize (bytes)</th></TR>\n");
// loop through the array of files and print them all
for($index=0; $index < $indexCount; $index++) {
        if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files

$thisPath = $logs_path.$dirArray[$index];

    print("<TR><TD><a href=\"?log=$thisPath\">$dirArray[$index]</a></td>");
    print("<td>");
    print(filetype($logs_path.$dirArray[$index]));
    print("</td>");
    print("<td>");
    print(filesize($logs_path.$dirArray[$index]));
    print("</td>");
    print("</TR>\n");
  }
}
print("</TABLE>\n");

}else{

  $logPath = $_GET['log'];

  $log = new Log($logPath);
  
?>

    <div id="timeline">
    
	    <script>


				var timelineByHour = [
	    <?php 
	    
	    $j = 0;
	    
	    $timelineByHour = $log->timeline(3600);
	    
	    foreach($timelineByHour as $timestamp => $count){
	    	print "[".$timestamp.",".$count."]";
	    	
	    	if(($j+1)!==count($timelineByHour)){
	    		print ",\r\n";
	    	}
	    	
	    	$j++;
	    	
	    }
	    
	    ?>
				];


				var timelineByMinute = [
              	    <?php 
              	    
              	    $j = 0;
              	    
              	    $timelineByMinute = $log->timeline(60);
              	    
              	    foreach($timelineByMinute as $timestamp => $count){
              	    	print "[".$timestamp.",".$count."]";
              	    	
              	    	if(($j+1)!==count($timelineByMinute)){
              	    		print ",\r\n";
              	    	}
              	    	
              	    	$j++;
              	    	
              	    }
              	    
              	    ?>
              				];

	    </script>
    
    </div>

<div id='data'>


<form action='' method='get' name='logData'>
	
	<input type='hidden' name='log' value='<?php print $logPath; ?>' />
	
	
	<label for='logEntry'>Select Log Entry</label>
	<select id='logEntry' name='logEntry'>
	<?php
	
	foreach($log->logEntriesLabelled() as $value => $label){
		
		if(isset($_GET['logEntry']) && $value==$_GET['logEntry']){
			$selected = " selected='selected' ";
		}else{
			$selected = " ";
		}
	
		print "<option value='".$value."' ".$selected.">".$label."</option>";
	}
	
	
	?>
	</select>
	
	<input type='submit' />

</form>

<h4 class='message'>Examining file: <strong><a href='<?php print $logPath; ?>'><?php print $logPath; ?></a></strong> (<?php print count($log->logEntriesLabelled()); ?> log entries).</h4>

<?php
if(isset($_GET['logEntry'])){

$thisLogEntry = $_GET['logEntry'];

$previousLogEntry = $thisLogEntry - 1;
$nextLogEntry = $thisLogEntry + 1;
  ?>
<div class='navigation'>

<ul>
  <li class='previous'><a href='?log=<?php print $logPath; ?>&logEntry=<?php print $previousLogEntry; ?>'>&lt;&lt; previous</a></li>
  <li class='next'><a href='?log=<?php print $logPath; ?>&logEntry=<?php print $nextLogEntry; ?>'>next &gt;&gt;</a></li>
</ul>

</div>


<h2>Examining Log Entry: <a href='<?php print $log->entryPath($thisLogEntry); ?>'><?php print $thisLogEntry; ?></a></h2>



<?php ?>




<?php 
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
	</pr</div>






<?php 
}
 ?>



</div>




<?php


}
?>
</div>

    </div>
    <footer>
    
    
    </footer>
  </div> <!--! end of #container -->


  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <!-- 
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  -->
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>


  <!-- scripts concatenated and minified via ant build script-->
  <!-- 
<script defer src="js/libs/chili/jquery.chili-2.2.js"></script>
<script defer src="js/libs/chili/recipes.js"></script>
  -->
  
	<script defer src="js/libs/Highstock-1.0.1/js/highstock.js"></script>
	<script defer src="js/libs/Highstock-1.0.1/js/modules/exporting.js"></script>
	<script defer src="js/libs/Highstock-1.0.1/js/themes/dark-green.js"></script>
  
  <script defer src="js/libs/x2j.js"></script>
  <script defer src="js/plugins.js"></script>
  <script defer src="js/script.js"></script>
  <!-- end scripts-->

	
  <!-- Change UA-XXXXX-X to be your site's ID -->
  <script>
    window._gaq = [['_setAccount','UAXXXXXXXX1'],['_trackPageview'],['_trackPageLoadTime']];
    Modernizr.load({
      load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
    });
  </script>


  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
  <![endif]-->
</body>



</html>
