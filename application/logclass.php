<?php

class Log {
	
	private $__logpath;
	private $__logEntries;
	private $__outputFolder = "output";
	private $__logcode;
	
	function __construct($filename){
		
		$this->__logpath = $filename;
		$this->__logcode = md5($this->__logpath);
		
		$file_data = file_get_contents($this->__logpath);
		$megaSplit = preg_split('/-----------------/',$file_data);
		
		//print_r($megaSplit);
		$logEntries = array();
		$logEntryData = array();
		
		foreach($megaSplit as $key => $section){
			if($key!==0){
				if($key%2==1){
					$logEntries[] = $section;
				}else{
					$logEntryData[] = $section;
				}
			}
		}
		
		$this->__logEntries = $logEntries;
		
		$i = 0;
		
		if(!file_exists($this->__outputFolder."/".$this->__logcode)){

			
			foreach($this->__logEntries as $logEntry){
				
				$logIndex = str_replace(" Log Entry: ","",$logEntry);
				$logIndex = trim($logIndex);
				
				$thisFileName = $logIndex.".txt";
				$this->save($thisFileName,$logEntryData[$i]);
				$i++;
				
			}
			
		}
		
	}
	
	private function createFolder($folderPath){
		if(!file_exists($folderPath)){
			mkdir($folderPath,0777);
			chmod($folderPath,0777);
		}
	}
	
	private function save($filename,$data){
		
		$this->createFolder($this->__outputFolder."/".$this->__logcode);
		
		$filepath = $this->__outputFolder."/".$this->__logcode."/".$filename;
		
		$fp = fopen($filepath, 'w');
		fwrite($fp, trim($data));
		fclose($fp);
		
		chmod($filepath,0777);
		
	}
	
	private function get_match($regex,$content)
	{
		preg_match($regex,$content,$matches);
		return $matches[1];
	}
	
	private function logEntryIndex($logEntry){
		
		$logIndex = str_replace(" Log Entry: ","",$logEntry);
		$logIndex = trim($logIndex);
		
		return $logIndex;
	}
	
	
	
	
	
	
	
	
	

	public function entryPath($thisLogEntry){
		return $this->__outputFolder."/".$this->__logcode."/".$thisLogEntry.".txt";
	}
	
	public function requestBodyXMLPath($thisLogEntry){
		$thisFileName = $thisLogEntry.'requestBodyXML.txt';
		return $this->__outputFolder."/".$this->__logcode."/".$thisFileName;
	}

	public function responseBodyXMLPath($thisLogEntry){
		$thisFileName = $thisLogEntry.'responseBodyXML.txt';
		return $this->__outputFolder."/".$this->__logcode."/".$thisFileName;
	}
	
	
	
	
	
	
	
	
	/*
	* converts from UTC to central time, makes a pretty format.
	*/
	private function formatTimeShort($time){
		$timestamp = strtotime($time) - 5*3600;
		//return date("l, F jS @ g:i:sa",$timestamp);
		return date("M j @ g:i:sa",$timestamp);
	}
	
	
	
	
	public function logEntries(){
		return $this->__logEntries;
	}

	
	public function logEntriesLabelled(){
	
		$outputArray = array();
	
		foreach($this->__logEntries as $logEntry){
			
			$logEntryIndex = $this->logEntryIndex($logEntry);
			
			$thisRequestTime = $this->formatTimeShort($this->requestTime($logEntryIndex));
			$outputArray[$logEntryIndex] = $logEntry." at ".$thisRequestTime;
		}
	
		return $outputArray;
	}
	
	/*
	 * outputs an array like  ( 'timestamp'  => 'number of syncs in this minute' )
	 */
	public function timeline($resolution){
	
		$outputArray = array();
	
		$previousTime = 0;
		$syncCounter = 0;
		
		foreach($this->__logEntries as $logEntry){
			
			$logEntryIndex = $this->logEntryIndex($logEntry);
			// load time, convert to timestamp, convert to CST timezone (there's a weird but necessary 
			//  overcorrection here for some reason)
			// TODO: fix overcorrection
			$thisRequestTime = strtotime($this->requestTime($logEntryIndex)) - 10*3600;
			
			
			// round timestamp to nearest [resolution]
			// $resolution = 3600;  // in seconds

			$thisRequestTime = floor($thisRequestTime/$resolution)*$resolution;
			
			//error_log("thisRequestTime: ".$thisRequestTime.",     previousTime: ".$previousTime,0);
			
			if($thisRequestTime === $previousTime){
				$syncCounter++;
			}else{
				if($previousTime!==0){
					$syncCounter++;
					
					$outputValue = $thisRequestTime * 1000;
					$outputArray[$outputValue] = $syncCounter;
					
					$syncCounter = 0;
					
				}
			}
			
			$previousTime = $thisRequestTime;
			
		}
	
		return $outputArray;
	}
	
	
	
	
	
	
	public function requestTime($thisLogEntry){
		
		$data = file_get_contents($this->__outputFolder."/".$this->__logcode."/".$thisLogEntry.".txt");
		$output = $this->get_match('/RequestTime \: (.*)ServerName \:/isU',$data);
	
		return htmlentities($output);
	}
	
	public function responseTime($thisLogEntry){
		$data = file_get_contents($this->__outputFolder."/".$this->__logcode."/".$thisLogEntry.".txt");
		$output = $this->get_match('/ResponseTime \: (.*)$/isU',$data);
	
		return htmlentities($output);
	}
	
	public function requestBodyXML($thisLogEntry){
	
		if(!file_exists($this->requestBodyXMLPath($thisLogEntry))){
	
			$data = file_get_contents($this->__outputFolder."/".$this->__logcode."/".$thisLogEntry.".txt");
	
			$output = $this->get_match('/RequestBody \: (.*)LogicalRequest \: /isU',$data);
	
			if(strlen($output)==0){
				$output = $this->get_match('/RequestBody \: (.*)AccessState \: /isU',$data);
				if(strlen($output)==0){
					$output = $this->get_match('/RequestBody \: (.*)WasPending \: /isU',$data);
					if(strlen($output)==0){
						$output = $this->get_match('/RequestBody \: (.*)-----------------/isU',$data);
						if(strlen($output)==0){
							$output = $this->get_match('/RequestBody \: (.*)$/isU',$data);
						}
					}
				}
			}
			 
			$this->save($thisLogEntry.'requestBodyXML.txt',$output);
		}
	
		return htmlentities(file_get_contents($this->requestBodyXMLPath($thisLogEntry)));
	}
	
	public function logicalRequestXML($source,$thisLogEntry){
		return htmlentities($this->get_match('/LogicalRequest \: (.*)AccessState \: /isU',$source[$thisLogEntry]));
	}
	
	public function responseBodyXML($thisLogEntry){
	
		if(!file_exists($this->responseBodyXMLPath($thisLogEntry))){
			$data = file_get_contents($this->__outputFolder."/".$this->__logcode."/".$thisLogEntry.".txt");
	
			$output = $this->get_match('/ResponseBody \: (.*)ResponseTime \: /isU',$data);
	
			$this->save($thisLogEntry.'responseBodyXML.txt',$output);
		}
	
		return htmlentities(file_get_contents($this->responseBodyXMLPath($thisLogEntry)));
	}
	
	
	
	
}


?>