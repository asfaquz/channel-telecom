<?php

/**
 * Souq APIs and other shipping services APIs functions.
 * @author Syed Afaquz Zaman <szaman@souq.com>
 * @copyright Copyright &copy; 2014 Souq.com Group
 */
class Api {

    /**
     * Api call to search Item
     * @param country
     * @param language
     * @param $type=search
     * @param $extra_param : $page,$t,#with_seller_units,$item_collapse
     * @return json
     */
    public static function search($params) {
        /* Prepare API Parameter */
        $aApiParams = array();
        if (!empty($params)):
            $aApiParams['country'] = Yii::app()->params['country_iso_code'];
            $aApiParams['language'] = Yii::app()->getLanguage();
            $aApiParams['type'] = 'search';
            $aApiParams['extra_params'] = json_encode(array_merge($params, array(
                's' => Yii::app()->params['seller'],
                'with_seller_units' => '1',
                'item_collpase' => '1')));

            $response = self::_createApiRequest('GetContent', $aApiParams, 'json');
            if (isset($response['@nodes']['result'][0]['@nodes']['data'][0]['@value'])) :
                return $response['@nodes']['result'][0]['@nodes']['data'][0]['@value'];
            else:
                return FALSE;
            endif;
        endif;
    }
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