<?php namespace spec\CodeZero\Curl;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OptionParserSpec extends ObjectBehavior {

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\Curl\OptionParser');
    }

    function it_accepts_a_url_and_data_array_and_returns_a_url_with_query_string()
    {
        $url = 'http://my.site/';
        $data = ['name' => 'value', 'extra' => 'info with spaces'];
        $parsed = 'http://my.site/?name=value&extra=info+with+spaces';

        $this->parseUrl($url, $data)->shouldReturn($parsed);
    }

    function it_turns_a_data_array_into_a_query_string()
    {
        $data = ['name' => 'value', 'extra' => 'info with spaces'];
        $parsed = 'name=value&extra=info+with+spaces';

        $this->parseData($data)->shouldReturn($parsed);
    }

    function it_turns_an_associative_array_into_an_array_with_valid_headers()
    {
        $headers = [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Authorization' => 'Bearer abcdef'
        ];
        $parsed = [
            'Content-Type: text/plain; charset=UTF-8',
            'Authorization: Bearer abcdef'
        ];

        $this->parseHeaders($headers)->shouldReturn($parsed);
    }

}
