<?php
    
	include 'db-config.php';
	error_reporting(0);

	// This function will run within each post array including multi-dimensional arrays
	function ExtendedAddslash(&$params)
	{
	        foreach ($params as &$var) {
	            // check if $var is an array. If yes, it will start another ExtendedAddslash() function to loop to each key inside.
	            is_array($var) ? ExtendedAddslash($var) : $var=addslashes($var);
	        }
	}

     // Initialize ExtendedAddslash() function for every $_POST variable
    ExtendedAddslash($_POST);    

    $email = $_POST['email'];
    $first = $_POST['first'];
    $last = $_POST['last'];

    settype($email, 'string');
    $connect = mysqli_connect($host_name, $user_name, $password, $database);
	$email = $connect->real_escape_string($email);
    $dupesql = "SELECT * FROM users WHERE email ='".$email."'";
	$sql = "INSERT INTO users (email,first,last) VALUES ('".$email."','".$first."','".$last."')";
    
	$reponse = array();

    if (mysqli_connect_errno()) {
		$response->status = "error";
		$response->status_message = "Database connection error.";    	
    	$response->message = "Having technical difficulties";
    } else {
			$duperaw = $connect->query($dupesql);		
			if ($duperaw->num_rows > 0) {
				$response->status = "error";
				$response->status_message = "duplicate";
				$response->message = "This email is already registered!";
			} else {
				if ($connect->query($sql) === TRUE) {
					$response->status = "success";
					$response->status_message = "insertion complete";
				    $response->message = "Thanks for submitting your email. <br><br>  You'll be hearing from us soon!";
				} else {
					$response->status = "error";
					$response->status_message = "Error: " . $sql . "<br>" . $connect->error;					
					$response->message = "Oops!  Didn't work.  Please try again later.";
				}
			}
	}	
	echo json_encode($response);
	$connect->close();
?>
