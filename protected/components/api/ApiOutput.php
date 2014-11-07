<?php

/**
 * Souq API caller.
 * @author Rijesh Kandathil <rkandathil@souq.com>/Syed Asfaquz Zaman<szaman@souq.com>
 * @copyright Copyright &copy; 2013 Souq.com Group
 */
class ApiOutput {

    // -- Api Access
    private static $sApiIdent;
    private static $sApiHashKey;

    /**
     * return API Output
     * <code>
     * $aApiOutput = ApiOutput::getOutput('GetUnits','[id_item=67597]','json','basic,note,seller', 'ident')
     * </code>
     *
     * @access public
     * @param   string	$sService		[service name ]
     *          string	$sParams		["[id_item=67597][id_seller=801954"]
     *          integer $sSessionTry	[if session timeout re login]
     *          string	$sResult		[xml|json]
     *          string	$sOutput		[basic,note,seller|empty]
     *          string	$sAccessLevel	[ident|nonce]
     * @return array
     */
    public static function getOutput($sService = '', $sParams = '', $sResult = 'xml', $sOutput = '', $sAccessLevel = 'ident') {
        self::$sApiIdent = Yii::app()->params['ApiIdent'];
        self::$sApiHashKey = Yii::app()->params['ApiHashKey'];
        try {

            $sOutput = self::CallApi($sService, $sParams, $sResult, $sOutput, $sAccessLevel);

            if ($sOutput === FALSE) {
                throw new Exception('Souq service down!');
            }
            switch ($sResult) {
                case 'xml':
                    try {
                        $aApiOutput = self::XmlToArray($sOutput);
                        //if (!isset($aApiOutput['souq']['request']['errors_attr']['count']))
                        //throw new Exception;
                    } catch (Exception $e) {
                        throw new Exception('XML convert error!');
                    }
                    break;
                case 'json':
                    $aApiOutput = CJSON::decode($sOutput, TRUE);
                    //if (!isset($aApiOutput['souq']['request']['errors_attr']['count']))
                    //throw new Exception;
                    break;
            }
            ## If user still logged in on app but timeout on souq logout him
            ## this happend on error SESSION_EXPIRED_EXCEPTION
            if ($sResult != 'json' && isset($aApiOutput['souq']['request']['errors_attr']['count']) && $aApiOutput['souq']['request']['errors_attr']['count'] > 0) {
                if ($aApiOutput['souq']['request']['errors']['error'] == 'SESSION_EXPIRED_EXCEPTION') {
                    Yii::app()->request->redirect(Yii::app()->createUrl('/site/logout'));
                }
            }
            return $aApiOutput;
        } catch (Exception $e) {
            Yii::log($e->getMessage() . '| Souq Api XmlToArray | ' . $sOutput . Yii::app()->params['logNL'], CLogger::LEVEL_ERROR, 'errors');
            return FALSE;
        }
    }

    /**
     * return API Access level params
     * <code>
     * $aApiOutput = ApiOutput::createApiAccessLevel('ident')
     * </code>
     *
     * @access private
     * @param   string $sAccessLevel    (anonymous,ident,ident-nonce,signed,signed-nonce)
     * 			string $sParams
     * @return array
     */
    private static function createApiAccessLevel($sAccessLevel = 'anonymous', $sParams = '') {
        try {
            switch ($sAccessLevel) {
                case 'anonymous':
                    $sAccessLevelParam = '';
                    break;
                case 'ident':
                    $sAccessLevelParam = '&ident=' . self::$sApiIdent;
                    break;
                case 'ident-nonce':
                    //$aNonceApiResult =self::createApiNonce($sService='GetNonce','[ident='.self::$sApiIdent.']',$sResult='json');
                    try {
                        $sOutput = self::CallApi($sService = 'GetNonce', '[ident=' . self::$sApiIdent . ']', $sResult = 'xml');
                        switch ($sResult) {
                            case 'xml':
                                try {
                                    $aApiOutput = self::XmlToArray($sOutput);
                                } catch (Exception $e) {
                                    throw $e;
                                }
                                break;
                            case 'json':
                                $aApiOutput = json_decode($sOutput);
                                break;
                            default:
                                try {
                                    $aApiOutput = self::XmlToArray($sOutput);
                                } catch (Exception $e) {
                                    throw $e;
                                }
                                break;
                        }
                    } catch (Exception $e) {
                        throw $e;
                    }
                    if (!isset($aApiOutput['souq']['result']['nonce'])) {
                        throw new Exception('Souq Api nonce');
                    }
                    $sAccessLevelParam = '&ident=' . self::$sApiIdent . '&nonce=' . $aApiOutput['souq']['result']['nonce'];

                    break;
                case 'signed':
                    $sAccessLevelParam = '&ident=' . self::$sApiIdent . '&signature=' . md5($sParams . self::$sApiIdent . self::$sApiHashKey);
                    break;
                case 'signed-nonce':
                    try {
                        $sOutput = self::CallApi($sService = 'GetNonce', '[ident=' . self::$sApiIdent . ']', $sResult = 'xml');
                        $sResult = 'xml';
                        switch ($sResult) {
                            case 'xml':
                                try {
                                    $aApiOutput = self::XmlToArray($sOutput);
                                } catch (Exception $e) {
                                    throw $e;
                                }
                                break;
                            case 'json':
                                $aApiOutput = json_decode($sOutput);
                                break;
                            default:
                                try {
                                    $aApiOutput = self::XmlToArray($sOutput);
                                } catch (Exception $e) {
                                    throw $e;
                                }
                                break;
                        }
                    } catch (Exception $e) {
                        throw $e;
                    }
                    if (!isset($aApiOutput['souq']['result']['nonce'])) {
                        throw new Exception('Souq Api nonce');
                    }
                    $sAccessLevelParam = '&ident=' . self::$sApiIdent . '&nonce=' . $aApiOutput['souq']['result']['nonce'] . '&signature=' . md5($sParams . self::$sApiIdent . $aApiOutput['souq']['result']['nonce'] . self::$sApiHashKey);
                    break;
            }
            return $sAccessLevelParam;
        } catch (Exception $e) {
            Yii::log('| Souq Api access error | ' . $e->getMessage() . Yii::app()->params['logNL'], CLogger::LEVEL_ERROR, 'errors');
            return FALSE;
        }
    }

    /**
     * return API nonce
     * <code>
     * $aApiOutput = ApiOutput::createApiNonce('GetNonce','[ident=]','json')
     * </code>
     *
     * @access private
     * @param   string $sService    [service name ]
     *          string $sParams     [ident=67597][id_seller=801954"]
     *          string $sResult     [xml|json]
     * @return array
     */
    /* private static function createApiNonce($sService = '', $sParams = '', $sResult = 'xml') {
      try {
      $sResult = self::CallApi($sService, $sParams, $sResult);
      $sResult = 'xml';

      switch ($sResult) {
      case 'xml':
      try {
      $aApiOutput = self::XmlToArray($sOutput);
      } catch (Exception $e) {
      throw $e;
      }
      break;
      case 'json':
      $aApiOutput = json_decode($sOutput);
      break;
      default:
      try {
      $aApiOutput = self::XmlToArray($sOutput);
      } catch (Exception $e) {
      throw $e;
      }
      break;
      }
      return $aApiOutput;
      } catch (Exception $e) {
      throw $e;
      }
      } */

    /**
     * Souq API caller
     * <code>
     * $aApiOutput = ApiOutput::CallApi('GetNonce','[ident=]','json')
     * </code>
     *
     * @access private
     * @param   string $sService    [service name ]
     *          string $sParams     [ident=67597][id_seller=801954"]
     *          string $sResult     [xml|json]
     * @return array
     */
    private static function CallApi($sService = '', $sParams = '', $sResult = 'xml', $sOutput = '', $sAccessLevel = 'ident') {
        $sApiHost = Yii::app()->params['api_host'];
        $sApiHostAgent=Yii::app()->params['api_host_agent'];
        $sApiFile = Yii::app()->params['api_file'];
        /*                    
        if (Yii::app()->params['siteMode'] != 'production') {
                $sApiUsername = 'souq';
                $sApiPassword = 'mqVtb3703ojV6';
        }*/
        
        $sLanguageCode = Yii::app()->getLanguage();
        $sCountryCode = Yii::app()->params['CountryIsoCode'];
        if (isset(Yii::app()->session['user_info']['sessionId'])) {
            $sSessionId = Yii::app()->session['user_info']['sessionId'];
        } else {
            $sSessionId = Yii::app()->session->sessionId;
        }
        if(isset(Yii::app()->request->cookies["c_ident"]->value)){
           $cIdent = Yii::app()->request->cookies["c_ident"]->value;
        }else{
            $cIdent = (int) (microtime(TRUE) * 10000);
            $cookie = new CHttpCookie("c_ident", $cIdent);
            $cookie->expire = time() + (10 * 365 * 24 * 60 * 60);
            Yii::app()->request->cookies["c_ident"] = $cookie;
        }
        $sURL = $sApiHost . $sCountryCode . '-';
        $sURL .= $sLanguageCode . '/' . $sApiFile;
        $sURL .= '?service=' . $sService . '&params=' . urlencode($sParams) . '&result=' . $sResult . '&output=' . $sOutput.'&c_ident='.$cIdent;

        // -- Add the access level params
        $AccessLevel = self::createApiAccessLevel($sAccessLevel, $sParams);
        if ($AccessLevel === FALSE) {
            return FALSE;
        }
        $sURL .= $AccessLevel;
        @set_time_limit(60);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ($sURL));
        if (isset($sApiHostAgent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Souq (Spring; U; Spring )');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $sApiHostAgent));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if ($sService != 'GetNonce' && !defined('CLI')) {
            curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_COOKIEFILE, "cookiefile");
            curl_setopt($ch, CURLOPT_COOKIEJAR, "cookiefile");
            curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . $sSessionId);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if (isset($sApiUsername) && isset($sApiPassword)) {
            curl_setopt($ch, CURLOPT_USERPWD, $sApiUsername . ':' . $sApiPassword);
        }
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $sOutput = curl_exec($ch);

        if ($sOutput === false) {
            Yii::log(curl_error($ch) . ' | ' . $sURL . Yii::app()->params['logNL'], CLogger::LEVEL_ERROR, 'errors');
            return FALSE;
        }
        //DevR::VarDumpR($sOutput);

        if (count(explode('<?', $sOutput)) > 1) {
            $sOutput = explode('<?', $sOutput);
            $sOutput = '<?' . $sOutput[1];
        }

        curl_close($ch);
        Yii::log("\n" . urldecode($sURL) . "\nPHPSESSID=" . $sSessionId . "\n" . $sOutput . Yii::app()->params['logNL'], CLogger::LEVEL_PROFILE, "souq_api");
        return $sOutput;
    }

    /**
     * xml2array() will convert the given XML text to an array in the XML structure.
     * @link http://www.bin-co.com/php/scripts/xml2array/
     * @param   $contents - The XML text
     *          $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
     *          $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
     * @return  The parsed XML in an array form. Use DevR::PrintR() to see the resulting array structure.
     * @example $array =  xml2array(file_get_contents('feed.xml'));
     *              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
     */
    public static function XmlToArray($contents, $get_attributes = 1, $priority = 'tag') {

        if (!$contents)
            return array();

        if (!function_exists('xml_parser_create')) {
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if (!$xml_values) {
            return; //Not XML Values
        }

        //Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array; //Refference
        //Go through the tags.
        $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
        foreach ($xml_values as $data) {
            unset($attributes, $value); //Remove existing values, or there will be trouble
            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data); //We could use the array by itself, but this cooler.

            $result = array();
            $attributes_data = array();

            if (isset($value)) {
                if ($priority == 'tag')
                    $result = $value;
                else
                    $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($priority == 'tag')
                        $attributes_data[$attr] = $val;
                    else
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if ($type == "open") {//The starting of the tag '<tag>'
                $parent[$level - 1] = &$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag . '_' . $level] = 1;

                    $current = &$current[$tag];
                } else { //There was another element with the same tag name
                    if (isset($current[$tag][0])) {//If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level] ++;
                    } else {//This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag], $result); //This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag . '_' . $level] = 2;

                        if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$last_item_index];
                }
            } elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if (!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                } else { //If taken, put all things inside a list(array)
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level] ++;
                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }

                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level] ++; //0 and 1 index is already taken
                    }
                }
            } elseif ($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level - 1];
            }
        }
        //Return asso Array
        return($xml_array);
    }

    /**
     * http api call with xml header.
     * <code>
     * $aApiOutput = ApiOutput::CurlPost('google.com','<?xml ..', false)
     * </code>
     *
     * @access public
     * @param   string $url				[service url]
     *          string $XMLDataCreate	[xml]
     *          string $bReturnArray	[true|false]
     * @return array
     */
    public static function CurlPost($url, $XMLDataCreate, $bReturnArray = false) {


        //$oSession = new CHttpSession;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=UTF-8"));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $XMLDataCreate);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if ($response === false) {
            // echo curl_error($curl);
            Yii::log("\n" . $url . "\nPHPSESSID=" . $oSession->getSessionID() ."\n" . $XMLDataCreate . "\n" . curl_error($curl) . Yii::app()->params['logNL'], CLogger::LEVEL_PROFILE, "mobily_api");
            Yii::log("\n" . $url . "\nPHPSESSID=" . Yii::app()->session->sessionID . "\n" . $XMLDataCreate . "\n" . curl_error($curl) . Yii::app()->params['logNL'], CLogger::LEVEL_PROFILE, "mobily_api");
            Yii::log(curl_error($curl), CLogger::LEVEL_ERROR);
            Yii::log(curl_error($curl) . ' | ' . $url . ' | ' . $XMLDataCreate . Yii::app()->params['logNL'], CLogger::LEVEL_ERROR, 'errors');
        }
        Yii::log("\n" . $url . "\nPHPSESSID=" . $oSession->getSessionID() ."\n" . $XMLDataCreate . "\n" . $response . Yii::app()->params['logNL'], CLogger::LEVEL_PROFILE, "mobily_api");
        Yii::log("\n" . $url . "\nPHPSESSID=" . Yii::app()->session->sessionID . "\n" . $XMLDataCreate . "\n" . $response . Yii::app()->params['logNL'], CLogger::LEVEL_PROFILE, "mobily_api");
        if (true == $bReturnArray) {
            $response = self::XmlToArray($response);
        }

        curl_close($curl);

        return $response;
    }

}

?>