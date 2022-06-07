<?php 

	$executionStartTime = microtime(true);

	include("config.php"); 

	$conn = new mysqli($cd_host, $cd_user, $cd_password, $cd_dbname, $cd_port, $cd_socket);

	if (mysqli_connect_errno()) { 
		$output['status']['code'] = "300"; 
		$output['status']['name'] = "failure"; 
		$output['status']['description'] = "database unavailable"; 
		$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms"; 
		$output['data'] = []; mysqli_close($conn); 

		header('Content-Type: application/json; charset=UTF-8', true, '300'); 
		echo json_encode($output); 

		exit; 
	}

	$query = $conn->prepare('DELETE FROM inventory WHERE id = ?'); 

	$query->bind_param("i", $_GET['id']); 

	$query->execute(); 

	if (false === $query) { 
		$output['status']['code'] = "400"; 
		$output['status']['name'] = "executed"; 
		$output['status']['description'] = "query failed";	
		$output['data'] = []; mysqli_close($conn); 

		header('Content-Type: application/json; charset=UTF-8', true, '400'); 
		echo json_encode($output); 

		exit; 
	} 

	$output['status']['code'] = "200"; 
	$output['status']['name'] = "ok"; 
	$output['status']['description'] = "success"; 
	$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms"; 
	$output['data'] = []; 
	
	mysqli_close($conn); 

	header('Content-Type: application/json; charset=UTF-8', true, '200'); 
	echo json_encode($output); 

?>
