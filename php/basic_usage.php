<!DOCTYPE html>
<?
	/*
	 * Connecting to the AmeriCommerce Web Service API and utilizing some basic data retrieval methods.
	 *
	 * Copyright AmeriCommerce L.P.
	 * www.americommerce.com
	 *
	 * 10/16/2012
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
		"UserName" => API_USERNAME,
		"Password" => API_PASSWORD,
		"SecurityToken" => SECURITY_TOKEN
	);

	try {
		// Create a new instance of the client pointing to the wsdl endpoint.
		$client = new ACSoapClient("https://".STORE_DOMAIN."/store/ws/AmeriCommerceDb.asmx?wsdl");

		// Create a new header object specifying the namespace, header object type, and hash containing the header details.
		$header = new SoapHeader(AC_NAMESPACE, "AmeriCommerceHeaderInfo", $header_info);

		// Set the header on the client so we can authenticate the request.
		$client->__setSoapHeaders($header);

		// Fetch a product by its ID from the API. Set this ID to something relevant for your store.
		$response = $client->Product_GetByKey(array("piitemID" => 1670));
		// The response is an object that has our result.
		$product = $response->Product_GetByKeyResult;

		// Fill a collection on the product, notice that the product itself is passed in here.
		$response = $client->Product_FillProductVariantCollection(array("poProductTrans" => $product));
		// We get back the same product with the collection filled.
		$product = $response->Product_FillProductVariantCollectionResult;
	}
	catch(Exception $e) {
		print_r($e);
	}

?>
<html>
<head>
	<title>AmeriCommerce API Connection Demo</title>
</head>
<body>
	<h3>Item ID</h3>
	<p><?= $product->itemID->Value ?></p>

	<h3>Item Name</h3>
	<p><?= $product->itemName ?></p>

	<h3>Item Number</h3>
	<p><?= $product->itemNr ?></p>

	<h3>Price</h3>
	<p><?= $product->price->Value ?></p>

	<h3>Variants</h3>
	<?
		foreach($product->ProductVariantColTrans->ProductVariantTrans as $variant) {
			?><div>group <?= $variant->groupID->Value ?>: <?= $variant->shortDesc ?></div><?
		}
	?>
</body>
</html>