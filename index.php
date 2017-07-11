<?php
	require_once "API.php";
	
        // API takes URIs of form: 
        //      Get:  key
        //      Post: key/unit#/information
	// Requests from the same server don't have a HTTP_ORIGIN header
	if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
		$_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
	}

	try {
		$API = new API($_SERVER['REQUEST_URI']);
		echo $API->processAPI();
	} catch (Exception $e) {
                http_response_code(400);
		echo json_encode(Array('error' => $e->getMessage()));
	}
?>
