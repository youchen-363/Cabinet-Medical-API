<?php 

    function getAllConsultations($linkpdo) { 
        $res = $linkpdo->query("SELECT * FROM Consulter ORDER BY date_heure DESC");
        $arrRes = $res -> fetchAll(PDO::FETCH_ASSOC);
        return $arrRes;
    }

    function getConsultationsParMedecin ($linkpdo, $idM) { 
        $st = $linkpdo->prepare("SELECT * FROM Consulter WHERE idMedecin = :idM ORDER BY date_heure DESC");
        $st -> bindParam('idM', $idM);
        $st -> execute();
        $arrRes = $st -> fetchAll(PDO::FETCH_ASSOC);
        return $arrRes;
    }

    // return TRUE if creneau non disjoint, insertion OK
    function verifierNewConsultation($linkpdo, $idM, $date_heure, $duree){
        $dateTIME = (new DateTime($date_heure)) -> format("Y-m-d");
        $st = $linkpdo -> prepare("SELECT * FROM Consulter 
                                WHERE idMedecin = :idMedecin
                                AND DATE(date_heure) = :date_heure"); 
        $st -> bindParam(':idMedecin',$idM);
        $st -> bindParam(':date_heure', $dateTIME);
        $st -> execute();
        $arrRes = $st -> fetchAll(PDO::FETCH_ASSOC);
        foreach ($arrRes as $arr){
            $dhT = $arr['date_heure'];
            $dhAT = ((new DateTime($arr['date_heure'])) -> modify ("+". $arr['Duree'] . "minutes")) ->format('Y-m-d H:i:s');
            $dhA = ((new DateTime($date_heure)) -> modify ("+". $duree . "minutes")) ->format('Y-m-d H:i:s');
            $dh = (new DateTime($date_heure))->format('Y-m-d H:i:s');
            if ($dhA>$dhT && $dhAT>$dh){
                return false;
            }
        }
        return true;
    }

    function verifierMAJConsultation($linkpdo, $idM, $date_heure, $duree, $date_heureOri){
        $dateTIME = (new DateTime($date_heure)) -> format("Y-m-d");
        $st = $linkpdo -> prepare("SELECT * FROM Consulter 
                                WHERE idMedecin = :idMedecin
                                AND DATE(date_heure) = :date_heure"); 
        $st -> bindParam(':idMedecin',$idM);
        $st -> bindParam(':date_heure', $dateTIME);
        $st -> execute();
        // all creneau d'un med 
        $arrRes = $st -> fetchAll(PDO::FETCH_ASSOC);
        foreach ($arrRes as $arr){
            $dhT = $arr['date_heure'];
            $dhAT = ((new DateTime($arr['date_heure'])) -> modify ("+". $arr['Duree'] . "minutes")) ->format('Y-m-d H:i:s');
            $dhA = ((new DateTime($date_heure)) -> modify ("+". $duree . "minutes")) ->format('Y-m-d H:i:s');
            $dh = (new DateTime($date_heure))->format('Y-m-d H:i:s');
            if ($date_heureOri != $arr['date_heure']){
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

    function supprimerConsultation($linkpdo, $idM, $date_heure){
        $st = $linkpdo -> prepare("DELETE FROM Consulter WHERE idMedecin = :idMedecin AND date_heure=:date_heure");
        $st -> bindParam(':idMedecin',$idM);
        $st -> bindParam(':date_heure',$date_heure);
        $ins = $st-> execute();
        return $ins;
    }

    function saisieConsultation($linkpdo, $idM, $date_heure, $idU, $duree){
        $st = $linkpdo -> prepare("INSERT INTO Consulter (idMedecin, date_heure, idUsager, duree) 
                                   VALUES (:idM, :date_heure, :idU, :duree)");
        $st -> bindParam(':idM', $idM);
        $st -> bindParam(':date_heure', $date_heure);
        $st -> bindParam(':idU', $idU);
        $st -> bindParam(':duree', $duree);
        $ins = $st -> execute();
        return $ins;
    }

    function updateConsultation($linkpdo, $idM, $date_heure, $idU, $duree, $idMOri, $date_heureOri){
        $st = $linkpdo -> prepare("UPDATE Consulter SET idMedecin = :idM, date_heure = :date_heure, idUsager = :idU, duree = :duree
                                   WHERE date_heure = :date_heureOri AND idMedecin = :idMOri" );
        $st -> bindParam(':idM', $idM);
        $st -> bindParam(':date_heure', $date_heure);
        $st -> bindParam(':idU', $idU);
        $st -> bindParam(':duree', $duree);
        $st -> bindParam(':idMOri', $idMOri);
        $st -> bindParam(':date_heureOri', $date_heureOri);
        $ins = $st -> execute();
        return $ins;
    }

?>