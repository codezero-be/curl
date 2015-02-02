<?php namespace CodeZero\Curl;

class Response {

    /**
     * The Response Body
     *
     * @var mixed
     */
    private $rawResponse;

    /**
     * Additional Response Information
     *
     * @var ResponseInfo
     */
    private $responseInfo;

    /**
     * Constructor
     *
     * @param $rawResponse
     * @param ResponseInfo $responseInfo
     */
    public function __construct($rawResponse, ResponseInfo $responseInfo)
    {
        $this->rawResponse = $rawResponse;
        $this->responseInfo = $responseInfo;
    }

    /**
     * Response information
     *
     * @return ResponseInfo
     */
    public function info()
    {
        return $this->responseInfo;
    }

    /**
     * Get the response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->rawResponse;
    }

    /**
     * Output the raw response
     *
     * @return string
     */
    public function __toString()
    {
        return $this->rawResponse;
    }

}