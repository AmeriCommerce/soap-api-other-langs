# Making a basic API connection from Ruby. Uses the gem 'savon' to make this easier to work with.
# More information about the gem available at: http://savonrb.com
#
# Copyright AmeriCommerce L.P.
# www.americommerce.com
#
# Updated: 5/10/2013
# Now uses version 2 of savon

require 'rubygems'
require 'savon'

# These constants can be replaced by any configuration your app needs to do. This just contains all the information
# you need to connect to the web service and authenticate.
STORE_DOMAIN = "mystore.americommerce.com"
API_USERNAME = "UserName"
API_PASSWORD = "Password"
SECURITY_TOKEN = "SecurityToken"

# Create a new hash containing the header information for authentication.
ac_header = {
  'AmeriCommerceHeaderInfo' => {
    'UserName' => API_USERNAME,
    'Password' => API_PASSWORD,
    'SecurityToken' => SECURITY_TOKEN
  }
}

namespaces = {
  'xmlns' => 'http://www.americommerce.com'
}

# Create a new instance of the client and tell it where the wsdl endpoint is.
client = Savon.client({
  :ssl_verify_mode  => :none, 
  :wsdl             => "https://#{STORE_DOMAIN}/store/ws/AmeriCommerceDb.asmx?wsdl",
  :soap_header      => ac_header,
  :namespaces       => namespaces
})

# Issue a request to perform an operation to test out the API. The namespace here is explicit. The example here is
# calling Order_GetByKey and passing in an order ID of 100026. The body of the request should be a hash of the
# parameters that the web service method expects.
response = client.call(:order_get_by_key, message: { 'piorderID' => 100004 })

# The response is a hash containing a response hash which in turn contains a result object. The names are prefixed
# with the name of the API operation that you called. These names are formatted in a Ruby conventions.
order = response.body[:order_get_by_key_response][:order_get_by_key_result]

# Print out what we got back, just to make sure it's all working.
puts order.to_hash
