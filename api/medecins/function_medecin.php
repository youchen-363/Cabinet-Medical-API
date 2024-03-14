<?php
    /* Cote API */

    function getAllMedecinsAPI($linkpdo){
        $query = "SELECT * FROM Medecin ORDER BY nom, prenom";
        $res = $linkpdo -> query($query); 
        $arrRes = $res -> fetchAll(PDO::FETCH_ASSOC);
        return $arrRes;
    }
    
    function getMedecinByIdAPI($linkpdo, $id){
        $recherche = "SELECT * FROM Medecin  
                     WHERE idMedecin = :id   
                     ORDER BY nom, prenom";
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':id', $id);
        $st -> execute();
        $arrRes = $st -> fetch(PDO::FETCH_ASSOC);
        return $arrRes;
    }
    
    function updateUsagerResetMedecinAPI($linkpdo, $idMedecin){
        $st = $linkpdo -> prepare('UPDATE Usager set idMedecin = null
                                    where idMedecin = :idMedecin');
        $st -> bindParam(':idMedecin', $idMedecin);
        $ins = $st -> execute();
        return $ins;
    }

    function deleteConsultationByIdMedecinAPI($linkpdo, $idMedecin){
        $st = $linkpdo -> prepare("DELETE FROM Consulter where idMedecin = :idMedecin");
        $st -> bindParam(':idMedecin',$idMedecin);
        $ins = $st-> execute();
        return $ins;
    }

    function deleteMedecinByIdAPI($linkpdo, $id){
        deleteConsultationByIdMedecinAPI($linkpdo, $id);
        updateUsagerResetMedecinAPI($linkpdo, $id);
        $st = $linkpdo->prepare("DELETE FROM Medecin WHERE idMedecin = :idM");
        $st -> bindParam(':idM', $id);
        $ins = $st -> execute();
        return $ins;
    }

    function insertMedecinAPI ($linkpdo, $data) {
        global $linkpdo;
        $st = $linkpdo -> prepare("INSERT INTO Medecin (civilite, nom, prenom)
                                    values (:civilite, :nom, :prenom)");
        $st -> bindParam(':civilite', $data['civilite']);
        $st -> bindParam(':nom', $data['nom']);
        $st -> bindParam(':prenom', $data['prenom']);
            
        $ins = $st -> execute();
        return $ins;
    }

    // return le resultat d'execution
    // mettre a jour le medecin
    function updateMedecinAPI($linkpdo, $idM, $data){
        $req = "UPDATE Medecin SET ";
        $param = array();
        $added = false;
        if (array_key_exists('civilite', $data)) {
            $req .= "civilite = :civilite";
            $param['civilite'] = $data['civilite'];
            $added = true;
        }
        if (array_key_exists('nom', $data)){
            $req .= $added ? ", nom = :nom" : "nom = :nom";
            $param['nom'] = $data['nom'];
            $added = true;
        }
        if (array_key_exists('prenom', $data)) {
            $req .= $added ? ", prenom = :prenom" : "prenom = :prenom";
            $param['prenom'] = $data['prenom'];
        }
        $req .= " WHERE idMedecin = :id";
        $param['id'] = $idM;
        $st = $linkpdo -> prepare($req);
        $ins = $st -> execute($param);
        return $ins;
    }
    
    /* Cote client */

    function getMedecinByIdOrderId($linkpdo, $id){
        $recherche = "SELECT * FROM Medecin  
                     WHERE idMedecin = :id   
                     ORDER BY idMedecin";
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':id', $id);
        $st -> execute();
        $arrRes = $st -> fetch(PDO::FETCH_ASSOC);
        return $arrRes;
    }

    function getMedecinPrefereByNomUsager($nom){
        global $linkpdo;
        $recherche = "SELECT * FROM Usager 
                        WHERE nom = :search
                        ORDER BY nom, prenom";
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':search', $nom);                  
        $st->execute();
        if ($st !== false) {
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) {
                return htmlspecialchars(getNomMedecinById($row["idMedecin"]));
            }
        }
    }
    
    function getMedecinByNom($nom){
        global $linkpdo; 
        $nom = '%'. $nom . '%';
        $recherche = "SELECT * FROM Medecin WHERE
                        nom LIKE :search OR 
                        prenom LIKE :search
                        ORDER BY nom, prenom";
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':search', $nom);
        $st -> execute();
        $res = $st -> fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    function getNomMedecinById($linkpdo, $idM){
        $recherche = "SELECT * FROM Medecin WHERE
                        idMedecin = :search
                        ORDER BY nom, prenom";
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':search', $idM);
        $st -> execute();

        if ($st !== false) {
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) {
                return htmlspecialchars($row["nom"]);
            }
        }
        return "";
    }

    // obtenir le next id d'usager
    function nextValeurMedecin(){
        global $linkpdo;
        $req = "SELECT AUTO_INCREMENT
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = 'cabinet'
                AND TABLE_NAME = 'Medecin'";
        try {
            $result = $linkpdo->query($req);

            if ($result !== false) {
                $row = $result->fetch(PDO::FETCH_ASSOC);

                if ($row !== false) {
                    return htmlspecialchars($row["AUTO_INCREMENT"]);
                }
            }
        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // obtenir medecin par son nom exact
    function getIdMedecinByNom($nomMed){
        global $linkpdo;
        $res = -1;
        $req = "SELECT idMedecin FROM Medecin where nom = :nomMed ORDER BY nom, prenom";
        $result = $linkpdo->prepare($req);
        $result -> bindParam(':nomMed', $nomMed);
        $result -> execute();
        if ($result !== false) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) {
                $res = htmlspecialchars($row["idMedecin"]);
            }
        }
        return $res;
    }

    // Here new function 
    function getNomPrenomMedecinById($idM){
        global $linkpdo;
        $recherche = "SELECT * FROM Medecin WHERE
                        idMedecin = :search";
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':search', $idM);
        $st -> execute();

        if ($st !== false) {
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) {
                return htmlspecialchars($row["nom"] . " " . $row["prenom"]);
            }
        }
        return "";
    }

    // Here new function 
    function getIdMedecinByNomEtPrenom($nomMed, $prenomMed){
        global $linkpdo;
        $req = "SELECT idMedecin FROM Medecin where nom = :nomMed and prenom = :prenomMed";
        $result = $linkpdo->prepare($req);
        $result -> bindParam(':nomMed', $nomMed);
        $result -> bindParam(':prenomMed', $prenomMed);
        $result -> execute();
        if ($result !== false) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) {
                return htmlspecialchars($row["idMedecin"]);
            }
        }
        return null;
    }

    // obtenir medecin par son nom exact
    function getIdMedecinByNomPrenom($nomMed){
        global $linkpdo;
        $nomFiltre = "%" . $nomMed . "%";
        $res = -1;
        $req = "SELECT idMedecin FROM Medecin where nom LIKE :nomMed or prenom LIKE :nomMed ORDER BY nom, prenom";
        $result = $linkpdo->prepare($req);
        $result -> bindParam(':nomMed', $nomFiltre);
        $result -> execute();
        if ($result !== false) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) {
                $res = htmlspecialchars($row["idMedecin"]);
            }
        }
        return $res;
    }

?>