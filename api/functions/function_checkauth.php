<?php 
define('AUTHAPI', 'https://ycvcabinetauth.alwaysdata.net/auth');
define('NO_DATA', 'null');

    function get_authorization_header(){
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } else if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        return $headers;
    }

    function get_bearer_token() {
        $headers = get_authorization_header();
        
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                if($matches[1]=='null') //$matches[1] est de type string et peut contenir 'null'
                    return null;
                else
                    return $matches[1];
            }
        }
        return null;
    }

    function getAuthorisation($token){
        // Paramètres d'en-tête à envoyer
        $headers = array(
        'Authorization: Bearer '.$token.'',
        'Content-Type: application/json');

        $curl = curl_init();

        // Configuration de cURL
        curl_setopt($curl, CURLOPT_URL, AUTHAPI);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // Exécution de la requête
        $response = curl_exec($curl);
        $tab = json_decode($response, true);
        if(!empty($tab['data'])){
            return $tab['data'];
        } else {
            return NO_DATA;
        }
    }
?>