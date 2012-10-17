<?php
	/*
	* Connecting to the AmeriCommerce Web Service API and utilizing some basic data retrieval and update methods.
	*
	* Copyright AmeriCommerce L.P.
	* www.americommerce.com
	*
	* 10/17/2012
	*/
	
	/* These constants can be replaced by any configuration your app needs to do. This just contains all the information
	* you need to connect to the web service and authenticate. */
	define("STORE_DOMAIN", "mystore.americommerce.com");
	define("API_USERNAME", "UserName");
	define("API_PASSWORD", "Password");
	define("SECURITY_TOKEN", "SecurityToken");
	
	// This namespace must be defined and cannot be changed.
	define("AC_NAMESPACE", "http://www.americommerce.com");
	
	/* Due to the nature of .NET SOAP Web Services, we have to create a subclass of the native PHP SoapClient and
	* override the way it handles namespaces. The default way the request is formatted is not interpreted by the
	* server correctly, especially when passing in a complex object as a parameter. */
	class ACSoapClient extends SoapClient {
		function __doRequest($request, $location, $action, $version) {
			$namespace = AC_NAMESPACE;
			
			$request = preg_replace('/<ns1:(\w+)/', '<$1 xmlns="'.$namespace.'"', $request, 1);
			$request = preg_replace('/<ns1:(\w+)/', '<$1', $request);
			$request = str_replace(array('/ns1:', 'xmlns:ns1="'.$namespace.'"'), array('/', 'xmlns="'.$namespace.'"'), $request);
			
			// parent call
			return parent::__doRequest($request, $location, $action, $version);
		}
	}

	// Create a hash containing the header information for authentication.
	$header_info = array(
		"UserName" 		=> API_USERNAME,
		"Password" 		=> API_PASSWORD,
		"SecurityToken" => SECURITY_TOKEN
		);
		
	try {
			
	    // Create a new instance of the client pointing to the wsdl endpoint.
	    $client = new ACSoapClient("https://".STORE_DOMAIN."/store/ws/AmeriCommerceDb.asmx?wsdl");
	
	    // Create a new header object specifying the namespace, header object type, and hash containing the header details.
	    $header = new SoapHeader("http://www.americommerce.com", "AmeriCommerceHeaderInfo", $header_opts, false);
	
	    // Set the header on the client instance so that we can authenticate.
	    $client->__setSoapHeaders($header);
		
		// Grab the OrderID from the last order placed		
		$lastorder = $client->Order_GetLastOrderID();
		// Fetch the order details
		$order = $client->Order_GetByKey(array("piorderID" => $lastorder->Order_GetLastOrderIDResult))->Order_GetByKeyResult;
		// Fetch the customer details from the order
		$customer = $client->Customer_GetByKey(array("picustomerID" => $order->customerID->Value))->Customer_GetByKeyResult;
		
		// Lets update some of the customer information
		$customer->lastName = "John";
		$customer->firstName = "Doe";
		$Customer->email = "newemail@thedomain.com";
		
		// After updating the customer information, we need to save it on the server
		$savedCustomer = $client->Customer_SaveAndGet(array("poCustomerTrans" => $customer))->Customer_SaveAndGetResult;
			
	} catch(Exception $e) {
		print_r($e);
	}	
?>
<html>
	<head>
		<title>Example Customer_SaveAndGet Method</title>
	</head>
	<body>
		
		<h3>First Name</h3>
		<?= $savedCustomer->firstName ?><br/>
		
		<h3>Last Name</h3>
		<?= $savedCustomer->lastName ?><br/>
		
		<h3>Email Address</h3>
		<?= $savedCustomer->email ?><br/>
		
	</body>
</html>	