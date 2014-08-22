# Simple cURL Wrapper #

[![Build Status](https://travis-ci.org/codezero-be/curl.svg?branch=master)](https://travis-ci.org/codezero-be/curl)
[![Latest Stable Version](https://poser.pugx.org/codezero/curl/v/stable.svg)](https://packagist.org/packages/codezero/curl)
[![Total Downloads](https://poser.pugx.org/codezero/curl/downloads.svg)](https://packagist.org/packages/codezero/curl)
[![License](https://poser.pugx.org/codezero/curl/license.svg)](https://packagist.org/packages/codezero/curl)

This package wraps most of the cURL functions in a dedicated `Curl` class, making it feel more object oriented and a little easier to use.

More importantly, the `Request` class provides some friendly methods that will set the most commonly used cURL options for you, so you don't have to memorize these.

This is a **simple** cURL wrapper. Some features are not supported (yet):

- Including headers in the response output (`CURLOPT_HEADER`)
- cURL multi handles
- cURL share handles

## Installation ##

You can download this package, or install it through Composer:

    "require": {
    	"codezero/curl": "1.*"
    }

## Usage ##

Send requests, the easy way!

##### Create a Request instance: #####

	// Instantiate Dependencies (or let an IoC container take care of this)
    $curl = new \CodeZero\Curl\Curl();
    $optionParser = new \CodeZero\Curl\OptionParser();
    $responseFactory = new \CodeZero\Curl\ResponseFactory();

    // Instantiate Request
    $request = new \CodeZero\Curl\Request($curl, $optionParser, $responseFactory);

You can now use this for all of your requests. You don't need to create a new instance for every request.

##### Configure your request: #####

	$url = 'http://my.site/api';
    $data = ['do' => 'something', 'with' => 'this']; //=> Optional
    $headers = ['Some Header' => 'Some Value']; //=> Optional

	// If you want you can set custom cURL options, before sending the request
	$request->setOption(CURLOPT_USERAGENT, 'My User Agent');

	// Or unset one...
	$request->unsetOption(CURLOPT_USERAGENT);

Only the URL, data and headers option, and a few options that are required for the given request method will be reset automatically on every request. Custom options will remain set until you unset them. An overview of all cURL options can be found here: [http://php.net/manual/en/function.curl-setopt.php](http://php.net/manual/en/function.curl-setopt.php "cURL options")

##### Send the request: (one of the following) #####

	$response = $request->get($url, $data, $headers);
	$response = $request->post($url, $data, $headers);
	$response = $request->put($url, $data, $headers);
	$response = $request->patch($url, $data, $headers);
	$response = $request->delete($url, $data, $headers);

All of these methods will return an instance of the `CodeZero\Curl\Response` class.

##### Get the response body #####

	$body = $response->getBody();

##### Get additional request info #####

	// Array with all info
	$info = $response->info()->getList();

	// Specific information
	$httpCode = $reponse->info()->getHttpCode(); //=> "200"
	$responseType = $response->info()->getResponseType(); //=> "application/json"
	$responseCharset = $response->info()->getResponseCharset(); //=> "UTF-8" 

For an overview of all the available info, take a look at the `CodeZero\Curl\ResponseInfo` class or refer to [http://php.net/manual/en/function.curl-getinfo.php](http://php.net/manual/en/function.curl-getinfo.php "cURL info")
 
## Exceptions ##

#### cURL issues ####

A `CodeZero\Curl\CurlException` will be thrown, if there was a problem initializing cURL. This will probably be the case if the cURL extension is not loaded by PHP, or if there is some other local server issue.

#### Request issues ####

A `CodeZero\Curl\RequestException` will be thrown, if cURL was unable to execute the request. This might be the case if your request is not properly configured (unsupported protocol, etc.). Find more information about these kind of errors on [http://curl.haxx.se/libcurl/c/libcurl-errors.html](http://curl.haxx.se/libcurl/c/libcurl-errors.html "cURL errors") 

#### Response issues ####

HTTP response errors >= 400 will not throw an exception, unless you set the `CURLOPT_FAILONERROR` cURL option to `true`. If you do, these errors will also throw a `CodeZero\Curl\RequestException`, with the proper error message.

Another way to handle HTTP errors is to check the `$httpCode` value:

	if ($httpCode >= 400)
	{
		// Handle error...
		// or throw your own exception...
	}

## Curl Class ##

You can also use the `Curl` class instead of the `Request` class. The difference is that you will need to provide all of the cURL options yourself and you will just get the raw response back instead of the `Response` object.

#### Quick example: ####

    $options = [
        CURLOPT_URL => 'http://my.site/api',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPGET => true,
        CURLOPT_RETURNTRANSFER => true
    ];

    $curl = new \CodeZero\Curl\Curl();

	// Send & get results
    $responseBody = $curl->sendRequest($options); //=> Returns the actual output
	$info = $curl->getRequestInfo(); //=> Returns info array

	// Get errors
	$error = $curl->getError(); //=> For PHP < 5.5.0 this is the same as $curl->getErrorDescription()
	$errorCode = $curl->getErrorCode();
	$errorDescription = $curl->getErrorDescription();

	// Close cURL resource
	$curl->close();

Options are **not reset** automatically after each request. If you need to reset them, you can either run `$curl->close()` to force a new cURL resource to be initialized at the next request, or you can call `$curl->reset()`.

#### Long example: ####

The following will do the exact same thing as the previous example:

    $options = [
        CURLOPT_URL => 'http://my.site/api',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPGET => true,
        CURLOPT_RETURNTRANSFER => true
    ];

    $curl = new \CodeZero\Curl\Curl();

	// Setup & send
	$curl->initialize();
	$curl->setOptions($options);
    $curl->sendRequest();

	// Get results
	$responseBody = $curl->getResponse(); //=> Returns the actual output
	$info = $curl->getRequestInfo(); //=> Returns info array

	// Get errors
	$error = $curl->getError();//=> For PHP < 5.5.0 this is the same as $curl->getErrorDescription()
	$errorCode = $curl->getErrorCode();
	$errorDescription = $curl->getErrorDescription();

	// Close cURL resource
	$curl->close();

#### Error handling: ####

The `Curl` class will only throw a `CodeZero\Curl\CurlException`, when there is some cURL initialization issue. You will need to check the error code to determine if the request had any problems:

	if ($errorCode > 0)
	{
		// Do something...
	}
