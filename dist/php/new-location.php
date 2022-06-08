<?php $executionStartTime = microtime(true); include("config.php"); $conn = new mysqli($cd_host, $cd_user, $cd_password, $cd_dbname, $cd_port, $cd_socket); if (mysqli_connect_errno()) { $output['status']['code'] = "300"; $output['status']['name'] = "failure"; $output['status']['description'] = "database unavailable"; $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms"; $output['data'] = []; mysqli_close($conn); header('Content-Type: application/json; charset=UTF-8', true, '300'); echo json_encode($output); exit; }	$query = $conn->prepare("SELECT id FROM locations WHERE name = ?"); $query->bind_param("s", ucwords($_REQUEST['name'])); $query->execute(); $resultA = null; $query->bind_result($resultA); $query->fetch(); if ($resultA) { $output['status']['code'] = "409"; $output['status']['name'] = "executed"; $output['status']['description'] = "location already exists";	mysqli_close($conn); header('Content-Type: application/json; charset=UTF-8', true, '409'); echo json_encode($output); exit; } $query = $conn->prepare("INSERT INTO `locations` (`id`, `name`) VALUES (DEFAULT, ?)"); $locName = ucwords($_REQUEST['name']); $query->bind_param("s", $locName); $query->execute(); $result = $conn->query('SELECT name, id FROM locations ORDER BY id DESC LIMIT 1'); $response = mysqli_fetch_assoc($result); if (false === $query) { $output['status']['code'] = "400"; $output['status']['name'] = "executed"; $output['status']['description'] = "query failed";	$output['data'] = []; mysqli_close($conn); header('Content-Type: application/json; charset=UTF-8', true, '400'); echo json_encode($output); exit; } $output['status']['code'] = "200"; $output['status']['name'] = "ok"; $output['status']['description'] = "success"; $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms"; $output['data'] = $response; mysqli_close($conn); header('Content-Type: application/json; charset=UTF-8', true, '200'); echo json_encode($output); ?>
