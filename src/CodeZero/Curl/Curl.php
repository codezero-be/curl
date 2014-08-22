<?php namespace CodeZero\Curl; 

class Curl {

    /**
     * cURL Resource Handle
     *
     * @var resource
     */
    private $curl;

    /**
     * cURL Response
     *
     * @var mixed
     */
    private $response;

    /**
     * Constructor
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
        if ($this->curl != null)
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
     */
    public function setOption($option, $value)
    {
        if ($this->curl == null)
        {
            $this->initialize();
        }

        return curl_setopt($this->curl, $option, $value);
    }

    /**
     * Set cURL options
     *
     * @param array $options
     *
     * @return bool
     */
    public function setOptions(array $options)
    {
        if ($this->curl == null)
        {
            $this->initialize();
        }

        return curl_setopt_array($this->curl, $options);
    }

    /**
     * Send the cURL request (will initialize if needed and set options if provided)
     *
     * !!! Options that have already been set are not automatically reset !!!
     *
     * @param array $options
     *
     * @return mixed
     */
    public function sendRequest(array $options = [])
    {
        if ($this->curl == null)
        {
            $this->initialize();
        }

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
     * @return mixed
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
        if ($this->curl == null)
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
     */
    public function urlEncode($string)
    {
        if ($this->curl == null)
        {
            $this->initialize();
        }

        return curl_escape($this->curl, $string);
    }

    /**
     * Decodes the given URL encoded string
     *
     * @param string $string
     *
     * @return string|bool
     */
    public function urlDecode($string)
    {
        if ($this->curl == null)
        {
            $this->initialize();
        }

        return curl_unescape($this->curl, $string);
    }

    /**
     * Reset all cURL options
     *
     * @return void
     */
    public function reset()
    {
        if ($this->curl == null)
        {
            $this->initialize();
        }
        else
        {
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
        if ($this->curl != null)
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

}
