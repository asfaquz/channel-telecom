<?php

/**
 * Souq APIs and other shipping services APIs functions.
 * @author Rijesh Kandathil <rkandathil@souq.com>/Syed Afaquz Zaman <szaman@souq.com>
 * @copyright Copyright &copy; 2013 Souq.com Group
 */
class Api {

    public static function AddUnit($params) {
        $sParams = '[id_seller=' . Yii::app()->params["IdSeller"] . ']';
        $sParams .= '[id_item=' . $params["id_item"] . ']';
        //$sParams .= '[id_offer=' . $params["sku"] . ']';
        $sParams .= '[delivery_time=c]';
        $sParams .= '[unit_condition=new]';
        $sParams .= '[listing_price=' . $params["price"] . ']';
        $sParams .= '[amount=' . $params["qty"] . ']';
        $sParams .= '[location=' . Yii::app()->params["CountryIsoCode"] . ']';
        $sParams .= '[currency=' . Yii::app()->params["currency"] . ']';
        $randNote = CArray::RandNumberTime();
        $sParams .= '[note=' . $randNote . ']';
        $aApiCall = ApiOutput::getOutput('AddUnit', $sParams);
        //DevR::PrintR($aApiResult);
        if ($aApiCall === FALSE || empty($aApiCall)) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        }
        return $aApiCall['souq']['result'];
    }

    /**
     * Api call to add item to cart
     * @param $unit_id
     * @param $count
     * @return bool
     */
    public static function AddUnitToCart($unit_id, $count) {
        //$sParams = '[id_unit=' . $unit_id . '][count=' . $count . '][id_customer=' . Yii::app()->session['id_customer'] . ']';
        $sParams = '[id_unit=' . $unit_id . '][count=' . $count . ']';
        $aApiCall = ApiOutput::getOutput('AddUnitToCartV1', $sParams);
        if ($aApiCall === FALSE || empty($aApiCall)) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            $aApiCallResult['error_details'] = $aApiCall['souq']['request']['errors']['error_details'];
            return $aApiCallResult; // return error
        }
        return TRUE;
    }

    /**
     * Api call to delete item from cart
     * @param $idUnit
     * @param $limit
     * @return bool
     */
    public static function DeleteUnitFromCart($idUnit, $limit) {
        $sParams = '[id_unit=' . $idUnit . '][limit=' . $limit . '][id_session=' . Yii::app()->session['user_info']['sessionId'] . ']';
        $aApiCall = ApiOutput::getOutput('DeleteUnitFromCart', $sParams);
        //DevR::PrintR($aApiCall);
        if ($aApiCall === FALSE || empty($aApiCall)) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Api call to to delete cart
     * @return bool
     */
    public static function DeleteCart() {
        $aApiCall = ApiOutput::getOutput('DeleteCart', '');
        //DevR::PrintR($aApiCall);
        if ($aApiCall === FALSE || empty($aApiCall)) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Api call to regsiter a customer
     * @param $params
     * @return array|bool
     */
    public static function RegisterCustomer($params) {
        if (isset($params["firstname"])) {
            $sApiRegisterParms = '[firstname=' . $params["firstname"] . ']';
        } else {
            $sApiRegisterParms = '[firstname=' . $params["first_name"] . ']';
        }
        if (isset($params["lastname"])) {
            $sApiRegisterParms.= '[lastname=' . $params["lastname"] . ']';
        } else {
            $sApiRegisterParms.= '[lastname=' . $params["last_name"] . ']';
        }
        $sApiRegisterParms.= '[email=' . $params["email"] . ']';
        $sApiRegisterParms.= '[email_confirmation=' . $params["email"] . ']';
        $sApiRegisterParms.= '[gender=' . $params["gender"] . ']';
        $sApiRegisterParms.= '[password=' . $params["password"] . ']';
        $sApiRegisterParms.= '[password_confirmation=' . $params["password"] . ']';
        $sApiRegisterParms.= '[subscribe_newsletters=false]';
        $sApiRegisterParms.= '[accept_terms_of_service=true]';
        $sApiRegisterParms.= '[country_iso_code=' . Yii::app()->params['CountryIsoCode'] . ']';
        $sApiRegisterParms.= '[VerificationLink=' . $params['VerificationLink'] . ']';
        $aApiCall = ApiOutput::getOutput('RegisterCustomer', $sApiRegisterParms);
        if ($aApiCall === FALSE || empty($aApiCall)) {
            return FALSE;
        }
        return $aApiCall;
    }

    /**
     * Api call to to login customer by email
     * @param $params
     * @return array|bool
     */
    public static function LoginCustomerByEmail($params) {
        $sParams = '[email=' . $params["email"] . ']';
        $sParams .= '[VerificationCode=' . $params["code"] . ']';
        $aApiCall = ApiOutput::getOutput('LoginCustomerByEmail', $sParams);
        //DevR::PrintR($aApiCall);DevR::DieR();
        if ($aApiCall === FALSE || empty($aApiCall)) {
            return FALSE;
        }
        return $aApiCall;
    }

    /**
     * Api call to login
     * @param $params
     * @return array|bool
     */
    // Customer Login API
    public static function LoginCustomer($params) {
        $sParams = '[email=' . $params["email"] . '][password=' . $params["password"] . ']';
        $aApiCall = ApiOutput::getOutput($sService = 'LoginCustomer', $sParams, $sResult = 'json', '', 'signed-nonce');
        if ($aApiCall === FALSE || empty($aApiCall)) {
            return FALSE;
        }
        return $aApiCall;
    }

    /**
     * Api call to get all the shipping addresses of a customer
     * @param $id
     * @return array|bool
     */
    public static function GetShippingAddresses($id) {
        $sParams = '[id_customer=' . $id . ']';
        $aApiCall = ApiOutput::getOutput('GetShippingAddresses', $sParams);
        //DevR::PrintR($aApiCall);DevR::DieR();
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return $aApiCall['souq']['request']['errors']['error'];
            //return FALSE;
        }
        return $aApiCall;
    }

    /**
     * Api call to get all the shipping services to an address
     * @param string $idCustomerAddress
     * @return bool
     */
    public static function GetShippingServices($idCustomerAddress = '') {

        if (empty($idCustomerAddress)) {
            $idCustomerAddress = Yii::app()->session['id_customer_address'];
        }
        $sParams = '[id_customer=' . Yii::app()->session['id_customer'] . '][id_customer_address=' . $idCustomerAddress . ']';
        $sParams .= '[language=' . Yii::app()->getLanguage() . ']';
        //$sParams .= '[country=' . Yii::app()->params['CountryIsoCode'] . ']';
        $aApiCall = ApiOutput::getOutput('GetShippingServices', $sParams);
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        }
        $aShippingService['shipping'] = $aApiCall['souq']['result']['units']['unit']['service']['id_shipping_provider']
                . '-' . $aApiCall['souq']['result']['units']['unit']['service']['id_shipping_service'];
        $aShippingService['provider'] = Yii::t('strings', 'Note') . ": " . Yii::t('strings', 'Your shipment will be delivered to you via')
                . " " .
                $aApiCall['souq']['result']['units']['unit']['service']['provider_label']
                . " " .
                Yii::t('strings', 'within') . " " . $aApiCall['souq']['result']['units']['unit']['service']['delivery_time'];
        $aShippingService['shipping_rate'] = $aApiCall['souq']['result']['units']['unit']['service']['shipping_rate'];
        return $aShippingService;
    }

    /**
     * Api call to checkout to an address
     * @param $params
     * @return array|bool
     */
    public static function CheckoutUseAddress($params) {

        $sParams = '[id_customer=' . Yii::app()->session['id_customer'] . ']';
        $sParams .= '[id_customer_address=' . $params['id_customer_address_list'] . ']';
        $sParams .= '[shipping_service=' . $params['shipping_services'] . ']';
        $aApiCall = ApiOutput::getOutput('CheckoutUseAddress', $sParams);
        //DevR::PrintR($aApiCall);DevR::DieR();
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        }
        return $aApiCall;
    }

    /**
     * Api call to get all the payment methods available
     * @param $dIdCustomer
     * @return mixed
     */
//    public static function GetPaymentMethods($dIdCustomer) {
//        $sParams = '[id_customer=' . $dIdCustomer . ']';
//        $sParams .= '[product=checkout_api]';
//        $sParams .= '[country=ae]';
//        $sParams .= '[language='.Yii::app()->getLanguage().']';
//        $aApiCall = ApiOutput::getOutput($sService = 'GetPaymentMethods', $sParams, $sResult = 'json', '', 'signed-nonce');
//        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
//            return;
//        } else {
//            return $aApiCall['souq']['result'];
//        }
//    }


    public static function GetPaymentMethods() {
        $sParams = '[id_customer=' . Yii::app()->session['id_customer'] . ']';
        $sParams .= '[product=checkout_api]';
        $sParams .= '[country=ae]';
        $sParams .= '[language=' . Yii::app()->getLanguage() . ']';
        $aApiCall = ApiOutput::getOutput($sService = 'GetPaymentMethods', $sParams, $sResult = 'json', '', 'signed-nonce');
        if (isset($aApiCall['@nodes']['result'][0]['@nodes']['payment_methods'][0]['@nodes']['paymentmethods'][0]['@nodes'])) {
            $aPaymentsInfo = $aApiCall['@nodes']['result'][0]['@nodes']['payment_methods'][0]['@nodes']['paymentmethods'][0]['@nodes'];
            $aExtraInfos = isset($aApiCall['@nodes']['result'][0]['@nodes']['payment_methods'][0]['@nodes']['paymentmethods_extrainformation']) ? $aApiCall['@nodes']['result'][0]['@nodes']['payment_methods'][0]['@nodes']['paymentmethods_extrainformation'] : array();
            $aTempPayment = array();

            foreach ($aPaymentsInfo as $sPaymentName => $aPayment) {
                $aTempPayment['name'] = $sPaymentName;
                $aTempPayment['label'] = $aPayment[0]['@nodes']['display_text'][0]['@value'];
                if ($sPaymentName == 'creditcard') {
                    $aSavedCreditCardInfo = array();
                    if (isset($aPayment[0]['@nodes']['pre_requested_params'][0]['@nodes']['saved_creditcard_information'])) {
                        $aCreditCardInfo = $aPayment[0]['@nodes']['pre_requested_params'][0]['@nodes']['saved_creditcard_information'][0]['@nodes'];

                        foreach ($aCreditCardInfo as $sCCKey => $aCCValue) {
                            $aSavedCreditCardInfo[$sCCKey] = $aCCValue[0]['@value'];
                        }
                    }
                    $aTempPayment['saved_credit_card'] = $aSavedCreditCardInfo;
                } else if ($sPaymentName == 'cash_on_delivery' && isset($aExtraInfos[0]['@nodes']['cash_on_delivery'])) {

                    $aTempPayment['fees'] = $aExtraInfos[0]['@nodes']['cash_on_delivery'][0]['@nodes']['cod_fees'][0]['@value'];
                } else if ($sPaymentName == 'hitmeister' && isset($aExtraInfos[0]['@nodes']['hitmeister'])) {
                    $aTempPayment['balance'] = $aExtraInfos[0]['@nodes']['hitmeister'][0]['@nodes']['wallet_balance'][0]['@value'];
                }

                $aPayments['payments'][$sPaymentName] = $aTempPayment;
                $aTempPayment = NULL;
            }
            $aPayments['wallet_info'] = array();
            if (isset($aExtraInfos[0]['@nodes']['hitmeister'][0]['@nodes']['wallet_balance'])) {
                $aPayments['wallet_info']['wallet_balance'] = $aExtraInfos[0]['@nodes']['hitmeister'][0]['@nodes']['wallet_balance'][0]['@value'];
            }

            if (isset($aExtraInfos[0]['@nodes']['hitmeister'][0]['@nodes']['not_enough_balance'])) {
                $aPayments['wallet_info']['not_enough_balance'] = $aExtraInfos[0]['@nodes']['hitmeister'][0]['@nodes']['not_enough_balance'][0]['@value'];
            }

            if (isset($aExtraInfos[0]['@nodes']['hitmeister'][0]['@nodes']['due_amount'])) {
                $aPayments['wallet_info']['due_amount'] = $aExtraInfos[0]['@nodes']['hitmeister'][0]['@nodes']['due_amount'][0]['@value'];
            }

            unset($aExtraInfos);
            unset($aPaymentsInfo);
            return $aPayments;
        } else {
            return FALSE;
        }
    }

    /**
     * Prepare the payment
     * @param $aParams
     * @return bool
     */
    public static function PreparePaymentMethod($aParams) {
        $urlSchema = 'http';
        if (Yii::app()->params['siteMode'] == 'production') {
            $urlSchema = 'https';
        }

        $sParams = '[id_customer=' . Yii::app()->session['id_customer'] . ']';
        if ($aParams['payment_method'] == "creditcard") {
            $sParams .= '[payment_method=' . $aParams['payment_method'] . ']';
            $aSerializedParams['save_creditcard'] = $aParams['save_creditcard']; //1--SAVE 0-DO NOT SAVE
            $aSerializedParams['use_saved_creditcard'] = $aParams['use_saved_creditcard'];
            $aSerializedParams['accept_url'] = Yii::app()->createAbsoluteUrl('/product/payment_accept', array(), $urlSchema);
            $aSerializedParams['exception_url'] = Yii::app()->createAbsoluteUrl('/product/payment', array('error' => 1), $urlSchema);
            $sSerializedParams = serialize($aSerializedParams);
            $sParams .= '[serialized_params=' . urlencode($sSerializedParams) . ']';
        }


        if ($aParams['payment_method'] == "save_card") {
            $sParams .= '[payment_method=creditcard]';
            $aSerializedParams['save_creditcard'] = 1; //1
            $aSerializedParams['use_saved_creditcard'] = 0;
            $aSerializedParams['accept_url'] = Yii::app()->createAbsoluteUrl('/product/payment_accept', array(), $urlSchema);
            $aSerializedParams['exception_url'] = Yii::app()->createAbsoluteUrl('/product/payment', array('error' => 1), $urlSchema);
            $sSerializedParams = serialize($aSerializedParams);
            $sParams .= '[serialized_params=' . urlencode($sSerializedParams) . ']';
        }


        if ($aParams['payment_method'] == "saved_credit_card") {
            $sParams .= '[payment_method=creditcard]';
            $aSerializedParams['save_creditcard'] = 0; //1
            $aSerializedParams['use_saved_creditcard'] = 1;
            $aSerializedParams['accept_url'] = Yii::app()->createAbsoluteUrl('/product/payment_accept', array(), $urlSchema);
            $aSerializedParams['exception_url'] = Yii::app()->createAbsoluteUrl('/product/payment', array('error' => 1), $urlSchema);
            $sSerializedParams = serialize($aSerializedParams);
            $sParams .= '[serialized_params=' . urlencode($sSerializedParams) . ']';
        }
        
        
        if ($aParams['payment_method'] == "cash_on_delivery") {
            $sParams .= '[payment_method=' . $aParams['payment_method'] . ']';
        }
        
        if ($aParams['payment_method'] == "hitmeister") {
            $sParams .= '[payment_method=' . $aParams['payment_method'] . ']';
        }

        $sParams .= '[language=' . Yii::app()->getLanguage() . ']';
        $sParams .= '[product=checkout_api]';
        $aApiCall = ApiOutput::getOutput('PreparePaymentMethod', $sParams);

        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        }
        return $aApiCall['souq']['result'];
    }

    /**
     * Email verification of a regsitered user
     * @param $email
     * @return bool
     */
    public static function EmailVerification($email) {
        $sParams = '[VerificationCode=' . trim($email) . ']';
        $aApiCall = ApiOutput::getOutput('EmailVerification', $sParams);
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['result']['status'])) {
            return false;
        }
        return $aApiCall['souq']['result']['status'];
    }

    /**
     * Edit the customer address
     * @param $aParams
     * @param $aID
     * @return array|bool
     */
    public static function EditCustomerAddress($aParams, $aID) {
        $aCRLFFilter = array("\r\n", "\n", "\r");
        $sApiRegisterParms = '[id_customer_address=' . $aID . ']';
        $sApiRegisterParms .= '[firstname=' . $aParams['firstname'] . ']';
        $sApiRegisterParms .= '[lastname=' . $aParams['lastname'] . ']';
        $sApiRegisterParms .= '[city=' . $aParams['city'] . ']';
        $sApiRegisterParms .= '[language=' . Yii::app()->getLanguage() . ']';
        $sApiRegisterParms .= '[country=' . Yii::app()->params['CountryIsoCode'] . ']';
        $sApiRegisterParms .= '[street=' . $aParams['street'] . ']';
        $sApiRegisterParms .= '[region=' . $aParams['AddressArea'] . ']';
        $sApiRegisterParms .= '[phone=' . Yii::app()->params['country_landline_code'] . $aParams['OperatorPrefix'] . $aParams['phone'] . ']';
        $sApiRegisterParms .= '[note=' . $aParams['note'] . ']';
        $sApiRegisterParms .= '[building_no=' . $aParams['building_no'] . ']';
        $sApiRegisterParms .= '[floor_no=' . $aParams['floor_no'] . ']';
        $sApiRegisterParms .= '[apartment_no=' . $aParams['apartment_no'] . ']';
        $sApiRegisterParms = str_replace($aCRLFFilter, ' ', $sApiRegisterParms);
        //echo $sApiRegisterParms;
        $aApiCall = ApiOutput::getOutput('EditCustomerAddress', $sApiRegisterParms);
        //DevR::PrintR($aApiCall);
        //DevR::DieR();
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        $aApiCallResult = array();
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult; // return error
        }
        $aApiCallResult['status'] = 'success';
        $aApiCallResult['result'] = $aApiCall['souq']['result'];
        return $aApiCallResult; // return results
    }

    /**
     * Add a customer address
     * @param $aParams
     * @return bool
     */
    public static function AddNewAddress($aParams) {
        $aCRLFFilter = array("\r\n", "\n", "\r");
        $sApiRegisterParms = '[id_customer=' . Yii::app()->session['id_customer'] . ']';
        $sApiRegisterParms .= '[firstname=' . $aParams['firstname'] . ']';
        $sApiRegisterParms .= '[lastname=' . $aParams['lastname'] . ']';
        $sApiRegisterParms .= '[CountryCode=' . Yii::app()->params['country_landline_code'] . ']';
        $sApiRegisterParms .= '[OperatorPrefix=' . $aParams['OperatorPrefix'] . ']';
        $sApiRegisterParms .= '[phone=' . $aParams['phone'] . ']';
        $sApiRegisterParms .= '[CountryLandlineCode=' . Yii::app()->params['country_landline_code'] . ']';
        $sApiRegisterParms .= '[city=' . $aParams['city'] . ']';
        $sApiRegisterParms .= '[AddressArea=' . $aParams['AddressArea'] . ']';
        $sApiRegisterParms .= '[country=' . Yii::app()->params['CountryIsoCode'] . ']';
        $sApiRegisterParms .= '[street=' . $aParams['street'] . ']';
        $sApiRegisterParms .= '[building_no=' . $aParams['building_no'] . ']';
        $sApiRegisterParms .= '[apartment_no=' . $aParams['apartment_no'] . ']';
        $sApiRegisterParms .= '[floor_no=' . $aParams['floor_no'] . ']';
        $sApiRegisterParms .= '[note=' . $aParams['note'] . ']';
        //print_r($sApiRegisterParms);exit;
        //DevR::DieR();
        $sApiRegisterParms = str_replace($aCRLFFilter, ' ', $sApiRegisterParms);
        $aApiCall = ApiOutput::getOutput('AddShippingAddress', $sApiRegisterParms);
        //DevR::PrintR($aApiCall);DevR::DieR();
        if ($aApiCall === FALSE) {
            return FALSE;
        }

        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aResult['error'] = $aApiCall['souq']['request']['errors']['error'];
        } else {
            $aResult['id_customer_address'] = $aApiCall['souq']['result']['id_customer_address'];
        }
        return $aResult;
    }

    public static function AwbTrackRequest($params) {
        Yii::import('ext.EHttpClient.*');
        @set_time_limit(60);
        $oClient = new EHttpClient();
        $oClient->setUri(Yii::app()->params['aramexUri']);
        $oClient->setConfig(array(
            'maxredirects' => 0,
            'timeout' => 60,
            'useragent' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13'
                )
        );
        $oClient->setParameterGet($params);
        $sResponse = $oClient->request('GET');
        if ($sResponse->getStatus() == 200) {
            //$sContentType = $sResponse->getHeader('Content-type');
            $sContentBody = $sResponse->getBody();
            $aResult = ApiOutput::XmlToArray($sContentBody);
            Yii::log("\n" . $sContentBody . "\n" . Yii::app()->params['logNL'], CLogger::LEVEL_PROFILE, "aramex");
            return $aResult;
        }
        Yii::log('| Aramex API | ' . $params['ShipmentNumber'] . Yii::app()->params['logNL'], CLogger::LEVEL_ERROR, 'errors');
        return FALSE;
    }

    public static function CustomerVerificationGenerateCode($aParams) {
        switch ($aParams['type']) {
            case 'SMS':
                $sParams = '[id_customer=' . $aParams['id_customer'] . ']';
                $sParams .= '[id_customer_address=' . $aParams['id_customer_address'] . ']';
                $sParams .= '[phone=' . $aParams['phone'] . ']';
                $sParams .= '[type=sms]';
                break;
            case 'Email':
                $sParams = '[id_customer=' . $aParams['id_customer'] . ']';
                $sParams .= '[type=email]';
                break;
            default:
                return;
                break;
        }
        $aApiCall = ApiOutput::getOutput('CustomerVerificationGenerateCode', $sParams);
        //DevR::PrintR($aApiCall);
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        } else {
            return $aApiCall['souq']['result']['verification_code'];
        }
    }

    public static function CustomerVerificationCheck($aParams) {
        switch ($aParams['type']) {
            case 'SMS':
                $sParams = '[VerificationCode=' . $aParams['VerificationCode'] . ']';
                $sParams .= '[phone=' . $aParams['phone'] . ']';
                $sParams .= '[type=sms]';
                break;

            case 'Email':
                $sParams = '[VerificationCode=' . $aParams['VerificationCode'] . ']';
                $sParams .= '[email=' . $aParams['email'] . ']';
                $sParams .= '[type=email]';
                break;
            default:
                return;
                break;
        }

        $aApiCall = ApiOutput::getOutput('CustomerVerificationCheck', $sParams);
        //DevR::PrintR($aApiCall);DevR::DieR();
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        } else {
            return $aApiCall['souq']['result']['status'];
        }
    }

    /**
     * get the cart of a customer
     * @return array|bool
     */
    public static function GetCart() {
        //$sParams = '[id_session=' . Yii::app()->session->sessionID . ']';
        $sParams = '[id_session=' . Yii::app()->session['user_info']['sessionId'] . ']';
        $sParams .= '[items_details=1]';
        $sParams .= '[units_details=1]';
        $sParams .= '[language=' . Yii::app()->getLanguage() . ']';
        $sParams .= '[country=' . Yii::app()->params["CountryIsoCode"] . ']';

        $aApiCall = ApiOutput::getOutput('GetCart', $sParams);

        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        }

        return $aApiCall;
    }

    /**
     * Get all the orders
     * @param $param
     * @param string $type
     * @return array|bool
     */
    public static function GetOrders($param, $type = 'id_order') {
        if ($type == 'pseudonym') {
            $sParams = '[pseudonym=' . $param . ']';
            //$sParams .= '[date_lastchange=' . date('Y-m-d') . ']';
        } else {
            $sParams = '[id_order=' . $param . ']';
        }
        $sParams .= '[language=' . Yii::app()->getLanguage() . ']';
        $aApiCall = ApiOutput::getOutput('GetOrders', $sParams, 'json');
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        return $aApiCall;
    }

    /**
     * Search items
     * @param $params
     * @return array|bool
     */
    public static function search($params) {
        $params = array_merge($params, array('s' => Yii::app()->params['seller'], 'with_seller_units' => '1', 'item_collpase' => '1'));
        $sParams = '[country=ae][language=' . Yii::app()->getLanguage() . '][type=search][extra_params=' . json_encode($params) . ']';
        $aApiCall = ApiOutput::getOutput('GetContent', $sParams, 'json');
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (isset($aApiCall['@nodes']['result'][0]['@nodes']['data'][0]['@value'])) {
            return $aApiCall['@nodes']['result'][0]['@nodes']['data'][0]['@value'];
        } else {
            return array(
                'row_all' => 0,
                'items' => array(),
                'refined_attributes' => array()
            );
        }
    }

    /**
     * Get product
     * @param $id
     * @return array|bool
     */
    public static function getProduct($id) {
        $extraParams = array('grouped_attributes' => '1', 'get_item_connections' => '1', 'all_item_images' => '1');
        $sParams = '[country=ae][language=' . Yii::app()->getLanguage() . '][type=offers][id_item=' . $id . '][extra_params=' . json_encode($extraParams) . ']';
        $aApiCall = ApiOutput::getOutput('GetContent', $sParams, 'json');
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (isset($aApiCall['@nodes']['result'][0]['@nodes']['data'][0]['@value']['item_data'])) {
            $aApiProductInfo = array();
            $winnerUnits = isset($aApiCall['@nodes']['result'][0]['@nodes']['data'][0]['@value']['units']['winner_unit']) ? $aApiCall['@nodes']['result'][0]['@nodes']['data'][0]['@value']['units']['winner_unit'] : '';
            $aApiProductInfo = array('item_data' => $aApiCall['@nodes']['result'][0]['@nodes']['data'][0]['@value']['item_data'],
                'winner_units' => $winnerUnits);
            return $aApiProductInfo;
        } else {
            return array();
        }
    }

    /**
     * Change password
     * @param $params
     * @return array|bool
     */
    public static function changePassword($params) {
        $sParams = '[id_customer=' . $params['id_customer'] . ']';
        $sParams.= '[old_password=' . $params['old_password'] . ']';
        $sParams.= '[password=' . $params['password'] . '][language=' . Yii::app()->getLanguage() . ']';
        $sParams.= '[password_confirmation=' . $params['password_confirmation'] . ']';
        $aApiCall = ApiOutput::getOutput('ChangePassword', $sParams);
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        $aApiCallResult = array();
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult; // return error
        }
        $aApiCallResult['status'] = "success";
        return $aApiCallResult; // return results
    }

    /*
     * Function : API call to create wish list
     * Response : If success return id_customer_list 
     * Params   : language,id_customer,list_name,list_description,privacy(public/hidden)
     */

    public static function AddWhisList($aParams) {

        $sApiWishListParms = '[language=' . $aParams['language'] . ']';
        $sApiWishListParms .= '[id_customer=' . $aParams['id_customer'] . ']';
        $sApiWishListParms .= '[list_name=' . $aParams['list_name'] . ']';
        $sApiWishListParms .= '[list_description=' . $aParams['list_description'] . ']';
        $sApiWishListParms .= '[list_privacy=' . $aParams['list_privacy'] . ']';
        $aApiCall = ApiOutput::getOutput('AddWishList', $sApiWishListParms);
        $aApiCallResult = array();
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult;
        }
        $aApiCallResult['status'] = $aApiCall['souq']['result']['success'];
        $aApiCallResult['id_cutomer_list'] = $aApiCall['souq']['result']['id_customer_list'];
        return $aApiCallResult;
    }

    /*
     * Function : API to add item wish list
     * Response : If success return id_customer_items_list_entry 
     * Params   : language,id_customer,id_cutomer_list,id_item,comment,position_in_list
     */

    public static function AddWishListItem($aParams) {

        $sApiAddWishListItem = '[language=' . Yii::app()->getLanguage() . ']';
        $sApiAddWishListItem.='[id_customer=' . $aParams['id_customer'] . ']';
        $sApiAddWishListItem.='[id_customer_list=' . $aParams['id_customer_list'] . ']';
        $sApiAddWishListItem.='[id_item=' . $aParams['id_item'] . ']';
        $sApiAddWishListItem.='[comment=' . $aParams['comment'] . ']';
        $sApiAddWishListItem.='[position_in_list=' . $aParams['position_in_list'] . ']';
        $aApiCall = ApiOutput::getOutput('AddWishListItem', $sApiAddWishListItem);
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult;
        }
        $aApiCallResult['status'] = $aApiCall['souq']['result']['success'];
        $aApiCallResult['id_customer_items_list_entry'] = $aApiCall['souq']['result']['id_customer_items_list_entry'];
        return $aApiCallResult;
    }

    /*
     * Function : API to get user wish list
     * Response : If success return user wish_lists 
     * Params   : language,id_customer
     */

    public static function GetWishList($aParams) {

        $sApiGetWishList = '[language=' . Yii::app()->getLanguage() . ']';
        $sApiGetWishList.='[id_customer=' . $aParams['id_customer'] . ']';
        $aApiCall = ApiOutput::getOutput('GetWishList', $sApiGetWishList);

        $aApiCallResult = array();
        if ($aApiCall === FALSE) {
            return FALSE;
        }

        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        } else {
            if ($aApiCall['souq']['result']['count'] > 0) {
                $aApiCallResult['wish_list_status'] = 'SUCCESS';
                $aApiCallResult['wish_list'] = $aApiCall['souq']['result']['wish_lists'];
            } else {
                $aApiCallResult['wish_list_status'] = 'EMPTY';
                $aApiCallResult['wish_list'] = '';
            }
        }

        return $aApiCallResult;
    }

    /*
     * Function : API to get user wish list items
     * Response : If success return user wish_list items
     * Params   : language,id_customer,id_customer_list
     */

    public static function GetWishListItems($aParams) {

        $sApiGetWishListItem = '[language=' . Yii::app()->getLanguage() . ']';
        $sApiGetWishListItem.='[id_customer=' . $aParams['id_customer'] . ']';
        $sApiGetWishListItem.='[id_customer_list=' . $aParams['id_customer_list'] . ']';
        $aApiCall = ApiOutput::getOutput('GetWishListItems', $sApiGetWishListItem);

        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult;
        }
        $aApiCallResult['status'] = 'success';
        $aApiCallResult['wish_lists_items'] = $aApiCall['souq']['result'];
        return $aApiCallResult;
    }

    /*
     * Function : API to edit user wish list item
     * Response : If success return id_customer_items_list_entry and success set to 1
     * Params   : language,id_customer,id_customer_list,id_list_entry,id_item,comment,position_in_list
     */

    public static function EditWishListItem($aParams) {

        $sApiWishListItem = '[language=' . Yii::app()->getLanguage() . ']';
        $sApiWishListItem.='[id_customer=' . $aParams['id_customer'] . ']';
        $sApiWishListItem.='[id_customer_list=' . $aParams['id_customer_list'] . ']';
        $sApiWishListItem.='[id_list_entry=' . $aParams['id_list_entry'] . ']';
        $sApiWishListItem.='[id_item=' . $aParams['id_item'] . ']';
        $sApiWishListItem.='[comment=' . $aParams['comment'] . ']';
        $sApiWishListItem.='[position_in_list=' . $aParams['position_in_list'] . ']';
        $aApiCall = ApiOutput::getOutput('EditWishListItem', $sApiWishListItem);
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult;
        }
        $aApiCallResult['status'] = $aApiCall['souq']['result']['success'];
        $aApiCallResult['id_customer_items_list_entry'] = $aApiCall['souq']['result']['id_customer_items_list_entry'];
        return $aApiCallResult;
    }

    public static function EditWishList($aParams) {

        $sApiWishListParms = '[language=' . Yii::app()->getLanguage() . ']';
        $sApiWishListParms .= '[id_customer=' . $aParams['id_customer'] . ']';
        $sApiWishListParms .= '[id_customer_list=' . $aParams['id_customer_list'] . ']';
        $sApiWishListParms .= '[list_name=' . $aParams['list_name'] . ']';
        $sApiWishListParms .= '[list_description=' . $aParams['list_description'] . ']';
        $sApiWishListParms .= '[list_privacy=' . $aParams['list_privacy'] . ']';
        $aApiCall = ApiOutput::getOutput('EditWishList', $sApiWishListParms);

        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult;
        }
        $aApiCallResult['status'] = $aApiCall['souq']['result']['success'];
        $aApiCallResult['id_customer_list'] = $aApiCall['souq']['result']['id_customer_list'];
        return $aApiCallResult;
    }

    public static function DeleteWishList($aParams) {


        $sApiGetWishListItem = '[language=' . Yii::app()->getLanguage() . ']';
        $sApiGetWishListItem.='[id_customer=' . $aParams['id_customer'] . ']';
        $sApiGetWishListItem.='[id_customer_list=' . $aParams['id_customer_list'] . ']';
        $aApiCall = ApiOutput::getOutput('DeleteWishList', $sApiGetWishListItem);

        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult;
        }
        $aApiCallResult['status'] = $aApiCall['souq']['result']['success'];
        $aApiCallResult['wish_lists'] = $aApiCall['souq']['result'];
        return $aApiCallResult;
    }

    public static function DeleteWishListItem($aParams) {

        $sApiWishListItem = '[language=' . Yii::app()->getLanguage() . ']';
        $sApiWishListItem.='[id_customer=' . $aParams['id_customer'] . ']';
        $sApiWishListItem.='[id_customer_list=' . $aParams['id_customer_list'] . ']';
        $sApiWishListItem.='[id_list_entry=' . $aParams['id_list_entry'] . ']';
        $aApiCall = ApiOutput::getOutput('DeleteWishListItem', $sApiWishListItem);


        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult;
        }
        $aApiCallResult['status'] = $aApiCall['souq']['result']['success'];
        $aApiCallResult['wish_lists'] = $aApiCall['souq']['result'];
        return $aApiCallResult;
    }

    public static function ForgotPassword($email) {
        $sParams = '[email=' . $email . ']';
        $aApiCall = ApiOutput::getOutput('PasswordReminder', $sParams);
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        $aApiCallResult = array();
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult; // return error
        }
        $aApiCallResult['status'] = "success";
        return $aApiCallResult; // return results
    }

    public static function ResetPassword($params) {
        $sParams = '[c=' . $params['c'] . ']';
        $sParams.= '[k=' . $params['k'] . ']';
        $sParams.= '[password=' . $params['password'] . ']';
        $sParams.= '[password_confirmation=' . $params['password_confirmation'] . ']';
        $aApiCall = ApiOutput::getOutput('ForgetPassword', $sParams);
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        $aApiCallResult = array();
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult; // return error
        }
        $aApiCallResult['status'] = "success";
        return $aApiCallResult; // return results
    }

    public static function AddLead($params) {
        $sParams = '[email=' . $params['email'] . ']';
        $sParams.= '[first_name=' . $params['first_name'] . ']';
        $sParams.= '[last_name=' . $params['last_name'] . ']';
        $sParams.= '[id_country=1]';
        $sParams.= '[id_language=1]';
        $sParams.= '[id_newsletter=9]';
        $sParams.= '[id_campaign=210]';
        // $sParams.= '[id_newsletter=' .  Yii::app()->params['DEFAULT_NEWS_LETTER'] . ']';
        $sParams.= '[redirect_url=' . Yii::app()->createAbsoluteUrl('site/confirmSubscription') . ']';

        $aApiCall = ApiOutput::getOutput('AddLead', $sParams);
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (!isset($aApiCall['souq']['request']['errors_attr']['count'])) {
            return FALSE;
        }
        $aApiCallResult = array();
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['status'] = 'error';
            $aApiCallResult['error'] = $aApiCall['souq']['request']['errors']['error'];
            return $aApiCallResult; // return error
        }
        $aApiCallResult['status'] = "success";
        return $aApiCallResult; // return results
    }

    public static function AddCoupon($aParams) {
        $sApiparams = '[id_customer=' . Yii::app()->session['id_customer'] . ']';
        $sApiparams.='[coupon_code=' . $aParams['coupon_code'] . ']';
        $aApiCall = ApiOutput::getOutput('AddCouponToCart', $sApiparams);
        if ($aApiCall === FALSE) {
            return FALSE;
        }

        $aApiCallResult = array();
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['error_details'] = $aApiCall['souq']['request']['errors']['error_details'];
            $aApiCallResult['status'] = 'error';
        } else {
            $aApiCallResult['status'] = 'success';
            $aApiCallResult['result'] = $aApiCall['souq']['result'];
        }
        return $aApiCallResult;
    }

    public static function DeleteCoupon() {
        $sApiparams = '[id_customer=' . Yii::app()->session['id_customer'] . ']';
        $aApiCall = ApiOutput::getOutput('RemoveCouponFromCart', $sApiparams);
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        $aApiCallResult = array();
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            $aApiCallResult['error_details'] = $aApiCall['souq']['request']['errors']['error_details'];
            $aApiCallResult['status'] = 'error';
        } else {
            $aApiCallResult['status'] = 'success';
            $aApiCallResult['result'] = $aApiCall['souq']['result'];
        }
        return $aApiCallResult;
    }

    public static function getMultipleProduct($ids) {

        $extraParams = array('q' => $ids);
        $sParams = '[country=ae][language=' . Yii::app()->getLanguage() . '][type=search][extra_params=' . json_encode($extraParams) . ']';
        $aApiCall = ApiOutput::getOutput('GetContent', $sParams, 'json');

        if ($aApiCall === FALSE) {
            return FALSE;
        } else if (isset($aApiCall['@nodes']['result'][0]['@nodes']['data'][0]['@value'])) {
            return $aApiCall['@nodes']['result'][0]['@nodes']['data'][0]['@value'];
        }
    }

    public static function WriteItemReview($aParams) {

        $sItemReviewParams = '[language=' . Yii::app()->getLanguage() . ']';
        $sItemReviewParams .= '[id_customer=' . $aParams['id_customer'] . ']';
        $sItemReviewParams .= '[id_item=' . $aParams['id_item'] . ']';
        $sItemReviewParams .= '[review_title=' . $aParams['review_title'] . ']';
        $sItemReviewParams .= '[review_text=' . $aParams['review_text'] . ']';
        $sItemReviewParams .= '[item_rating=' . $aParams['item_rating'] . ']';
        $sItemReviewParams .= '[advantages=' . $aParams['advantages'] . ']';
        $sItemReviewParams .= '[disadvantages=' . $aParams['disadvantages'] . ']';
        $sItemReviewParams .= '[recommended=' . $aParams['recommended'] . ']';
        $aApiCall = ApiOutput::getOutput('ItemReview', $sItemReviewParams);
        if ($aApiCall === FALSE) {
            return FALSE;
        } else if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            // $aApiCallResult['error_details'] = $aApiCall['souq']['request']['errors']['error_details'];
            $aApiCallResult['error_details'] = "Sorry you can't write a review, you have to successfully buy at least one item on Toysrusuae.com before you can start writing reviews.";
            $aApiCallResult['status'] = 'error';
        } else {
            $aApiCallResult['status'] = 'success';
            $aApiCallResult['result'] = $aApiCall['souq']['result'];
        }
        return $aApiCallResult;
    }

    public static function Get_ItemReviews($aParams, $page = 1, $limit = 10) {
        $sItemReviewParams = '[country=ae]';
        $sItemReviewParams .= '[language=' . Yii::app()->getLanguage() . ']';
        $sItemReviewParams .= '[id_item=' . $aParams['id_item'] . ']';
        $extraparams = array('limit' => $limit, 'page' => $page);
        $sExtraParams = '[extra_params=' . json_encode($extraparams) . ']';
        $sItemReviewParams.=$sExtraParams;
        $aApiCall = ApiOutput::getOutput('GetReviews', $sItemReviewParams);
        $aApiCallResult = array();
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if ($aApiCall['souq']['request']['errors_attr']['count'] > 0) {
            return FALSE;
        }

        if (isset($aApiCall['souq']['result']['rows']))
            $aApiCallResult['ratingReviews'] = $aApiCall['souq']['result'];

        return $aApiCallResult;
    }

    public static function Get_CustomerInfo($id) {
        $sParams = '[id_customer=' . $id . ']';
        $aApiCall = ApiOutput::getOutput('GetCustomer', $sParams);
        $aApiCallResult = array();
        if ($aApiCall === FALSE) {
            return FALSE;
        }
        if (isset($aApiCall['souq']['request']['errors_attr']['count']) && ( $aApiCall['souq']['request']['errors_attr']['count'] > 0)) {
            return FALSE;
        }
        if (isset($aApiCall['customer'])) {
            $aApiCallResult = $aApiCall;
        }
        return $aApiCallResult;
    }

    public static function RepopulateCheckout() {
        $sParams = '[id_customer=' . Yii::app()->session['id_customer'] . ']';
        $sParams .= '[product=checkout_api]';
        $sParams .= '[language=' . Yii::app()->getLanguage() . ']';
        $aApiCall = ApiOutput::getOutput('RepopulateCheckout', $sParams);

        return $aApiCall;
    }

    public static function UpdateCart($idUnit,$qty){
        $sParams = '[id_unit=' . $idUnit . ']'
                . '[qty='.$qty.']'
                . '[id_session=' . Yii::app()->session['user_info']['sessionId'] . ']'
                . '[language=' . Yii::app()->getLanguage() . ']';
        $aApiCall = ApiOutput::getOutput('updateCart', $sParams);
        return $aApiCall;
    }
}

?>