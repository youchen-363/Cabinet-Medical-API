<?php 
    
    /* Cote API */
    
    function getAllUsagersAPI($linkpdo){
        $query = "SELECT * FROM Usager";
        // ORDER BY UPPER(nom) asc
        $res = $linkpdo -> query($query); 
        $arrRes = $res -> fetchAll(PDO::FETCH_ASSOC);
        return $arrRes;
    }

    function getUsagerByIdAPI($linkpdo, $id){
        $recherche = "SELECT * FROM Usager WHERE idUsager = :id";
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':id', $id);
        $st -> execute();
        $arrRes = $st -> fetch(PDO::FETCH_ASSOC);
        return $arrRes;
    }

    function deleteConsultationByIdUsagerAPI($linkpdo, $idUsager){
        $st = $linkpdo -> prepare("DELETE FROM Consulter where idUsager = :idUsager");
        $st -> bindParam(':idUsager',$idUsager);
        $ins = $st-> execute();
        return $ins;
    }
    function deleteUsagerParIdAPI($linkpdo, $id){
        deleteConsultationByIdUsager($linkpdo, $id);
        $st = $linkpdo->prepare("DELETE FROM Usager WHERE idUsager = :idUsager");
        $st -> bindParam(':idUsager', $id);
        $ins = $st -> execute();
        return $ins;
    }

    function insertUsagerAPI ($linkpdo, $data){
        global $linkpdo;
        $st = $linkpdo -> prepare("INSERT INTO Usager (civilite, numSecu, nom, prenom, adresse, cp, 
                                                            cpNaissance, ville, villeNaissance, dateNaissance, idMedecin)
                                    VALUES (:civilite, :numSecu, :nom, :prenom, :adresse, :cp, :cpNaissance, 
                                            :ville, :villeNaissance, :dateNaissance, :idMedecin)");
        $st -> bindParam(':civilite', $data['civilite']);
        $st -> bindParam(':numSecu', $data['num_secu']);
        $st -> bindParam(':nom', $data['nom']);
        $st -> bindParam(':prenom', $data['prenom']);
        $st -> bindParam(':adresse', $data['adresse']);
        $st -> bindParam(':cp', $data['code_postal']);
        $st -> bindParam(':cpNaissance', $data['cpNais']);
        $st -> bindParam(':ville', $data['ville']);
        $st -> bindParam(':villeNaissance', $data['lieu_nais']);
        $st -> bindParam(':dateNaissance', $data['date_nais']);
        $st -> bindParam(':idMedecin', $data['idMedecin']);
            
        $ins = $st -> execute();
        return $ins;
    }

    function updateUsagerAPI ($linkpdo, $data){
        global $linkpdo;
        $st = $linkpdo -> prepare("UPDATE Usager SET civilite = :civilite, numSecu = :numSecu, nom = :nom, 
                                    prenom = :prenom, adresse = :adresse, cp = :cp, cpNaissance = :cpNaissance, 
                                    ville = :ville, villeNaissance = :villeNaissance, dateNaissance = :dateNaissance, 
                                    idMedecin = :idM
                                    where idUsager = :idU");
        $st -> bindParam(':civilite', $cv);
        $st -> bindParam(':numSecu', $numSecu);
        $st -> bindParam(':nom', $nom);
        $st -> bindParam(':prenom', $pnom);
        $st -> bindParam(':adresse', $adr);
        $st -> bindParam(':cp', $cp);
        $st -> bindParam(':cpNaissance', $cpNais);
        $st -> bindParam(':ville', $ville);
        $st -> bindParam(':villeNaissance', $villeNais);
        $st -> bindParam(':dateNaissance', $dateNais);
        $st -> bindParam(':idM', $idMedecin);
        $st -> bindParam(':idU', $idU);

        $ins = $st -> execute();
        return $ins;
    }


    /* App original */
    
    // obtenir le next id d'usager
    function nextValeurUsager(){
        global $linkpdo;
        $req = "SELECT AUTO_INCREMENT
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = 'cabinet'
                AND TABLE_NAME = 'Usager'";
        $res = -1;
        try {
            $result = $linkpdo->query($req);

            if ($result !== false) {
                $row = $result->fetch(PDO::FETCH_ASSOC);

                if ($row !== false) {
                    $res = htmlspecialchars($row["AUTO_INCREMENT"]);
                }
            }
        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
        return $res;
    }

    //*****changer fonctions dans pages affichages
    function getAllUsagers($linkpdo){
        $query = "SELECT * FROM Usager ORDER BY UPPER(nom) asc";
        $res = $linkpdo -> query($query); 
        $arrRes = $res -> fetchAll(PDO::FETCH_ASSOC);
        return $arrRes;
    }

    function getIdUsagerParNom($nom){
        global $linkpdo;
        $res = -1;
        $req = "SELECT idUsager FROM Usager WHERE nom = :nomUsg ORDER BY nom, prenom";
        $result = $linkpdo->prepare($req);
        $result -> bindParam(':nomUsg', $nom);
        $result -> execute();
        if ($result) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $res = htmlspecialchars($row["idUsager"]);
            }
        }
        return $res;
    }

    function getNomUsagerById($idU){
        global $linkpdo;
        $res = "";
        $recherche = "SELECT * FROM Usager WHERE
                        idUsager = :search ORDER BY nom, prenom";
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':search', $idU);
        $st -> execute();

        if ($st !== false) {
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) {
                $res = htmlspecialchars($row["nom"]);
            }
        }
        return $res;
    }

    function getIdMedecinRefByIdUsager($idU){
        global $linkpdo;
        $res = -1;
        $recherche = "SELECT * FROM Usager WHERE
                        idUsager = :search ORDER BY nom, prenom";
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':search', $idU);
        $st -> execute();

        if ($st !== false) {
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) {
                $res = htmlspecialchars($row["idMedecin"]);
            }
        }
        return $res;
    }

    function getUsagerByNom($nom){
        global $linkpdo; 
        $nom = '%'. $nom . '%';
        $recherche = "SELECT * FROM Usager WHERE
                        nom LIKE :search OR 
                        prenom LIKE :search
                        ORDER BY UPPER(nom) asc, prenom";
                        // on fait recherche sur quoi?
                        // -- adresse LIKE :search OR 
                        // -- cp LIKE :search OR 
                        // -- ville LIKE :search OR
                        // -- cpNaissance LIKE :search OR
                        // -- dateNaissance LIKE :search OR
                        // -- villeNaissance LIKE :search"; 
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':search', $nom);
        $st -> execute();
        $arrRes = $st -> fetchAll(PDO::FETCH_ASSOC);
        return $arrRes;
    }

    function getUsagerById($linkpdo, $id){
        $arrRes = "";
        $recherche = "SELECT * FROM Usager WHERE
                        idUsager = :id";
        $st = $linkpdo->prepare($recherche);
        $st -> bindParam(':id', $id);
        $st -> execute();
        $arrRes = $st -> fetch(PDO::FETCH_ASSOC);
        return $arrRes;
    }

    // supprimer les consultation lorsqu'un usager est supprime
    function deleteConsultationByIdUsager($idUsager){
        global $linkpdo;
        $st = $linkpdo -> prepare("DELETE FROM Consulter where idUsager = :idUsager");
        $st -> bindParam(':idUsager',$idUsager);
        $ins = $st-> execute();
        return $ins;
    }

    // supprimer un usager
    // return le resultat d'execution
    function deleteUsagerParId($id){
        deleteConsultationByIdUsager($id);
        global $linkpdo;
        $st = $linkpdo->prepare("DELETE FROM Usager WHERE idUsager = :idUsager");
        $st -> bindParam(':idUsager', $id);
        $ins = $st -> execute();
        return $ins;
    }
    

    // inserer un usager
    // return le resultat d'execution
    function insertUsager ($cv, $numSecu, $nom, $pnom, $adr, $cp, $cpNais, $ville, $villeNais, $dateNais, $idMedecin){
        global $linkpdo;
        $st = $linkpdo -> prepare("INSERT INTO Usager (civilite, numSecu, nom, prenom, adresse, cp, 
                                                            cpNaissance, ville, villeNaissance, dateNaissance, idMedecin)
                                    VALUES (:civilite, :numSecu, :nom, :prenom, :adresse, :cp, :cpNaissance, 
                                            :ville, :villeNaissance, :dateNaissance, :idMedecin)");
        $st -> bindParam(':civilite', $cv);
        $st -> bindParam(':numSecu', $numSecu);
        $st -> bindParam(':nom', $nom);
        $st -> bindParam(':prenom', $pnom);
        $st -> bindParam(':adresse', $adr);
        $st -> bindParam(':cp', $cp);
        $st -> bindParam(':cpNaissance', $cpNais);
        $st -> bindParam(':ville', $ville);
        $st -> bindParam(':villeNaissance', $villeNais);
        $st -> bindParam(':dateNaissance', $dateNais);
        $st -> bindParam(':idMedecin', $idMedecin);
            
        $ins = $st -> execute();
        return $ins;
    }

    // return le resultat d'execution
    // mettre a jour l'usager
    function updateUsager($idU, $cv, $numSecu, $nom, $pnom, $adr, $cp, $cpNais, $ville, $villeNais, $dateNais, $idMedecin){
        global $linkpdo;
        $st = $linkpdo -> prepare("UPDATE Usager SET civilite = :civilite, numSecu = :numSecu, nom = :nom, 
                                    prenom = :prenom, adresse = :adresse, cp = :cp, cpNaissance = :cpNaissance, 
                                    ville = :ville, villeNaissance = :villeNaissance, dateNaissance = :dateNaissance, 
                                    idMedecin = :idM
                                    where idUsager = :idU");
        $st -> bindParam(':civilite', $cv);
        $st -> bindParam(':numSecu', $numSecu);
        $st -> bindParam(':nom', $nom);
        $st -> bindParam(':prenom', $pnom);
        $st -> bindParam(':adresse', $adr);
        $st -> bindParam(':cp', $cp);
        $st -> bindParam(':cpNaissance', $cpNais);
        $st -> bindParam(':ville', $ville);
        $st -> bindParam(':villeNaissance', $villeNais);
        $st -> bindParam(':dateNaissance', $dateNais);
        $st -> bindParam(':idM', $idMedecin);
        $st -> bindParam(':idU', $idU);

        $ins = $st -> execute();
        return $ins;
    }

    function ageUsager($dateNais){
        $dateNais = new DateTime($dateNais);
        $interval = $dateNais->diff(new DateTime);
        return $interval->y;
    }

?>