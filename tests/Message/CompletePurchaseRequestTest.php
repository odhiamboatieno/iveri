<?php

namespace Omnipay\IVeri\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseRequestTest extends TestCase
{
    /**
     * @var CompletePurchaseRequest
     */
    private $request;

    public function setUp()
    {
        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function getPostData()
    {
        return array(
            'ECOM_CONSUMERORDERID' => '',
            'LITE_TRANSACTIONINDEX' => '61493',
            'LITE_PAYMENT_CARD_STATUS' => 0,
            'LITE_RESULT_DESCRIPTION' => 'fjdksl',
            'LITE_CONSUMERORDERID_PREFIX' => '',
            'LITE_ORDER_AMOUNT' => '12.00',
            'ECOM_PAYMENT_CARD_PROTOCOLS' => 'iVeri',

            'ECOM_BILLTO_POSTAL_NAME_FIRST' => 'Test',
            'ECOM_BILLTO_POSTAL_NAME_LAST' => 'User 01',
            'ECOM_BILLTO_ONLINE_EMAIL' => 'sbtu01@payfast.co.za',
            'LITE_MERCHANT_APPLICAIONID' => '10000103',
        );
    }

    public function testCompletePurchaseSuccess()
    {
        $this->getHttpRequest()->request->replace($this->getPostData());
        $this->setMockHttpResponse('CompletePurchaseSuccess.txt');

        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\IVeri\Message\CompletePurchaseResponse', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('61493', $response->getTransactionReference());
        $this->assertSame(0, $response->getCode());
    }

    public function testCompletePurchaseInvalid()
    {
        $this->getHttpRequest()->request->replace($this->getPostData());
        $this->setMockHttpResponse('CompletePurchaseFailure.txt');

        $response = $this->request->send();

        // $this->assertFalse($response->isSuccessful());
        // $this->assertFalse($response->isRedirect());
        // $this->assertNull($response->getTransactionReference());
        // $this->assertSame('INVALID', $response->getMessage());
        // $this->assertNull($response->getCode());
    }
}
