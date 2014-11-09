<?php
/**
 * Call Souq APIs .
 * @author Syed Afaquz Zaman <szaman@souq.com>
 * @copyright Copyright &copy; 2014 Souq.com Group
 */

class ApiParamFormatter{
    static $staticApiIndex=array('extra_params');
    
    /**
     * Call ApiOutput with service name ,params and responseType expected
     * @param type $service
     * @param type $aApiParams
     * @param type $responseType
     * @return boolean
     * 
     * 
     */
    protected static function _createApiRequest($service = '', $aApiParams = array(), $responseType = '') {
        $sApiParams = '';
        if (isset($service) && !empty($aApiParams)):
            //Create API params string
            foreach ($aApiParams as $key => $value):
                if(in_array($key,self::$staticApiIndex)):
                    $value=  json_encode($value);
                endif;
                $sApiParams.='[' . $key . '=' . $value . ']';
            endforeach;
            $aApiResponse = ApiOutput::getOutput($service, $sApiParams, $responseType);

            if (empty($aApiResponse) || $aApiResponse === FALSE) {
                return FALSE;
            }
            return $aApiResponse;
        endif;
        return FALSE;
    }
}
?>
