<?php namespace CodeZero\Curl; 

class Curl
{
    /**
     * cURL Resource Handle
     *
     * @var resource
     */
    private $curl;

    /**
     * cURL Response
     *
     * @var bool|string
     */
    private $response;

    /**
     * Constructor
     *
     * @throws CurlException
     */
    public function __construct()
    {
        if ( ! extension_loaded('curl'))
        {
            throw new CurlException('The cURL extension is not installed');
        }
    }

    /**
     * Initialize a new cURL resource
     *
     * @return bool
     * @throws CurlException
     */
    public function initialize()
    {
        if ($this->isInitialized())
        {
            $this->close();
        }

        if ( ! ($this->curl = curl_init()))
        {
            throw new CurlException('Could not initialize a cURL resource');
        }

        return true;
    }

    /**
     * Check if a cURL resource has been initialized
     *
     * @return bool
     */
    public function isInitialized()
    {
        return $this->curl != null;
    }

    /**
     * Set cURL option
     *
     * @param int $option
     * @param mixed $value
     *
     * @return bool
     * @throws CurlException
     */
    public function setOption($option, $value)
    {
        $this->autoInitialize();

        return curl_setopt($this->curl, $option, $value);
    }

    /**
     * Set cURL options
     *
     * @param array $options
     *
     * @return bool
     * @throws CurlException
     */
    public function setOptions(array $options)
    {
        $this->autoInitialize();

        return curl_setopt_array($this->curl, $options);
    }

    /**
     * Send the cURL request (will initialize if needed and set options if provided)
     *
     * !!! Options that have already been set are not automatically reset !!!
     *
     * @param array $options
     *
     * @return bool|string
     * @throws CurlException
     */
    public function sendRequest(array $options = [])
    {
        $this->autoInitialize();

        if ( ! empty($options))
        {
            if ( ! $this->setOptions($options))
            {
                return false;
            }
        }

        $this->response = curl_exec($this->curl);

        return $this->response;
    }

    /**
     * Get the response of the last cURL request
     *
     * @return bool|string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get additional information about the last cURL request
     *
     * @param string $key
     *
     * @return string|array
     */
    public function getRequestInfo($key = null)
    {
        if ( ! $this->isInitialized())
        {
            return $key ? '' : [];
        }

        return $key ? curl_getinfo($this->curl, $key) : curl_getinfo($this->curl);
    }

    /**
     * Get the error from the last cURL request
     *
     * @return string
     */
    public function getError()
    {
        if ( ! function_exists('curl_strerror'))
        {
            return $this->getErrorDescription();
        }

        // PHP >= 5.5.0
        return curl_strerror($this->getErrorCode());
    }

    /**
     * Get the error code from the last cURL request
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->curl ? curl_errno($this->curl) : 0;
    }

    /**
     * Get the error description from the last cURL request
     *
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->curl ? curl_error($this->curl) : '';
    }

    /**
     * URL encodes the given string
     *
     * @param string $string
     *
     * @return string|bool
     * @throws CurlException
     */
    public function urlEncode($string)
    {
        return $this->parseUrl($string, false);
    }

    /**
     * Decodes the given URL encoded string
     *
     * @param string $string
     *
     * @return string|bool
     * @throws CurlException
     */
    public function urlDecode($string)
    {
        return $this->parseUrl($string, true);
    }

    /**
     * Reset all cURL options
     *
     * @return void
     * @throws CurlException
     */
    public function reset()
    {
        if ( ! $this->isInitialized() || ! function_exists('curl_reset'))
        {
            $this->initialize();
        }
        else
        {
            // PHP >= 5.5.0
            curl_reset($this->curl);

            $this->response = null;
        }
    }

    /**
     * Close the cURL resource
     *
     * @return void
     */
    public function close()
    {
        if ($this->isInitialized())
        {
            curl_close($this->curl);

            $this->curl = null;
            $this->response = null;
        }
    }

    /**
     * Get the cURL version
     *
     * @return string
     */
    public function getVersion()
    {
        return curl_version()['version'];
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Initialize cURL if it has not been initialized yet
     *
     * @throws CurlException
     */
    private function autoInitialize()
    {
        if ( ! $this->isInitialized())
        {
            $this->initialize();
        }
    }

    /**
     * Encode or decode a URL
     *
     * @param string $string
     * @param bool $decode
     *
     * @return bool|string
     */
    private function parseUrl($string, $decode)
    {
        $this->autoInitialize();

        $function = $decode ? 'curl_unescape' : 'curl_escape';

        if ( ! function_exists($function))
        {
            return rawurlencode($string);
        }

        // PHP >= 5.5.0
        return call_user_func($function, $this->curl, $string);
    }
}
