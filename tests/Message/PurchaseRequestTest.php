<?php

namespace Omnipay\IVeri\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testTransactionToken()
    {
        $this->request->initialize(
            array(
                'card' => [
                    'firstName' => 'Neo',
                    'lastName' => 'Likotsi',
                    'email' => 'neo@example.com',
                ],
                'amount' => '12.00',
                'description' => 'Test Product',
                'transactionId' => 123,
                'merchantId' => '{c0f9f3e2-b75c-4864-b6c6-df1372fbedb0}',
                'returnUrl' => 'https://www.example.com/return',
            )
        );

        $data = $this->request->getData();
        $this->assertNotNull($data['Lite_Transaction_Token']);
    }

    public function testBiilingUserDetails()
    {
        $this->request->initialize(
            array(
                'card' => [
                    'firstName' => 'Neo',
                    'lastName' => 'Likotsi',
                    'email' => 'neo@example.com',
                ],
                'amount' => '12.00',
                'description' => 'Test Product',
                'transactionId' => 123,
                'merchantId' => '{c0f9f3e2-b75c-4864-b6c6-df1372fbedb0}',
                'returnUrl' => 'https://www.example.com/return',
            )
        );

        $data = $this->request->getData();

        $this->assertArrayHasKey('Ecom_BillTo_Name_Postal_First', $data);
        $this->assertArrayHasKey('Ecom_BillTo_Name_Postal_Last', $data);
        $this->assertArrayHasKey('Ecom_BillTo_Online_Email', $data);
    }

    public function testPurchase()
    {
        $this->request->setAmount('12.00')->setDescription('Test Product');

        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\IVeri\Message\PurchaseResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCode());

        $this->assertSame('https://portal.nedsecure.co.za/Lite/Authorise.aspx', $response->getRedirectUrl());
        $this->assertSame('POST', $response->getRedirectMethod());
        $this->assertArrayHasKey('Lite_Transaction_Token', $response->getData());
    }
}
