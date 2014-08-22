<?php namespace CodeZero\Curl;

class Request {

    /**
     * cURL Wrapper
     *
     * @var Curl
     */
    private $curl;

    /**
     * Option Parser
     *
     * @var OptionParser
     */
    private $optionParser;

    /**
     * Response Factory
     *
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * cURL Options
     *
     * @var array
     */
    private $options = [];

    /**
     * Constructor
     *
     * @param Curl $curl
     * @param OptionParser $optionParser
     * @param ResponseFactory $responseFactory
     */
    public function __construct(Curl $curl, OptionParser $optionParser, ResponseFactory $responseFactory)
    {
        $this->curl = $curl;
        $this->optionParser = $optionParser;
        $this->responseFactory = $responseFactory;

        $this->setDefaultOptions();
    }

    /**
     * Send GET request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return Response
     */
    public function get($url, array $data = [], array $headers = [])
    {
        $url = $this->optionParser->parseUrl($url, $data);

        $this->unsetOption(CURLOPT_POST);

        $this->setOptions([
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPGET => true
        ]);

        return $this->send($url, [], $headers);
    }

    /**
     * Send POST request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return Response
     */
    public function post($url, array $data = [], array $headers = [])
    {
        $this->unsetOption(CURLOPT_HTTPGET);

        $this->setOptions([
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST => true
        ]);

        return $this->send($url, $data, $headers);
    }

    /**
     * Send PUT request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return Response
     */
    public function put($url, array $data = [], array $headers = [])
    {
        $this->unsetOptions([CURLOPT_HTTPGET, CURLOPT_POST]);

        $this->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');

        return $this->send($url, $data, $headers);
    }

    /**
     * Send PATCH request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return Response
     */
    public function patch($url, array $data = [], array $headers = [])
    {
        $this->unsetOptions([CURLOPT_HTTPGET, CURLOPT_POST]);

        $this->setOption(CURLOPT_CUSTOMREQUEST, 'PATCH');

        return $this->send($url, $data, $headers);
    }

    /**
     * Send DELETE request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return Response
     */
    public function delete($url, array $data = [], array $headers = [])
    {
        $this->unsetOptions([CURLOPT_HTTPGET, CURLOPT_POST]);

        $this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->send($url, $data, $headers);
    }

    /**
     * Set or overwrite a cURL option
     *
     * @param $option
     * @param $value
     *
     * @return void
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Set or overwrite multiple cURL options
     *
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value)
        {
            $this->setOption($option, $value);
        }
    }

    /**
     * Unset a cURL option
     *
     * @param $option
     *
     * @return void
     */
    public function unsetOption($option)
    {
        if (array_key_exists($option, $this->options))
        {
            unset($this->options[$option]);
        }
    }

    /**
     * Unset multiple cURL options
     *
     * @param array $options
     *
     * @return void
     */
    public function unsetOptions(array $options)
    {
        foreach ($options as $option)
        {
            $this->unsetOption($option);
        }
    }

    /**
     * Check if an option is set
     *
     * @param $option
     *
     * @return bool
     */
    public function isOptionSet($option)
    {
        return array_key_exists($option, $this->options);
    }

    /**
     * Get the value of an option if it is set
     *
     * @param $option
     *
     * @return mixed|null
     */
    public function getOptionValue($option)
    {
        if (array_key_exists($option, $this->options))
        {
            return $this->options[$option];
        }

        return null;
    }

    /**
     * Set basic authentication
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    public function setBasicAuthentication($username, $password)
    {
        $this->setOptions([
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$username:$password"
        ]);
    }

    /**
     * Unset basic authentication
     *
     * @return void
     */
    public function unsetBasicAuthentication()
    {
        $this->unsetOptions([CURLOPT_HTTPAUTH, CURLOPT_USERPWD]);
    }

    /**
     * Set default cURL options
     *
     * @return void
     */
    private function setDefaultOptions()
    {
        $this->setOptions([
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true, //=> return response instead of boolean
            CURLOPT_FAILONERROR => false //=> also return an error when there is a http error >= 400
        ]);
    }

    /**
     * Set cURL request URL
     *
     * @param string $url
     *
     * @return void
     */
    private function setUrl($url)
    {
        $this->setOption(CURLOPT_URL, $url);
    }

    /**
     * Set request post fields
     *
     * @param array $data
     *
     * @return void
     */
    private function setData(array $data)
    {
        if ( ! empty($data))
        {
            $this->setOption(CURLOPT_POSTFIELDS, $this->optionParser->parseData($data));
        }
        else
        {
            $this->unsetOption(CURLOPT_POSTFIELDS);
        }
    }

    /**
     * Set request headers
     *
     * @param array $headers
     *
     * @return void
     */
    private function setHeaders(array $headers)
    {
        if ( ! empty($headers))
        {
            $this->setOption(CURLOPT_HTTPHEADER, $this->optionParser->parseHeaders($headers));
        }
        else
        {
            $this->unsetOption(CURLOPT_HTTPHEADER);
        }
    }

    /**
     * Send a request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return Response
     * @throws RequestException
     */
    private function send($url, array $data = [], array $headers = [])
    {
        // Set incoming parameters as cURL options
        $this->setUrl($url);
        $this->setData($data);
        $this->setHeaders($headers);

        return $this->executeCurlRequest();
    }

    /**
     * Execute the cURL request
     *
     * @return Response
     * @throws RequestException
     */
    private function executeCurlRequest()
    {
        // Send the request and capture the response
        $rawResponse = $this->curl->sendRequest($this->options);

        // Fetch additional information about the request
        $responseInfo = $this->curl->getRequestInfo();

        // Get the error (if any)
        $errorCode = $this->curl->getErrorCode();
        $errorDescription = $this->curl->getErrorDescription();

        // Close Curl
        $this->curl->close();

        if ($errorCode > 0)
        {
            // There was a cURL error...
            throw new RequestException($errorDescription, $errorCode);
        }

        // Generate a response with the collected information
        return $this->responseFactory->make($rawResponse, $responseInfo);
    }

} 