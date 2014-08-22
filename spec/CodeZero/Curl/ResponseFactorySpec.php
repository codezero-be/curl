<?php namespace spec\CodeZero\Curl;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseFactorySpec extends ObjectBehavior {

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\Curl\ResponseFactory');
    }

    function it_returns_a_response()
    {
        $this->make('response text', ['request', 'info'])
            ->shouldReturnAnInstanceOf('\CodeZero\Curl\Response');
    }

}
