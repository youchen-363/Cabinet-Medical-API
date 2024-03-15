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
    
    // switch ($http_method){
    //     case "GET" :
    //         //Récupération des données dans l’URL si nécessaire
    //         if(isset($_GET['id'])) {
    //             $id= htmlspecialchars($_GET['id']);
    //             //Traitement des données
    //             $matchingData=getUsagersById($linkpdo,$id);
    //         } else {
    //             $matchingData=getAllUsagers($linkpdo);
    //         }
    //         deliver_response(200, "Données récupérées avec succès", $matchingData);   
    //     break;
/*
        case "POST" :
            // Récupération des données dans le corps
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
            //Traitement des données
            if ($data === null) {
                deliver_response(400, "[R401 API REST] : Phrase manquante (paramètre ou valeur à vérifier).", null);
            } else {
                $newData = createChuckFact($linkpdo, $data['phrase']);
                // createChunkFact($linkpdo, $data['phrase']);
                deliver_response(201, "Données créées avec succès.", $newData);
            }
        break;
            
        case "DELETE" :
            if ($role === "admin") {
                if(isset($_GET['id'])) {
                    $id= (int) htmlspecialchars($_GET['id']);
                    //Traitement des données
                    $check = checkIDExists($linkpdo, $id);
                    if ($check === false) {
                        deliver_response(404, "Aucune ligne supprimée.", null);
                    } else {
                        deleteChuckFact($linkpdo,$id);
                        deliver_response(200, "Données id:$id supprimée avec succès.", null);
                    }
                } else {
                    deliver_response(400, "[R401 API REST] : Paramètre id invalide", null);
                }
            } else {
                $status = 403;
                $msg = "Action interdite : Vous n'avez pas les droits pour modifier une ressource.";
                deliver_response($status, $msg, null);
            }
        break;

        case "PATCH" : 
            if ($role === "admin") {
                // Récupération des données dans le corps
                $postedData = file_get_contents('php://input');
                $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
                if ($data != null) {
                    //Traitement des données
                    // $phrase = array_key_exists('phrase', $data) ? $data['phrase'] : null; 
                    // $vote = array_key_exists('vote', $data) ? $data['vote'] : null; 
                    // $faute = array_key_exists('faute', $data) ? $data['faute'] : null; 
                    // $signalement = array_key_exists('signalement', $data) ? $data['signalement'] : null; 
                    $phrase = $data['phrase']; 
                    $vote = $data['vote']; 
                    $faute = $data['faute']; 
                    $signalement = $data['signalement']; 
                    if ($phrase == null && $vote == null && $faute==null && $signalement == null) {
                        deliver_response(400, "[R401 API REST] : Au moins un paramètre doit être fourni", null);
                    } else {
                        if(isset($_GET['id'])) {
                            $id= htmlspecialchars($_GET['id']);
                            if (!checkIDExists($linkpdo, $id)) {
                                deliver_response(404, "Aucune donnée trouvée.", null);
                            } else {
                                updateChunkFact($linkpdo, $id, $faute, $signalement, $phrase, $vote, );
                                $res = readChuckFact($linkpdo, $id);
                                deliver_response(200, "Données id:$id modifiées avec succès.", $res);
                            }
                        } else {
                            deliver_response(400, "[R401 API REST] : Paramètre id invalide", null);
                        }
                    }
                } else {
                    deliver_response(400, "[R401 API REST] : Au moins un paramètre doit être fourni", null);
                }
            } else {
                $status = 403;
                $msg = "Action interdite : Vous n'avez pas les droits pour modifier une ressource.";
                deliver_response($status, $msg, null);
            }
        break;

        case "PUT" :
            if ($role === "admin") {
                // Récupération des données dans le corps
                $postedData = file_get_contents('php://input');
                $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
                //Traitement des données
                if ($data == null) {
                    deliver_response(400, "[R401 API REST] : Au moins un paramètre doit être fourni", null);
                } else {
                    $phrase = array_key_exists('phrase', $data) ? $data['phrase'] : null; 
                    $vote = array_key_exists('vote', $data) ? $data['vote'] : null; 
                    $faute = array_key_exists('faute', $data) ? $data['faute'] : null; 
                    $signalement = array_key_exists('signalement', $data) ? $data['signalement'] : null; 
                    if ($phrase == null || $vote == null || $faute==null || $signalement == null) {
                        deliver_response(400, "[R401 API REST] : Tous les paramêtres doivent être fournis", null);
                    } else {
                        if(isset($_GET['id'])) {
                            $id= htmlspecialchars($_GET['id']);
                            updateChunkFact($linkpdo, $id, $faute, $signalement, $phrase, $vote, );
                            $res = readChuckFact($linkpdo, $id);
                            deliver_response(200, "Données id:$id modifiées avec succès.", $res);
                        } else {
                            deliver_response(400, "[R401 API REST] : Paramêtre id invalide", null);
                        }
                    }
                }
            } else {
                $status = 403;
                $msg = "Action interdite : Vous n'avez pas les droits pour modifier une ressource.";
                deliver_response($status, $msg, null);
            }
        break;
*/
    //     case 'OPTIONS':
    //         deliver_response(204, 'Autorisation methode OPTIONS', null);
    //     break;
    // } 
    
?>
