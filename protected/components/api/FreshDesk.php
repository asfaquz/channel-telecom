<?php
/**
 * Created by PhpStorm.
 * User: rijesh
 * Date: 1/7/14
 * Time: 12:50 PM
 */

Class FreshDesk{

    public static function _call($url, $method, $postData = ''){
        $sDomain = Yii::app()->params['FRESHDESK_DOMAIN'];
        $sUserName = Yii::app()->params['FRESHDESK_USERNAME'];
        $sPassword= Yii::app()->params['FRESHDESK_PASSWORD'];
        $url = $sDomain.$url;
        $header[] = "Content-type: application/json";
        $ch = curl_init ($url);
        $postData = json_encode($postData);

        if( $method == "POST") {
            if( empty($postData) ){
                $header[] = "Content-length: 0";
            }
            curl_setopt ($ch, CURLOPT_POST, true);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postData);
        }
        else if( $method == "PUT" ) {
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "PUT" );
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postData);
        }
        else if( $method == "DELETE" ) {
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" ); // UNTESTED!
        }
        else {
            curl_setopt ($ch, CURLOPT_POST, false);
        }

        curl_setopt($ch, CURLOPT_USERPWD, "$sUserName:$sPassword");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $returnData = curl_exec ($ch);

        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if( !preg_match( '/2\d\d/', $http_status ) ) {
            Yii::log("\n" . $url ."ERROR: HTTP Status Code == " . $http_status . " (302 also isn't an error)\n", CLogger::LEVEL_PROFILE);
            return false;
        }
        $msgBody = json_decode($returnData);

        return $msgBody;
    }

    public static function addTicket($aData){
        $sUrl = "helpdesk/tickets.json";
        $msgBody = self::_call($sUrl, "POST", $aData);
        if(!isset($msgBody->helpdesk_ticket->display_id)){
            Yii::log("\n" . "ERROR: freshdesk not added\n", CLogger::LEVEL_PROFILE);
            return false;
        }
        return $msgBody;
    }


    public static function getAllTickets($email)
    {
        $sUrl = 'helpdesk/tickets.json?email='.$email.'&filter_name=new_my_open';
        return self::HandleFreshDeskRequest($sUrl, array(), 'GET');
    }
}