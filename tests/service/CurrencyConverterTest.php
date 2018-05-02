<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    /** @var Client */
    private $api;

    /** @var MockHandler */
    private $guzzleMock;

    /** @var CurrencyConverter */
    private $converter;

    protected function setUp()
    {
        parent::setUp();
        $this->guzzleMock = new MockHandler();
        $this->api = new Client(['handler' => HandlerStack::create($this->guzzleMock)]);

        $this->converter = $this->getMockBuilder(CurrencyConverter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getApi'])
            ->getMock();
        $this->converter->expects($this->once())
            ->method('getApi')
            ->willReturn($this->api);
    }

    public function testConvert()
    {
        $this->guzzleMock->append(new Response(200, [], '{"success":true,"rates":{"GBP":1.2,"USD":1.5}}'));
        $this->assertSame(15.0, $this->converter->convert(12, 'GBP', 'USD'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid currency XXX
     */
    public function testConvertWithInvalidCurrencyThrowsException()
    {
        $this->guzzleMock->append(new Response(200, [], '{"success":true,"rates":{"USD":1.5}}'));
        $this->converter->convert(12, 'XXX', 'USD');
    }
}
