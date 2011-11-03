/* Author: 

*/

var aRequest;
var aResponse;


 $(document).ready(function(){
	 /*
	 
	 $(".navigation .previous a").click(function(evt){
		 
		 var href = $(this).attr("href");
		 var parameters = unserialize(href.split('?')[1]);
		
		 // console.log(parameters);
		 
		 $.ajax();
		 

		 evt.preventDefault();
	 });
		 
	 */
	 
		 analyzeXML();
   
   
 });


function unserialize(queryString){
	 var a = queryString;
	 var b = a.split('&');
	 var final ={};
	 $.each(b, function(x,y){
	     var temp = y.split('=');
	     final[temp[0]] = temp[1];
	 });
	 return final;
}
 
 
 function analyzeXML(){
	 
	 
	 /*
	   $("table a").click(function(event){
	     //alert("loading file "+ $(this).text());
	     
	     $.ajax({
	    	 url: $(this).attr("href"),
	    	 complete: function(response,status){
	    		 alert(status);
	    	 }
	     });
	     
	     //event.preventDefault();
	   });
	   
	   
	   
	   
	   
				 	 <?xml version="1.0" encoding="utf-8" ?>
					 <MoveItems xmlns="Move:">
					 	<Move>
					 		<SrcMsgId>5:459</SrcMsgId>
					 		<SrcFldId>5</SrcFldId>
					 		<DstFldId>3</DstFldId>
					 	</Move>
					 	<Move>
					 		<SrcMsgId>5:458</SrcMsgId>
					 		<SrcFldId>5</SrcFldId>
					 		<DstFldId>3</DstFldId>
					 	</Move>
					 	..
					  </MoveItems>
					 
					 
					 // alert(aRequest.Move[1].SrcMsgId[0].Text); == 5:458
	   
	   
	   
	   
	   */
		 
			 $.ajax({
				 url: $(".request a").attr("href"),
				 complete: function(transport,status){
					 //requestDoc = $.parseXML(transport.responseText);
					 aRequest = {};
					 var requestText;
					 
					 try{
						 aRequest = $.xmlToJSON(transport.responseText);
						 if(aRequest.Move){
							 var numberMove = aRequest.Move.length;
							 requestText = "Droid requests that " + numberMove + " items be moved.";
						 }else{
							 if(aRequest.Collections[0].Collection[0].Commands[0].Delete){
								 var numberDelete = aRequest.Collections[0].Collection[0].Commands[0].Delete.length;
								 requestText = "Droid requests that " + numberDelete + " items be deleted.";
							 }
						 }
					 }catch(err){
						 
					 }
					 
					 if(transport.responseText.indexOf("Exception")!=-1){
						 requestText = "Droid request contains an Exception.";
					 }
					 
					 $(".request .message").text(requestText);
					 
				 }
			 });
			 
	   
			 $.ajax({
				 url: $(".response a").attr("href"),
				 complete: function(transport,status){
					 var responseText;
					 aResponse = {};
					 
					 try{
						 aResponse = $.xmlToJSON(transport.responseText);
						 
						 
						 if(aResponse.Move){
							 var numberMove = aResponse.Move.length;
							 responseText = "Server responds that " + numberMove + " items be moved.";
						 }else{
							 if(aResponse.Collections[0].Collection[0].Commands[0].Delete){
								 var numberDelete = aResponse.Collections[0].Collection[0].Commands[0].Delete.length;
								 responseText = "Server responds that " + numberDelete + " items be deleted.";
							 }else{
								 if(aResponse.Collections[0].Collection[0].Commands[0].Add){
									 var numberDelete = aResponse.Collections[0].Collection[0].Commands[0].Add.length;
									 responseText = "Server responds that " + numberDelete + " items be added.";
								 }
							 }
						 }
					 }catch(err){
						 //console.log(err);
						 //requestText = err.message;
					 }
					 
					 if(transport.responseText.indexOf("Exception")!=-1){
						 requestText = "Server response contains an Exception.";
					 }
					 
					 $(".response .message").text(responseText);
					 
				 }
			 });
	 
	 
 }
 
 



	$(function() {
		
		// Create the chart
		window.chart = new Highcharts.StockChart({
			chart : {
				renderTo : 'timeline'
			},

			rangeSelector : {
				//selected : 1
			},

			title : {
				text : 'Timeline of Syncing'
			},

			xAxis : {
				maxZoom : 1 * 1 * 3600000 // 1 hour
			},
			
			tooltip : {
				xDateFormat : '%A, %b %e, %l:%M%P'
			},
			
			series : [

			          {
						name : 'syncs per min',
						data : timelineByMinute,
						tooltip: {
							yDecimals: 0
						}
					}
			
			]
		});
	
		
		$("#timeline").hide();
		$("header").append("<p><a href=''>Toggle timeline</a></p>");
		$("header p a").click(function(evt){
			
			$("#timeline").toggle();
			
			evt.preventDefault();
		});
		
		
	});














