
// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function(){
  log.history = log.history || [];   // store logs to an array for reference
  log.history.push(arguments);
  if(this.console) {
    arguments.callee = arguments.callee.caller;
    var newarr = [].slice.call(arguments);
    (typeof console.log === 'object' ? log.apply.call(console.log, console, newarr) : console.log.apply(console, newarr));
  }
};

// make it safe to use console.log always
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,timeStamp,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();){b[a]=b[a]||c}})((function(){try
{console.log();return window.console;}catch(err){return window.console={};}})());


// place any jQuery/helper plugins in here, instead of separate, slower script files.

/*
ChiliBook.recipeFolder = "js/libs/chili/";

ChiliBook.automatic = false; 
$( function() { 
    $( '#highlight' ) 
    .one( 'click', function() { 
        var time1 = new Date(); 
        var $chili = $( '#jq' ).chili(); 
        var time2 = new Date(); 
        var delta = time2 - time1; 
        var spans = $chili.find( 'span' ).length; 
        var rate = Math.round( spans / delta * 100 ) / 100; 
        $( '#highlight' ).html(  
            'highlight done in ' + delta + ' milliseconds' 
            + ' with ' + spans + ' spans' 
            + ' at a rate of ' + rate * 1000 + ' span/sec' 
        ); 
    } ) 
    .show() 
    ; 
} ); 


*/
/*

<script type="text/javascript" src="SOMEWHERE_ELSE/jquery.js"></script>
<script type="text/javaScript" src="SOMEWHERE/jquery.chili-2.2.js"></script>
<script type="text/javascript">
	ChiliBook.recipeFolder = "SOMEWHERE/"; 
</script> 

*/