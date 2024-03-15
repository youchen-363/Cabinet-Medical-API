<?php
    require("database/connexionDB.php");
    require("functions/function_checkauth.php");
    require("functions/function_statistique.php");
    require("functions/function_consultation.php");
    require("functions/function_medecin.php");
    require("functions/function_usager.php");
    require("functions/deliverResponse.php");

    /// Identification du type de méthode HTTP envoyée par le client
    $http_method = $_SERVER['REQUEST_METHOD'];
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, POST, DELETE, PATCH, PUT, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    $token = get_bearer_token();
    $role = getAuthorisation($token);
    switch($role){
        case 'null':
            deliver_response(405, "[YCV-AUTHAPI] jwt token inexistant");
            break;
        case 'none':
            deliver_response(400, "[YCV-AUTHAPI] jwt token invalide");
            break;
        case 'admin':
        case 'user':
            switch ($http_method){
                case "GET":
                    if(isset($_GET['user_stat'])){
                        $user = $_GET['user_stat'];
                        if($user == 'medecins'){
                            $cons = getAllConsultations($linkpdo);
                            $matchingData = calculerDureeMeds($linkpdo, $cons);
                            deliver_response(200, "Données statistiques des médecins récupérées avec succès", $matchingData);
                        } else if($user == 'usagers') {
                            $usg = getAllUsagersAPI($linkpdo);
                            $matchingData = calculerAge($usg);
                            deliver_response(200, "Données statistiques des usagers récupérées avec succès", $matchingData);
                        } else {
                            deliver_response(400, 'Données statistiques inexistantes pour la valeur '.$user);
                        }
                    } else {
                        deliver_response(400, 'Aucune valeur renseignée pour la recherche de statistiques, veuillez préciser entre [medecins/usagers]');
                    }
                    break;
                default:
                    deliver_response(405, 'Methode connue mais non supportée par cette API');
                    break;
            }  
    }
?>
