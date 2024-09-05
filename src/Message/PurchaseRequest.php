<?php

namespace Omnipay\IVeri\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * IVeri Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected $endpoint = 'https://portal.nedsecure.co.za/Lite/Authorise.aspx';

    public function getMerchantId()
    {
        return $this->getParameter('merchant_id');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchant_id', $value);
    }

    public function getTransactionPrefix()
    {
        return $this->getParameter('transactionPrefix');
    }

    public function setTransactionPrefix($value)
    {
        return $this->setParameter('transactionPrefix', $value);
    }

    public function getPassphrase()
    {
        return $this->getParameter('passphrase');
    }

    public function setPassphrase($value)
    {
        return $this->setParameter('passphrase', $value);
    }

    public function getData()
    {
        $this->validate('amount', 'description');

        $data = [];
        $data['Lite_Merchant_ApplicationID'] = $this->getMerchantId();
        $data['Lite_Website_Successful_Url'] = $this->getReturnUrl();
        $data['Lite_Website_Fail_Url'] = $this->getReturnUrl();
        $data['Lite_Website_TryLater_Url'] = $this->getReturnUrl();
        $data['Lite_Website_Error_Url'] = $this->getReturnUrl();

        if ($this->getCard()) {
            $data['Ecom_BillTo_Name_Postal_First'] = $this->getCard()->getFirstName();
            $data['Ecom_BillTo_Name_Postal_Last'] = $this->getCard()->getLastName();
            $data['Ecom_BillTo_Online_Email'] = $this->getCard()->getEmail();
        }

        $data['Ecom_ConsumerOrderID'] = $this->getTransactionId();
        $data['Lite_ConsumerOrderID_PreFix'] = $this->getTransactionPrefix() || 'AUTOGENERATE';
        $data['Lite_Order_Amount'] = $this->getAmountInteger();
        $data['Lite_Order_LineItems_Product_1'] = $this->getDescription();
        $data['Lite_Order_LineItems_Amount_1'] = $this->getAmountInteger();
        $data['Ecom_TransactionComplete'] = 'FALSE';
        $data['Ecom_Payment_Card_Protocols'] = 'iVeri';
        $data['Lite_Version'] = 'neolikotsi-omnipay-iveri_line_2.0';

        $data['passphrase'] = $this->getParameter('passphrase');
        $data['Lite_Transaction_Token'] = $this->generateSignature($data);
        unset($data['passphrase']);

        return $data;
    }

    protected function generateSignature($data)
    {
        $filter = [ 'passphrase',
            'timestamp',
            'Lite_Merchant_ApplicationID',
            'Lite_Order_Amount',
            'Ecom_BillTo_Online_Email',
            ];

        $fields = array_filter($data, function($key) use ($filter) {
            return in_array($key, $filter);
        }, ARRAY_FILTER_USE_KEY);

        if (!array_key_exists('timestamp', $fields)) {
            $timestamp = time();
            $fields = $this->insertAt(['timestamp' => $timestamp], $fields, 1);
        }

        return hash('sha256', implode('', $fields));
    }

    /**
     * insert key - value array at index in array
     *
     * @param array $needle
     * @param array $array
     * @param int $index
     * @return array
     */
    protected function insertAt($needle, $array, $index)
    {
        $arrayEnd = array_splice($array, $index);
        $arrayStart = array_splice($array, 0, $index);

        return array_merge($arrayStart, $needle, $arrayEnd);
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data, $this->endpoint);
    }
}
