<?php

	/* Taken from Corey Maynard 
	* http://coreymaynard.com/blog/creating-a-restful-api-with-php/
	* Accessed 8-May-2017
	*/
	
	class API
	{
                // The arguments provided in the URI
		protected $args = Array();
                
                // A variable to hold my key
                protected $myKey = '';
                
                // Which Airconditioner to check
                protected $unitNumber = ''; 
                
                // The desired temperature for the room
                protected $desiredTemp = '';
                
                // The desired on-off mode of the air conditioner
                protected $desiredState = '';
                
                // The current temperature of the room
                protected $currentTemp = '';
                
                // The current on-off state of the air conditioner
                protected $currentState = '';
                
                // The current fan speed
                protected $currentFan = '';

                // GET or POST Method 
                protected $method = '';
                
                // Room 
                protected $room ='';
                
                // Device type
                protected $device = '';
                
                /*
                 *  Constructor prepares the header, parses the call
                 *  and translates HTTP_X method
                 */
		public function __construct($request) {
                        // Include proper headers so the receiving 
                        // unit understands the output.
			header("Access-Control-Allow-Orgin: *");
			header("Access-Control-Allow-Methods: *");
			header("Content-Type: application/json");

			$this->args = explode('/', rtrim($request, '/'));
                        
                        // Shift the endpoint and the null string off the 
                        // argument list
			array_shift($this->args);
                        array_shift($this->args);
                        
                        $this->myKey = $this->args[0];
                        

			$this->method = $_SERVER['REQUEST_METHOD'];
			if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
				if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
					$this->method = 'DELETE';
				} else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
					$this->method = 'PUT';
				} else {
					throw new Exception("Unexpected Header");
				}
			}

		}
		
                public function validateKey(){
                    
                    // Validate the key is the write length
                    if(strlen($this->myKey) != 10){
                        throw new Exception("Invalid API Key");
                    }
                    
                    $query = "SELECT room FROM devices WHERE apikey ='" . 
                            $this->myKey . "';";
                    
                    $result = mysqli_fetch_array($this->myQuery($query));
                    $this->room = $result[room];
                    
                    // Validate that the key was valid
                    if(strlen($this->room) == 0){
                        throw new Exception("Invalid API Key");
                    }
                    
                    $query = "SELECT type FROM devices WHERE apikey ='" . 
                            $this->myKey . "';";
                    
                    $result = mysqli_fetch_array($this->myQuery($query));
                    $this->device = $result[type];
                    
                    return;
                }
                
                public function myQuery($query){
                    
                    $username = "website";
                    $servername = "localhost";
                    $password = "password";
                    $dbname = "aircon";
                    
                    // Connect to the database
                    $conn = new mysqli($servername, $username, $password, 
                            $dbname);
                    if($conn->connect_error){
                        throw new Exception("No connection to database");
                    }
                    
                    // Make the query
                    $result = $conn->query($query) or die("Error in selection");
                    
                    if($conn->error){
                        throw new Exception($conn->error);
                    }
                    
                    mysqli_close($conn);
                    
                    return $result;
                    
                }
                
		public function processAPI() {
                    
                    $result = array();
                    
                    // Validate key
                    $this->validateKey();
                    
                    // If the method is GET, return data
                    if ($this->method == 'GET'){
                        
                        // All devices can GET data
                        // If it is the app return all
                        if($this->device == 'app'){
                            $query = "SELECT * FROM status;";
                        }
                        else{
                            $query = "SELECT * FROM status WHERE roomid = '" . 
                                    $this->room . "';";
                        }
                       
                    }
                    
                    else if($this->method == 'POST'){
                        
                        // App, Thermostat, and Device can post different
                        // Information
                        
                        // Thermostat
                        // Usage AutomationAPI/KEY/currTemp
                        if($this->device == 'therm'){
                            
                            $this->currentTemp = $this->args[1];

                            $query = "UPDATE status SET currTemp = " . 
                                    $_POST["currentTemp"] . " WHERE roomID ='" . 
                                    $_POST["room"] . "';";
                            
                        /*    
                            $query = "UPDATE status SET currTemp = " . 
                                    $this->currentTemp . " WHERE roomID ='" . 
                                    $this->room . "';";
                        */    
                        }
                        
                        // Device
                        // Usage AutomationAPI/KEY/state/fanspeed
                        if($this->device == 'unit'){
                            
                            $this->currentState = $this->args[1];
                            $this->currentFan = $this->args[2];
                            
                            $query = "UPDATE status SET currState = " . 
                                    $_POST["currentState"] . ", currFan = " . 
                                    $_POST["currentFan"] . " WHERE roomID ='" . 
                                    $_POST["room"] . "';";
                            
                            
                        /*    $query = "UPDATE status SET currState = " . 
                                    $this->currentState . ", currFan = " . 
                                    $this->currentFan . " WHERE roomID ='" . 
                                    $this->room . "';";
                        */    
                        }
                        
                        // App
                        // Main then Bed
                        // Usage AutomationAPI/Key/state/speed/temp/state/speed/temp
                        if($this->device == 'app'){
                            
                            $query = "UPDATE status SET desState = " . 
                                    $_POST["mainStatus"] . ", desFan = " . 
                                    $_POST["mainFanSpeed"] . ", desTemp = " . 
                                    $_POST["mainTemp"] . " WHERE roomID = 'main';";
                            
                            $this->myQuery($query);
                            
                            $query = "UPDATE status SET desState = " . 
                                    $_POST["bedState"] . ", desFan = " . 
                                    $_POST["bedFanSpeed"] . ", desTemp = " . 
                                    $_POST["bedTemp"] . " WHERE roomID = 'bed';";
                            
                        }
                        
                    }
                    else {
                        throw new Exception("Only Get and Post Allowed");
                    }
                    
                    $result = $this->myQuery($query);

                    if($this->method == GET){
                    
                        // Clean up the SQL Result to be displayed in JSON
                        if($result){    
                            $emparray = array();
                            while($row = mysqli_fetch_assoc($result)){
                                $emparray[] = $row;
                            }
                        }



                        return json_encode($emparray);
                    }
                    
                    return "success";
                        /*
                         * Taken from example
			if (method_exists($this, $this->endpoint)) {
				return $this->_response($this->{$this->endpoint}($this->args));
			}
			return $this->_response("No Endpoint: $this->endpoint", 404);
                         * 
                         */
		}

		private function _response($data, $status = 200) {
			header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
			return json_encode($data);
		}



		private function _requestStatus($code) {
			$status = array(  
				200 => 'OK',
				404 => 'Not Found',   
				405 => 'Method Not Allowed',
				500 => 'Internal Server Error',
			); 
			return ($status[$code])?$status[$code]:$status[500]; 
		}
	}
	
	


?>
