<?php namespace CodeZero\Curl;

class ResponseInfo {

    /**
     * Array Request Information
     *
     * @var array
     */
    private $info;

    /**
     * Constructor
     *
     * @param array $info
     */
    public function __construct(array $info)
    {
        $this->info = $info;
    }

    public function getUrl()
    {
        return $this->fetchInfo('url');
    }

    public function getContentType()
    {
        return $this->fetchInfo('content_type');
    }

    public function getHttpCode()
    {
        return $this->fetchInfo('http_code');
    }

    public function getHeaderSize()
    {
        return $this->fetchInfo('header_size');
    }

    public function getCertInfo()
    {
        return $this->fetchInfo('certinfo');
    }

    public function getSslVerifyResult()
    {
        return $this->fetchInfo('ssl_verify_result');
    }

    public function getRedirectCount()
    {
        return $this->fetchInfo('redirect_count');
    }

    public function getTotalTime()
    {
        return $this->fetchInfo('total_time');
    }

    public function getNameLookupTime()
    {
        return $this->fetchInfo('namelookup_time');
    }

    public function getConnectTime()
    {
        return $this->fetchInfo('connect_time');
    }

    public function getPreTransferTime()
    {
        return $this->fetchInfo('pretransfer_time');
    }

    public function getStartTransferTime()
    {
        return $this->fetchInfo('starttransfer_time');
    }

    public function getRedirectTime()
    {
        return $this->fetchInfo('redirect_time');
    }

    public function getFileTime()
    {
        return $this->fetchInfo('filetime');
    }

    public function getRequestSize()
    {
        return $this->fetchInfo('request_size');
    }

    public function getDownloadSize()
    {
        return $this->fetchInfo('size_download');
    }

    public function getDownloadContentLength()
    {
        return $this->fetchInfo('download_content_length');
    }

    public function getDownloadSpeed()
    {
        return $this->fetchInfo('speed_download');
    }

    public function getUploadSize()
    {
        return $this->fetchInfo('size_upload');
    }

    public function getUploadContentLength()
    {
        return $this->fetchInfo('upload_content_length');
    }

    public function getUploadSpeed()
    {
        return $this->fetchInfo('speed_upload');
    }

    /**
     * Get the array with all of the request information
     *
     * @return array
     */
    public function getList()
    {
        return $this->info;
    }

    /**
     * Search the information array for the specified key and return the value
     *
     * @param string $key
     *
     * @return mixed
     */
    private function fetchInfo($key)
    {
        return array_key_exists($key, $this->info)
            ? $this->info[$key]
            : null;
    }

}