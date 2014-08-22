<?php namespace CodeZero\Curl; 

class OptionParser {

    /**
     * Build URL with query string
     *
     * @param string $url
     * @param array $data
     *
     * @return string
     */
    public function parseUrl($url, array $data)
    {
        return (empty($data)) ? $url : $url . '?' . $this->parseData($data);
    }

    /**
     * Parse data array into query string
     *
     * @param array $data
     *
     * @return string
     */
    public function parseData(array $data)
    {
        return http_build_query($data);
    }

    /**
     * Parse header key/value array into value-only array
     *
     * @param array $headers
     *
     * @return array
     */
    public function parseHeaders(array $headers)
    {
        $parsed = [];

        foreach ($headers as $key => $val)
        {
            $parsed[] = $key . ': ' . $val;
        }

        return $parsed;
    }

} 