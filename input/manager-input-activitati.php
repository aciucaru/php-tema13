<?php

require_once __DIR__ . '/../model/activitate.php';
require_once __DIR__ . '/../repo/activitate.repo.php';
require_once __DIR__ . '/../service/manager-sesiune.php';

class ManagerInputActivitati
{
    private static $instanta = null;

    private static $logger;

    private function __construct()
    {
        if(self::$logger == null)
            self::$logger = new Logger(__FILE__);
    }

    public static function iaSingleton(): ManagerInputActivitati
    {
        if(self::$instanta == null)
            self::$instanta = new ManagerInputActivitati();

        session_start(); // aceasta metoda porneste si sesiunea
        return self::$instanta;
    }

    public function adaugaActivitate()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['submit-activitate']))
        {
            $managerSesiune = ManagerSesiune::iaSingleton();
            $repoActivitati = new ActivitateRepo();

            $emailUtilizator = $managerSesiune->iaEmailUtilizator();
            $dataTimp = new DateTime();
            $departamentUtilizator = $managerSesiune->iaNumeDepartamentUtilizator();
            $inputCategorie = htmlspecialchars($_POST['categorie']);
            $inputOre = htmlspecialchars($_POST['ore']);
            $inputDescriere = htmlspecialchars($_POST['descriere']);
            self::$logger->log("ManagerInputActivitati::adaugaActivitate: DEBUG: descriere: $inputDescriere");

            $activitate = new Activitate(
                                            -1,
                                            $emailUtilizator,
                                            $dataTimp,
                                            $departamentUtilizator,
                                            $inputCategorie,
                                            $inputOre,
                                            $inputDescriere
                                        );

            $repoActivitati->adaugaActivitate($activitate);
            
            // se redirectioneaza catre aceeasi pagina pt. a vedea imediat modificarea
            header('Location:activitati.php');
        }
        else
            self::$logger->log('ManagerSesiune::incearcaLogin: nu s-a facut POST sau input-ul submit lipseste');
    }
}

?>