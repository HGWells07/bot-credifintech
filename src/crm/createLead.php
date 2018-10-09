<?php

define ("ACCOUNT_ID", "F47BF91480DC9BB7126544EF8FFC3E63");
define ("SECRET_KEY", "00C274DE6B1D2AA4ED5D5494BB4A3F65");

function createLead($params){

    $requestID = session_id();

    $method = 'createLeads';

    $data = array(                                                                                
    'method' => $method,                                                                      
    'params' => $params,                                                                      
    'id' => $requestID,                                                                       
    ); 

    $queryString = http_build_query(array('accountID' => ACCOUNT_ID, 'secretKey' => SECRET_KEY)); 

    $url = "http://api.sharpspring.com/pubapi/v1/?$queryString";

    $data = json_encode($data);                                                                   
    $ch = curl_init($url);

    //Opciones de la solicitud enviada al servidor
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                              
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                               
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                   
    'Content-Type: application/json',                                                         
    'Content-Length: ' . strlen($data)                                                        
    )); 

    $result = curl_exec($ch);                                                                     
    curl_close($ch);

    return $result;
}

?>