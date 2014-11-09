<?php


return $aDefaultParams =array(
       
        //Dev Database Configuration
        'db_host' => 'localhost',
        'db_name' => 'channel-skin',
        'db_user' => 'root',
        'db_pass' => 'adminadmin',
    
        //Dev Api Credentials
        'ApiIdent' => 't0y$RU$!dEnT',
        'ApiHashKey' => '9122afe428331fc93c6ad068b0d77702',
        'api_host' => 'http://api.souq.com/',
        'api_host_agent' =>'api.souq.com',
        'api_file' => 'url_api_controller4.php',
    
          
        //Seller Credentials
        'IdSeller' => '163810',
        'seller' => 'toysrus+OR+babiesrus', // we have two sellers now
    
        'PaymentProduct' => 'checkout_api',
        'is_installed' => 0,
        'siteMode' => 'dev', //dev or production
        'paginationLimit' => 12,
        
        
        //Freshdesk Credetials
        'FRESHDESK_DOMAIN' => 'http://testsouq.freshdesk.com/',
        'FRESHDESK_USERNAME' => 'r4208MhhGhBrOxH1l1OM',
        'FRESHDESK_PASSWORD' => 'rijesh123',
        'FRESHDESK_ID' => '6626_114945',
        'FRESHDESK_GROUP_ID'   => '1000181737',
    
        'GOOGLE_ANALYTICS_ID' => 'UA-25918195-1',

        'DEFAULT_NEWS_LETTER' => 1,
       
        'CONTACT_US_ADMINS' => array('szaman@souq.com', 'rkandathil@souq.com','dkuchchangi@souq.com'),
    );


?>