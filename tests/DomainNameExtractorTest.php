<?php

namespace DigiTickets\StripeTests;

use DigiTickets\Stripe\Lib\DomainNameExtractor;
use Omnipay\Tests\TestCase;

class DomainNameExtractorTest extends TestCase
{

    public function domainProvider()
    {
        return [
            'subdomain' => ['https://sub.test.com', 'test.com'],
            'subdomain with path in url' => ['https://sub.test.com/test?test=test', 'test.com/test'],
            'subdomain with path in url2' => ['https://sub.test.com/', 'test.com/'],
            'subdomain with more dots' => ['https://sub.test.co.uk', 'test.co.uk'],
            'short domain' => ['https://sub.aa.co.uk', 'aa.co.uk'],
            'short domain2' => ['https://sub.aaa.com', 'aaa.com'],
            'short domain3' => ['https://sub.aaaa.uk', 'aaaa.uk'],
            '2 subdomains' => ['https://sub1.sub2.test.co.uk', 'sub2.test.co.uk'],
            'no subdomain' => ['https://testtest.co.uk', 'testtest.co.uk'],
            'weird domain' => ['https://localhost', 'localhost'],
            'ip' => ['https://127.123.123.123', '127.123.123.123'],
            'nothing' => ['', ''],
            'short domain we cant handle' => ['https://sub.aa.com', 'sub.aa.com'],
            'short domain we cant handle2' => ['https://sub.aaa.uk', 'sub.aaa.uk'],
        ];
    }

    /**
     * @param string $url
     * @param string $expectedBaseDomain
     *
     * @dataProvider domainProvider
     */
    public function testExtractBaseDomain(string $url, string $expectedBaseDomain)
    {
        $baseDomain = DomainNameExtractor::extractBaseDomainAndPath($url);
        $this->assertEquals($expectedBaseDomain, $baseDomain);
    }
}
