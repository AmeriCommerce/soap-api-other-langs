<!--

  Making a basic API connection from PHP.

  Copyright AmeriCommerce L.P.
  www.americommerce.com

  June 29, 2012

-->

<?

  // These constants can be replaced by any configuration your app needs to do. This just contains all the information
  // you need to connect to the web service and authenticate.
  define("STORE_DOMAIN", "mystore.americommerce.com");
  define("API_USERNAME", "UserName");
  define("API_PASSWORD", "Password");
  define("SECURITY_TOKEN", "SecurityToken");

  try {

    // Create a hash containing the header information for authentication.
    $header_info = array(
      "UserName" => API_USERNAME,
      "Password" => API_PASSWORD,
      "SecurityToken" => SECURITY_TOKEN
    );

    // Create a new instance of the client pointing to the wsdl endpoint.
    $client = new SoapClient("https://".STORE_DOMAIN."/store/ws/AmeriCommerceDb.asmx?wsdl");

    // Create a new header object specifying the namespace, header object type, and hash containing the header details.
    $header = new SoapHeader("http://www.americommerce.com", "AmeriCommerceHeaderInfo", $header_info, false);

    // Set the header on the client instance so that we can authenticate.
    $client->__setSoapHeaders($header);

    // Issue a request to perform an operation to test out the API. This request is calling Order_GetByKey and passing
    // it an order ID of 100026. The argument for the call should be hash containing the parameters that the web service
    // method expects.
    $result = $client->Order_GetByKey(array("piorderID" => 100026));
    $order = $result->Order_GetByKeyResult;

    // Capture the customer information related to the order that we retrieved above.
    $result = $client->Customer_GetByKey(array("picustomerID" => $order->customerID->Value));
    $customer = $result->Customer_GetByKeyResult;
  }
  catch(Exception $e) {
    print_r($e);
  }

?>

<html>
  <head>
    <title>AmeriCommerce API Demo</title>
  </head>

  <body>
    <div><strong>First Name</strong>: <?= $customer->firstName ?></div>
    <div><strong>Last Name</strong>: <?= $customer->lastName ?></div>
    <div><strong>Order ID</strong>: <?= $order->orderID->Value ?></div>
    <div><strong>Total</strong>: <?= $order->total->Value ?></div>
    <br/>
    <div><strong>Order Data Dump</strong>: <br/> <? print_r($order); ?></div>
    <br/>
    <div><strong>Customer Data Dump</strong>: <br/> <? print_r($customer); ?></div>
  </body>
</html>
