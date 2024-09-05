<?php

namespace Omnipay\IVeri;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /** @var array */
    private $options;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = [
            'card' => [
                'firstName' => 'Neo',
                'lastName' => 'Likotsi',
                'email' => 'neo@example.com',
            ],
            'amount' => 1999.00,
            'currency' => 'ZAR',
            'description' => 'Test Product',
            'transactionId' => 12,
            'merchantId' => '{c0f9f3e2-b75c-4864-b6c6-df1372fbedb0}',
            'returnUrl' => 'https://www.example.com/return',
        ];

    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(array('amount' => '12.00'));

        $this->assertInstanceOf('\Omnipay\IVeri\Message\PurchaseRequest', $request);
        $this->assertSame('12.00', $request->getAmount());
        $this->assertSame(1200, $request->getAmountInteger());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(array('amount' => '12.00'));

        $this->assertInstanceOf('\Omnipay\IVeri\Message\CompletePurchaseRequest', $request);
        $this->assertSame('12.00', $request->getAmount());
        $this->assertSame(1200, $request->getAmountInteger());
    }
}
