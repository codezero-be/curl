<?php namespace spec\CodeZero\Curl;

use CodeZero\Curl\Curl;
use CodeZero\Curl\OptionParser;
use CodeZero\Curl\ResponseFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestSpec extends ObjectBehavior {

    function let(Curl $curl, OptionParser $optionParser, ResponseFactory $responseFactory)
    {
        $this->beConstructedWith($curl, $optionParser, $responseFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\Curl\Request');
    }

    function it_sets_an_option()
    {
        $this->isOptionSet(CURLOPT_USERAGENT)->shouldBe(false);
        $this->setOption(CURLOPT_USERAGENT, 'blablabla');
        $this->isOptionSet(CURLOPT_USERAGENT)->shouldBe(true);
        $this->getOptionValue(CURLOPT_USERAGENT)->shouldReturn('blablabla');
    }

    function it_sets_an_array_of_options()
    {
        $this->isOptionSet(CURLOPT_USERAGENT)->shouldBe(false);
        $this->isOptionSet(CURLOPT_FOLLOWLOCATION)->shouldBe(false);
        $this->setOptions([CURLOPT_USERAGENT => 'blablabla', CURLOPT_FOLLOWLOCATION => true]);
        $this->isOptionSet(CURLOPT_USERAGENT)->shouldBe(true);
        $this->getOptionValue(CURLOPT_USERAGENT)->shouldReturn('blablabla');
        $this->isOptionSet(CURLOPT_FOLLOWLOCATION)->shouldBe(true);
        $this->getOptionValue(CURLOPT_FOLLOWLOCATION)->shouldBe(true);
    }

    function it_unsets_an_option()
    {
        $this->setOption(CURLOPT_USERAGENT, 'blablabla');
        $this->unsetOption(CURLOPT_USERAGENT);
        $this->isOptionSet(CURLOPT_USERAGENT)->shouldBe(false);
    }

    function it_unsets_an_array_of_options()
    {
        $this->setOptions([CURLOPT_USERAGENT => 'blablabla', CURLOPT_FOLLOWLOCATION => true]);
        $this->unsetOptions([CURLOPT_USERAGENT, CURLOPT_FOLLOWLOCATION]);
        $this->isOptionSet(CURLOPT_USERAGENT)->shouldBe(false);
        $this->isOptionSet(CURLOPT_FOLLOWLOCATION)->shouldBe(false);
    }

    function it_checks_if_an_option_is_set()
    {
        $this->unsetOption(CURLOPT_USERAGENT);
        $this->isOptionSet(CURLOPT_USERAGENT)->shouldBe(false);
        $this->setOption(CURLOPT_USERAGENT, 'blablabla');
        $this->isOptionSet(CURLOPT_USERAGENT)->shouldBe(true);
    }

    function it_fetches_the_value_of_an_option()
    {
        $this->setOption(CURLOPT_USERAGENT, 'blablabla');
        $this->getOptionValue(CURLOPT_USERAGENT)->shouldReturn('blablabla');
    }

    function it_sets_basic_authentication()
    {
        $this->setBasicAuthentication('johndoe', 'secret');
        $this->getOptionValue(CURLOPT_HTTPAUTH)->shouldReturn(CURLAUTH_BASIC);
        $this->getOptionValue(CURLOPT_USERPWD)->shouldReturn('johndoe:secret');
    }

    function it_unsets_basic_authentication()
    {
        $this->unsetBasicAuthentication();
        $this->isOptionSet(CURLOPT_HTTPAUTH)->shouldBe(false);
        $this->isOptionSet(CURLOPT_USERPWD)->shouldBe(false);
    }

    function it_sets_default_options_initially()
    {
        $this->isOptionSet(CURLOPT_SSL_VERIFYPEER)->shouldBe(true);
        $this->isOptionSet(CURLOPT_HEADER)->shouldBe(true);
        $this->isOptionSet(CURLOPT_RETURNTRANSFER)->shouldBe(true);
        $this->isOptionSet(CURLOPT_FAILONERROR)->shouldBe(true);
    }

    function it_sets_the_required_options_for_a_get_request(OptionParser $optionParser, Curl $curl, ResponseFactory $responseFactory)
    {
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];

        $optionParser->parseUrl($url, $data)->shouldBeCalled()->willReturn('parsed url');
        $optionParser->parseData($data)->shouldNotBeCalled();
        $optionParser->parseHeaders($headers)->shouldBeCalled()->willReturn(['parsed headers']);

        $curl->sendRequest(Argument::any())->shouldBeCalled()->willReturn('response');
        $curl->getRequestInfo()->shouldBeCalled()->willReturn([]);
        $curl->getErrorCode()->shouldBeCalled()->willReturn(0);
        $curl->getErrorDescription()->shouldBeCalled();
        $curl->close()->shouldBeCalled();
        $responseFactory->make('response', [])->shouldBeCalled();

        $this->get($url, $data, $headers);

        $this->getOptionValue(CURLOPT_URL)->shouldReturn('parsed url');
        $this->getOptionValue(CURLOPT_HTTPHEADER)->shouldReturn(['parsed headers']);
        $this->getOptionValue(CURLOPT_CUSTOMREQUEST)->shouldReturn('GET');
        $this->isOptionSet(CURLOPT_HTTPGET)->shouldBe(true);
        $this->isOptionSet(CURLOPT_POST)->shouldBe(false);
        $this->isOptionSet(CURLOPT_POSTFIELDS)->shouldBe(false);
    }

    function it_sets_the_required_options_for_a_post_request(OptionParser $optionParser, Curl $curl, ResponseFactory $responseFactory)
    {
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];

        $optionParser->parseUrl($url, $data)->shouldNotBeCalled();
        $optionParser->parseData($data)->shouldBeCalled()->willReturn('parsed data');
        $optionParser->parseHeaders($headers)->shouldBeCalled()->willReturn(['parsed headers']);

        $curl->sendRequest(Argument::any())->shouldBeCalled()->willReturn('response');
        $curl->getRequestInfo()->shouldBeCalled()->willReturn([]);
        $curl->getErrorCode()->shouldBeCalled()->willReturn(0);
        $curl->getErrorDescription()->shouldBeCalled();
        $curl->close()->shouldBeCalled();
        $responseFactory->make('response', [])->shouldBeCalled();

        $this->post($url, $data, $headers);

        $this->getOptionValue(CURLOPT_URL)->shouldReturn($url);
        $this->getOptionValue(CURLOPT_HTTPHEADER)->shouldReturn(['parsed headers']);
        $this->getOptionValue(CURLOPT_POSTFIELDS)->shouldReturn('parsed data');
        $this->getOptionValue(CURLOPT_CUSTOMREQUEST)->shouldReturn('POST');
        $this->isOptionSet(CURLOPT_POST)->shouldBe(true);
        $this->isOptionSet(CURLOPT_HTTPGET)->shouldBe(false);
    }

    function it_sets_the_required_options_for_a_put_request(OptionParser $optionParser, Curl $curl, ResponseFactory $responseFactory)
    {
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];

        $optionParser->parseUrl($url, $data)->shouldNotBeCalled();
        $optionParser->parseData($data)->shouldBeCalled()->willReturn('parsed data');
        $optionParser->parseHeaders($headers)->shouldBeCalled()->willReturn(['parsed headers']);

        $curl->sendRequest(Argument::any())->shouldBeCalled()->willReturn('response');
        $curl->getRequestInfo()->shouldBeCalled()->willReturn([]);
        $curl->getErrorCode()->shouldBeCalled()->willReturn(0);
        $curl->getErrorDescription()->shouldBeCalled();
        $curl->close()->shouldBeCalled();
        $responseFactory->make('response', [])->shouldBeCalled();

        $this->put($url, $data, $headers);

        $this->getOptionValue(CURLOPT_URL)->shouldReturn($url);
        $this->getOptionValue(CURLOPT_HTTPHEADER)->shouldReturn(['parsed headers']);
        $this->getOptionValue(CURLOPT_POSTFIELDS)->shouldReturn('parsed data');
        $this->getOptionValue(CURLOPT_CUSTOMREQUEST)->shouldReturn('PUT');
        $this->isOptionSet(CURLOPT_HTTPGET)->shouldBe(false);
        $this->isOptionSet(CURLOPT_POST)->shouldBe(false);
    }

    function it_sets_the_required_options_for_a_patch_request(OptionParser $optionParser, Curl $curl, ResponseFactory $responseFactory)
    {
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];

        $optionParser->parseUrl($url, $data)->shouldNotBeCalled();
        $optionParser->parseData($data)->shouldBeCalled()->willReturn('parsed data');
        $optionParser->parseHeaders($headers)->shouldBeCalled()->willReturn(['parsed headers']);

        $curl->sendRequest(Argument::any())->shouldBeCalled()->willReturn('response');
        $curl->getRequestInfo()->shouldBeCalled()->willReturn([]);
        $curl->getErrorCode()->shouldBeCalled()->willReturn(0);
        $curl->getErrorDescription()->shouldBeCalled();
        $curl->close()->shouldBeCalled();
        $responseFactory->make('response', [])->shouldBeCalled();

        $this->patch($url, $data, $headers);

        $this->getOptionValue(CURLOPT_URL)->shouldReturn($url);
        $this->getOptionValue(CURLOPT_HTTPHEADER)->shouldReturn(['parsed headers']);
        $this->getOptionValue(CURLOPT_POSTFIELDS)->shouldReturn('parsed data');
        $this->getOptionValue(CURLOPT_CUSTOMREQUEST)->shouldReturn('PATCH');
        $this->isOptionSet(CURLOPT_HTTPGET)->shouldBe(false);
        $this->isOptionSet(CURLOPT_POST)->shouldBe(false);
    }

    function it_sets_the_required_options_for_a_delete_request(OptionParser $optionParser, Curl $curl, ResponseFactory $responseFactory)
    {
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];

        $optionParser->parseUrl($url, $data)->shouldNotBeCalled();
        $optionParser->parseData($data)->shouldBeCalled()->willReturn('parsed data');
        $optionParser->parseHeaders($headers)->shouldBeCalled()->willReturn(['parsed headers']);

        $curl->sendRequest(Argument::any())->shouldBeCalled()->willReturn('response');
        $curl->getRequestInfo()->shouldBeCalled()->willReturn([]);
        $curl->getErrorCode()->shouldBeCalled()->willReturn(0);
        $curl->getErrorDescription()->shouldBeCalled();
        $curl->close()->shouldBeCalled();
        $responseFactory->make('response', [])->shouldBeCalled();

        $this->delete($url, $data, $headers);

        $this->getOptionValue(CURLOPT_URL)->shouldReturn($url);
        $this->getOptionValue(CURLOPT_HTTPHEADER)->shouldReturn(['parsed headers']);
        $this->getOptionValue(CURLOPT_POSTFIELDS)->shouldReturn('parsed data');
        $this->getOptionValue(CURLOPT_CUSTOMREQUEST)->shouldReturn('DELETE');
        $this->isOptionSet(CURLOPT_HTTPGET)->shouldBe(false);
        $this->isOptionSet(CURLOPT_POST)->shouldBe(false);
    }

    function it_throws_if_the_request_returns_an_error_code(OptionParser $optionParser, Curl $curl, ResponseFactory $responseFactory)
    {
        $url = 'http://my.site/api';

        $optionParser->parseUrl(Argument::any(), Argument::type('array'))->shouldNotBeCalled();
        $optionParser->parseData(Argument::type('array'))->shouldNotBeCalled();
        $optionParser->parseHeaders(Argument::type('array'))->shouldNotBeCalled();

        $curl->sendRequest(Argument::any())->shouldBeCalled()->willReturn('response');
        $curl->getRequestInfo()->shouldBeCalled()->willReturn([]);
        $curl->getErrorCode()->shouldBeCalled()->willReturn(1);
        $curl->getErrorDescription()->shouldBeCalled()->willReturn('error message');
        $curl->close()->shouldBeCalled();
        $responseFactory->make('response', [])->shouldNotBeCalled();

        $this->shouldThrow('CodeZero\Curl\RequestException')->duringPost($url);
    }

    function it_returns_a_parsed_response_from_the_factory(OptionParser $optionParser, Curl $curl, ResponseFactory $responseFactory)
    {
        $url = 'http://my.site/api';

        $optionParser->parseUrl(Argument::any(), Argument::type('array'))->shouldNotBeCalled();
        $optionParser->parseData(Argument::type('array'))->shouldNotBeCalled();
        $optionParser->parseHeaders(Argument::type('array'))->shouldNotBeCalled();

        $curl->sendRequest(Argument::any())->shouldBeCalled()->willReturn('response');
        $curl->getRequestInfo()->shouldBeCalled()->willReturn([]);
        $curl->getErrorCode()->shouldBeCalled()->willReturn(0);
        $curl->getErrorDescription()->shouldBeCalled();
        $curl->close()->shouldBeCalled();

        $responseFactory->make('response', [])->shouldBeCalled()->willReturn('parsed response');

        $this->delete($url)->shouldReturn('parsed response');
    }

    function it_resets_request_specific_options_when_sending_consecutive_requests(OptionParser $optionParser, Curl $curl, ResponseFactory $responseFactory)
    {
        $url1 = 'http://my.site/api';
        $url2 = 'http://my.other/site';
        $url3 = 'http://my.third/site';
        $data = ['do' => 'something'];
        $headers = ['Some Header' => 'Some Value'];

        $optionParser->parseUrl(Argument::any(), Argument::type('array'))->willReturn('parsed url');
        $optionParser->parseData(Argument::type('array'))->willReturn('parsed data');
        $optionParser->parseHeaders(Argument::type('array'))->willReturn(['parsed headers']);

        $curl->sendRequest(Argument::any())->willReturn('response');
        $curl->getRequestInfo()->willReturn([]);
        $curl->getErrorCode()->willReturn(0);
        $curl->getErrorDescription()->willReturn();
        $curl->close()->willReturn();

        $responseFactory->make('response', [])->willReturn();

        $this->post($url1, $data, $headers);

        $this->getOptionValue(CURLOPT_URL)->shouldReturn($url1);
        $this->getOptionValue(CURLOPT_HTTPHEADER)->shouldReturn(['parsed headers']);
        $this->getOptionValue(CURLOPT_POSTFIELDS)->shouldReturn('parsed data');
        $this->getOptionValue(CURLOPT_CUSTOMREQUEST)->shouldReturn('POST');
        $this->isOptionSet(CURLOPT_POST)->shouldBe(true);
        $this->isOptionSet(CURLOPT_HTTPGET)->shouldBe(false);

        $this->post($url2, $data);

        $this->getOptionValue(CURLOPT_URL)->shouldReturn($url2);
        $this->getOptionValue(CURLOPT_CUSTOMREQUEST)->shouldReturn('POST');
        $this->getOptionValue(CURLOPT_POSTFIELDS)->shouldReturn('parsed data');
        $this->isOptionSet(CURLOPT_HTTPHEADER)->shouldBe(false);
        $this->isOptionSet(CURLOPT_POST)->shouldBe(true);
        $this->isOptionSet(CURLOPT_HTTPGET)->shouldBe(false);

        $this->get($url3);

        $this->getOptionValue(CURLOPT_URL)->shouldReturn('parsed url');
        $this->getOptionValue(CURLOPT_CUSTOMREQUEST)->shouldReturn('GET');
        $this->isOptionSet(CURLOPT_HTTPHEADER)->shouldBe(false);
        $this->isOptionSet(CURLOPT_POSTFIELDS)->shouldBe(false);
        $this->isOptionSet(CURLOPT_POST)->shouldBe(false);
        $this->isOptionSet(CURLOPT_HTTPGET)->shouldBe(true);
    }

}
