<?php

namespace Omnipay\IVeri\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * IVeri Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    const SUCCESS_CODE = 0;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);
        $this->statusCode = $data['LITE_PAYMENT_CARD_STATUS'];
    }

    public function isSuccessful()
    {
        return $this->statusCode == self::SUCCESS_CODE;
    }

    public function getTransactionId() {
        if ($this->isSuccessful() && isset($this->data['ECOM_CONSUMERORDERID'])) {
            return $this->data['ECOM_CONSUMERORDERID'];
        }
    }

    public function getTransactionReference()
    {
        if ($this->isSuccessful() && isset($this->data['LITE_TRANSACTIONINDEX'])) {
            return $this->data['LITE_TRANSACTIONINDEX'];
        }
    }

    public function getMessage()
    {
        $paymentResultDescription = $this->data['LITE_RESULT_DESCRIPTION'];

        switch ($this->statusCode) {
            case 0:
                return 'Iveri Lite complete payment.';
            case 1:
            case 2:
            case 5:
            case 9:
                return 'Payment error: Please try again later ' . $paymentResultDescription;
            case 255:
                return 'Payment error: Please try again later ' . $paymentResultDescription;
            default:
                return 'Payment failed: '. $paymentResultDescription;
        }
    }

    public function getCode()
    {
        return $this->statusCode;
    }
}
