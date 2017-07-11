<!DOCTYPE html>
<html lang="en">

	<head>
		<title>Control the Conditioners</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="style/style.css">
	</head>
	

	<body>


			<div class="row">
				<div class="controller col-9 col-m-9 ">
					<h3>Main Unit</h3>
<?php
	
	$resultjson = file_get_contents("my query");
	
	$result = json_Decode($resultjson);
	

        $bedStatus = $result[0]->currState;
        $bedFan = $result[0]->currFan;
        $bedTemp = $result[0]->currTemp;
        
        $mainStatus = $result[1]->currState;
        $mainFan = $result[1]->currFan;
        $mainTemp = $result[1]->currTemp;
        
	echo "
					<form action='my query' method='post'>";
        
        if($mainStatus == 1){

            echo "
                                                    Status: 
                                                    <input type='radio' name='mainStatus' value='1' checked> On
                                                    <input type='radio' name='mainStatus' value='0'> Off";
        }
        if($mainStatus == 0){    
            echo "                                  Status: 
                                                    <input type='radio' name='mainStatus' value='1'> On
                                                    <input type='radio' name='mainStatus' value='0' checked> Off";
        }
        echo "                                  <br>";
        
        if($mainFan == 0){
		echo "				Fan Speed: 
                                                <input type='radio' name='mainFanSpeed' value='0' checked> Low
						<input type='radio' name='mainFanSpeed' value='1'> High
						<input type='radio' name='mainFanSpeed' value='2'> Auto";
        }
        if($mainFan == 1){
		echo "				Fan Speed: 
                                                <input type='radio' name='mainFanSpeed' value='0'> Low
						<input type='radio' name='mainFanSpeed' value='1' checked> High
						<input type='radio' name='mainFanSpeed' value='2'> Auto";
        }
        if($mainFan == 2){
		echo "				Fan Speed: 
                                                <input type='radio' name='mainFanSpeed' value='0'> Low
						<input type='radio' name='mainFanSpeed' value='1'> High
						<input type='radio' name='mainFanSpeed' value='2' checked> Auto"; 
        }
        echo "                                  <br>
						Temperature: 
						<input type='text' name='mainTemp' value=$mainTemp> deg F
						<br>                                             
                                                <h3>Bedroom Unit</h3>";            

        if(bedStatus == 1){

            echo "
                                                    Status: <input type='radio' name='bedState' value='1' checked> On
                                                    <input type='radio' name='bedState' value='0'> Off";
        }
        if(bedStatus == 0){    
            echo "                                  Status: <input type='radio' name='bedState' value='1'> On
                                                    <input type='radio' name='bedState' value='0' checked> Off";
        }
        echo "                                  <br>";


						
        
        if(bedFan == 0){
		echo "				Fan Speed: <input type='radio' name='bedFanSpeed' value='0' checked> Low
						<input type='radio' name='bedFanSpeed' value='1'> High
						<input type='radio' name='bedFanSpeed' value='2'> Auto";
        }
        if(bedFan == 1){
		echo "				Fan Speed: <input type='radio' name='bedFanSpeed' value='0'> Low
						<input type='radio' name='bedFanSpeed' value='1' checked> High
						<input type='radio' name='bedFanSpeed' value='2'> Auto";
        }
        if(bedFan == 2){
		echo "				Fan Speed: <input type='radio' name='bedFanSpeed' value='0'> Low
						<input type='radio' name='bedFanSpeed' value='1'> High
						<input type='radio' name='bedFanSpeed' value='2' checked> Auto";
        }
        echo "                                  <br>
						Temperature: 
						<input type='text' name='bedTemp' value=$bedTemp> deg F
						<br>
						<input type='submit'>
				
					</form>
		";
?>
				</div>
			</div> 

	
	</body>

</html>
