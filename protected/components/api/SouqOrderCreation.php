<?php

/**
 * Validate and place order in souq.
 * @author Hisham Aburob <haborob@souq.com>
 * @copyright Copyright &copy; 2014 Souq.com Group
 */
class SouqOrderCreation {

    /**
     * Place order by payment call back.
     * @param	string		$sSpringUrl	API URL
     * @param	array		$aRequest	GET headers to be passed in payment callback
     * @throws	Exception				Redirect to payment page with error
     */
    public static function doRequest($sSpringUrl, $aRequest) {

        Yii::import('ext.EHttpClient.*');
        unset($aRequest['language']);
        unset($aRequest['mob_st']);
        unset($aRequest['payment_method']);
        if(isset($aRequest['qc_s'])) unset($aRequest['qc_s']);
        $aRequest['ln'] = Yii::app()->getLanguage();

        try {
            @set_time_limit(60);

            ##########################################
            ##########################################
            $oClient = new EHttpClient($sSpringUrl);
            $oClient->setParameterGet($aRequest);
            /* if (Yii::app()->params['siteMode'] != 'production') {
              $oClient->setAuth('souq', 'mqVtb3703ojV6');
            } */
            $oClient->setHeaders('Cookie', 'PHPSESSID=' . Yii::app()->session['user_info']['sessionId']);
            //$oClient->setHeaders('Cookie', 'PHPSESSID=' . Yii::app()->session->sessionID);
            //$oClient->setHeaders('user-agent', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13');
            $oClient->setConfig(array(
                'timeout' => 60,
                'useragent' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13'
                    )
            );
            $oRespose = $oClient->request('GET');

            if ($oRespose->getStatus() != 200) {
                //$errorLogMsg = "\n[222ER|TIMEOUT]\nPHPSESSID=" . Yii::app()->session->sessionID . "\n" . $oRespose->getStatus() . Yii::app()->params['logNL'];
                throw new Exception();
            }
            $sResponse = $oRespose->getBody();
            $rStatus = $oRespose->getStatus();
            $aResponse = json_decode($sResponse, true);
            if (!is_array($aResponse) || empty($aResponse)) {
                //$errorLogMsg = "\n[223ER|WRONG_RESPONSE]\nPHPSESSID=" . Yii::app()->session['user_info']['sessionId'] . "\n" . $rStatus . Yii::app()->params['logNL'];

                throw new Exception();
            }
            if (isset($aResponse['html_answer']) && !empty($aResponse['html_answer']) && isset($aResponse['status']) && $aResponse['status'] == 46) {
                echo base64_decode($aResponse['html_answer']);
                Yii::app()->end();
            }

            $dIdOrder = self::returnApiResponseMessage($aResponse, 'id_order');
            if (is_bool($dIdOrder) || is_numeric($dIdOrder)) {
                if (is_bool($dIdOrder) && TRUE === $dIdOrder) {
                    throw new Exception();
                } else {
                    //////////////////////////////// 
                    //Adding Order Units in mobily_orders
                    try {
                        Yii::app()->session['orderId'] = $dIdOrder;
                        Yii::app()->session->remove('cart');
                        Yii::app()->request->redirect(Yii::app()->createUrl('/product/thanks'));
                    } catch (Exception $ex) {
                        throw $ex;
                    }

                    ////////////////////////////////////////////////////////
                }
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            Yii::app()->session->remove('cart');
            $errorHome = Yii::t('strings', 'Sorry there was an error occurred while processing your order. Please try again or if you have any problem please contact the support');
            Yii::app()->session['error_home'] = $errorHome;
            Yii::app()->request->redirect(Yii::app()->homeUrl);
            Yii::app()->request->redirect(Yii::app()->createUrl('/product/payment', array('error' => 2)));
        }
    }

    /**
     * Get order id from response message.
     * @param array		$aResponse	response headers from payment callback
     * @param string	$sKey		key to be got from response
     * @return boolean|array
     */
    private static function returnApiResponseMessage(array $aResponse, $sKey = 'message') {
        $sNodesVarName = '@nodes';
        $sValueVarName = '@value';
        if (self::isError($aResponse)) {
            return true;
        } else {
            if (isset($aResponse[$sNodesVarName]['result'][0][$sNodesVarName]['result'][0][$sValueVarName][$sKey])) {
                $sSuccess = $aResponse[$sNodesVarName]['result'][0][$sNodesVarName]['result'][0][$sValueVarName][$sKey];
                return $sSuccess;
            }
        }
    }

    /**
     * Is there error in response headers
     * @param	array	$aResponse
     * @return	boolean
     */
    private static function isError($aResponse) {
        $sNodesVarName = '@nodes';
        $sValueVarName = '@value';
        $aErrors = $aResponse[$sNodesVarName]['request'][0][$sNodesVarName]['errors'][0][$sNodesVarName];
        if (is_array($aErrors) && !empty($aErrors)) {
            return true;
        }
        return false;
    }

}
