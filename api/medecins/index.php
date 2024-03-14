<?php
    require("../connexionDB.php");
    require("function_medecin.php");
    require("../deliverResponse.php");
    /// Identification du type de méthode HTTP envoyée par le client
    $http_method = $_SERVER['REQUEST_METHOD'];
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, POST, DELETE, PATCH, PUT, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    
    switch ($http_method){
        case "GET" :
            //Récupération des données dans l’URL si nécessaire
            if(isset($_GET['id'])) {
                $id= htmlspecialchars($_GET['id']);
                //Traitement des données
                $matchingData=getMedecinByIdAPI($linkpdo,$id);
                if (!$matchingData) {
                    $matchingData = null;
                }
            } else {
                $matchingData=getAllMedecinsAPI($linkpdo);
            }
            deliver_response(200, "Données récupérées avec succès", $matchingData);   
        break;

        case "POST" :
            // Récupération des données dans le corps
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
            //Traitement des données
            if ($data === null) {
                deliver_response(400, "[R401 API REST] : Phrase manquante (paramètre ou valeur à vérifier).", null);
            } else {
                $newData = insertMedecinAPI($linkpdo, $data);
                // createChunkFact($linkpdo, $data['phrase']);
                deliver_response(201, "Données créées avec succès.", $newData);
            }
        break;
           
        case "DELETE" :
            if(isset($_GET['id'])) {
                $id= (int) htmlspecialchars($_GET['id']);
                //Traitement des données
                $check = getMedecinByIdAPI($linkpdo, $id);
                if ($check === null) {
                    deliver_response(404, "Aucune ligne supprimée.", null);
                } else {
                    deleteMedecinByIdAPI($linkpdo,$id);
                    deliver_response(200, "Données id:$id supprimée avec succès.", null);
                }
            } else {
                deliver_response(400, "[R401 API REST] : Paramètre id invalide", null);
            }
        break;

        case "PATCH" : 
        // if ($role === "admin") {
            // Récupération des données dans le corps
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
            if ($data != null) {
                if(isset($_GET['id'])) {
                    $id= htmlspecialchars($_GET['id']);
                    $check = getMedecinByIdAPI($linkpdo, $id);
                    if ($check == null) {
                        deliver_response(404, "Aucune donnée trouvée.", null);
                    } else {
                        if (array_key_exists('civilite', $data)) {
                            $cv = $data['civilite'];
                            if ($cv !== 'M' && $cv !== 'Mme') {
                                deliver_response(400, "[R401 API REST] : Civilite doit etre 'M' ou 'Mme'", null);
                            }
                        } else {
                            updateMedecinAPI($linkpdo, $id, $data);
                            $res = getMedecinByIdAPI($linkpdo, $id);
                            deliver_response(200, "Données id:$id modifiées avec succès.", $res);
                        }
                    }
                } else {
                    deliver_response(400, "[R401 API REST] : Paramètre id invalide", null);
                }
            } else {
                deliver_response(400, "[R401 API REST] : Au moins un paramètre doit être fourni", null);
            }
            /*
        } else {
            $status = 403;
            $msg = "Action interdite : Vous n'avez pas les droits pour modifier une ressource.";
            deliver_response($status, $msg, null);
        }
        */
        break;
        case 'OPTIONS':
            deliver_response(204, 'Autorisation methode OPTIONS', null);
        break;
    } 
    
?>
