<?php namespace spec\CodeZero\Curl;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CurlSpec extends ObjectBehavior {

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\Curl\Curl');
    }

    function it_initializes_a_curl_resource()
    {
        $this->initialize()->shouldReturn(true);
        $this->isInitialized()->shouldBe(true);
    }

    function it_initializes_and_sets_an_option()
    {
        $url = 'http://www.codezero.be/';

        $this->setOption(CURLOPT_URL, $url)->shouldReturn(true);
        $this->isInitialized()->shouldBe(true);
    }

    function it_initializes_and_sets_multiple_options()
    {
        $url = 'http://www.codezero.be/';

        $this->setOptions([CURLOPT_URL => $url, CURLOPT_HTTPGET => true])->shouldReturn(true);
        $this->isInitialized()->shouldBe(true);
    }

    function it_initializes_and_sends_a_request()
    {
        $options = [
            CURLOPT_URL => 'http://www.codezero.be/api/test/',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true
        ];

        $this->sendRequest($options)->shouldReturn('ok');
        $this->isInitialized()->shouldBe(true);
    }

    function it_returns_the_response()
    {
        $options = [
            CURLOPT_URL => 'http://www.codezero.be/api/test/',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true
        ];

        $this->sendRequest($options);

        $this->getResponse()->shouldReturn('ok');
    }

    function it_returns_an_array_with_additional_request_info()
    {
        $options = [
            CURLOPT_URL => 'http://www.codezero.be/api/test/',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true
        ];

        $this->sendRequest($options);

        $this->getRequestInfo()->shouldBeArray();
    }

    function it_returns_false_if_the_request_fails()
    {
        $options = [CURLOPT_URL => 'sptth://some.bogus.site/'];

        $this->sendRequest($options)->shouldReturn(false);
    }


    function it_gives_error_info_if_the_request_fails()
    {
        $options = [CURLOPT_URL => 'sptth://some.bogus.site/'];

        $this->sendRequest($options);

        $this->getErrorCode()->shouldReturn(1); //=> CURLE_UNSUPPORTED_PROTOCOL
        $this->getErrorDescription()->shouldReturn('Protocol sptth not supported or disabled in libcurl');

        // Function curl_strerror only available since PHP 5.5.0,
        // $this->getError() returns same as $this->getErrorDescription() for older versions
        //$this->getError()->shouldReturn('Unsupported protocol');
    }

    function it_url_encodes_the_given_string()
    {
        $this->initialize();
        $this->urlEncode('Hofbräuhaus / München')->shouldReturn('Hofbr%C3%A4uhaus%20%2F%20M%C3%BCnchen');
    }

    function it_decodes_the_given_url_encoded_string()
    {
        $this->initialize();
        $this->urlDecode('Hofbr%C3%A4uhaus%20%2F%20M%C3%BCnchen')->shouldReturn('Hofbräuhaus / München');
    }

    function it_remembers_options_across_multiple_requests()
    {
        $options1 = [
            CURLOPT_URL => 'http://www.codezero.be/api/test/',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => 'input=CodeZero'
        ];

        $options2 = [
            CURLOPT_URL => 'http://www.codezero.be/api/test/',
        ];

        $this->sendRequest($options1)->shouldReturn('CodeZero');
        $this->sendRequest($options2)->shouldReturn('CodeZero');
    }

}
