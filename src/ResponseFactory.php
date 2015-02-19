<?php namespace CodeZero\Curl;

class ResponseFactory
{
    /**
     * Make a response
     *
     * @param $responseBody
     * @param array $responseInfo
     *
     * @return Response
     */
    public function make($responseBody, array $responseInfo)
    {
        $info = $this->makeResponseInfo($responseInfo);
        $response = $this->makeResponse($responseBody, $info);

        return $response;
    }

    /**
     * Make a ResponseInfo instance
     *
     * @param array $responseInfo
     *
     * @return ResponseInfo
     */
    private function makeResponseInfo(array $responseInfo)
    {
        return new ResponseInfo($responseInfo);
    }

    /**
     * Make a Response instance
     *
     * @param $responseBody
     * @param ResponseInfo $responseInfo
     *
     * @return Response
     */
    private function makeResponse($responseBody, ResponseInfo $responseInfo)
    {
        return new Response($responseBody, $responseInfo);
    }
}
