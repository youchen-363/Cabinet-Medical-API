<?php 

    function getAllConsultations($linkpdo) { 
        $res = $linkpdo->query("SELECT * FROM Consulter ORDER BY DateHeure DESC");
        $arrRes = $res -> fetchAll(PDO::FETCH_ASSOC);
        return $arrRes;
    }

    function getConsultationsParMedecin ($idM) { 
        global $linkpdo;
        $st = $linkpdo->prepare("SELECT * FROM Consulter WHERE idMedecin = :idM ORDER BY DateHeure DESC");
        $st -> bindParam('idM', $idM);
        $st -> execute();
        $arrRes = $st -> fetchAll(PDO::FETCH_ASSOC);
        return $arrRes;
    }

    // return TRUE if creneau non disjoint, insertion OK
    function verifierNewConsultation($idM, $dateHeure, $duree){
        global $linkpdo;
        $dateTIME = (new DateTime($dateHeure)) -> format("Y-m-d");
        $st = $linkpdo -> prepare("SELECT * FROM Consulter 
                                WHERE idMedecin = :idMedecin
                                AND DATE(DateHeure) = :dateHeure"); 
        $st -> bindParam(':idMedecin',$idM);
        $st -> bindParam(':dateHeure', $dateTIME);
        $st -> execute();
        $arrRes = $st -> fetchAll(PDO::FETCH_ASSOC);
        foreach ($arrRes as $arr){
            $dhT = $arr['DateHeure'];
            $dhAT = ((new DateTime($arr['DateHeure'])) -> modify ("+". $arr['Duree'] . "minutes")) ->format('Y-m-d H:i:s');
            $dhA = ((new DateTime($dateHeure)) -> modify ("+". $duree . "minutes")) ->format('Y-m-d H:i:s');
            $dh = (new DateTime($dateHeure))->format('Y-m-d H:i:s');
            if ($dhA>$dhT && $dhAT>$dh){
                return false;
            }
        }
        return true;
    }

    function verifierMAJConsultation($idM, $dateHeure, $duree, $dateHeureOri){
        global $linkpdo;
        $dateTIME = (new DateTime($dateHeure)) -> format("Y-m-d");
        $st = $linkpdo -> prepare("SELECT * FROM Consulter 
                                WHERE idMedecin = :idMedecin
                                AND DATE(DateHeure) = :dateHeure"); 
        $st -> bindParam(':idMedecin',$idM);
        $st -> bindParam(':dateHeure', $dateTIME);
        $st -> execute();
        // all creneau d'un med 
        $arrRes = $st -> fetchAll(PDO::FETCH_ASSOC);
        foreach ($arrRes as $arr){
            $dhT = $arr['DateHeure'];
            $dhAT = ((new DateTime($arr['DateHeure'])) -> modify ("+". $arr['Duree'] . "minutes")) ->format('Y-m-d H:i:s');
            $dhA = ((new DateTime($dateHeure)) -> modify ("+". $duree . "minutes")) ->format('Y-m-d H:i:s');
            $dh = (new DateTime($dateHeure))->format('Y-m-d H:i:s');
            if ($dateHeureOri != $arr['DateHeure']){
                if ($dhA>$dhT && $dhAT>$dh){
                    return false;
                }
            }
        }
        return true;
    }

    function verifierDuree ($duree){
        return $duree >= 0.5 && $duree <=3;
    }

    function supprimerConsultation($idM, $dateHeure){
        global $linkpdo;
        $st = $linkpdo -> prepare("DELETE FROM Consulter WHERE idMedecin = :idMedecin AND dateHeure=:dateHeure");
        $st -> bindParam(':idMedecin',$idM);
        $st -> bindParam(':dateHeure',$dateHeure);
        $ins = $st-> execute();
        return $ins;
    }

    function saisieConsultation($idM, $dateHeure, $idU, $duree){
        global $linkpdo;
        $st = $linkpdo -> prepare("INSERT INTO Consulter (idMedecin, dateHeure, idUsager, duree) 
                                   VALUES (:idM, :dateheure, :idU, :duree)");
        $st -> bindParam(':idM', $idM);
        $st -> bindParam(':dateheure', $dateHeure);
        $st -> bindParam(':idU', $idU);
        $st -> bindParam(':duree', $duree);
        $ins = $st -> execute();
        return $ins;
    }

    function updateConsultation($idM, $dateHeure, $idU, $duree, $idMOri, $dateheureOri){
        global $linkpdo;
        $st = $linkpdo -> prepare("UPDATE Consulter SET idMedecin = :idM, dateHeure = :dateheure, idUsager = :idU, duree = :duree
                                   WHERE dateheure = :dateHeureOri AND idMedecin = :idMOri" );
        $st -> bindParam(':idM', $idM);
        $st -> bindParam(':dateheure', $dateHeure);
        $st -> bindParam(':idU', $idU);
        $st -> bindParam(':duree', $duree);
        $st -> bindParam(':idMOri', $idMOri);
        $st -> bindParam(':dateHeureOri', $dateheureOri);
        $ins = $st -> execute();
        return $ins;
    }

?>