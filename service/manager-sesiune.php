<?php

require_once __DIR__ . '/../model/utilizator.php';
require_once __DIR__ . '/../input-validation/register-input-validation.php';
require_once __DIR__ . '/../model/utilizator.php';
require_once __DIR__ . '/../log/logging.php';

class ManagerSesiune
{
    private static $instanta = null;

    private static $logger;

    private function __construct()
    {
        if(self::$logger == null)
            self::$logger = new Logger(__FILE__);
    }

    public static function iaSingleton(): ManagerSesiune
    {
        if(self::$instanta == null)
            self::$instanta = new ManagerSesiune();

        session_start(); // aceasta metoda porneste si sesiunea
        return self::$instanta;
    }

    public function incearcaLogin()
    {
        self::$logger->log('ManagerSesiune::incearcaLogin: inceput rularea');

        if($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['submit-login']))
        {
            // functia de validare returneaza adevarat daca validarea a avut succes, altfel returneaza fals
            $inputuriSuntValide = valideazaInputuriLogin();

            $utilizatorLogat = new Utilizator(-1, '', '', '', '', RolUtilizator::Obisnuit);

            // daca validarea a avut succes
            if($inputuriSuntValide === true)
            {
                // atunci se verifica daca exista cu adevarat in baza de date un utilizator cu acelasi username si parola
                // $inputUsername = $_POST['username'];
                $inputEmail = htmlspecialchars($_POST['email']);
                self::$logger->log("ManagerSesiune::incearcaLogin: input username: $inputEmail");

                // $inputParola = password_hash($_POST['parola'], PASSWORD_DEFAULT);
                $inputParola = htmlspecialchars($_POST['parolaLogin']);

                $repoUtilizator = new UtilizatorRepo();
                $utilizatorLogat = $repoUtilizator->verificaUtilizator($inputEmail, $inputParola);

                if($utilizatorLogat != null)
                {
                    $_SESSION['email_utilizator'] = $utilizatorLogat->email;
                    // EROARE: 'nume:' in loc de 'departament'
                    $_SESSION['departament_utilizator'] = $utilizatorLogat->departament;
                    self::$logger->log("ManagerSesiune::incearcaLogin: DEBUG: departament_utilizator: $utilizatorLogat->departament");
                    $_SESSION['rol_utilizator'] = $utilizatorLogat->rol->value;

                    $rolUtilizator = $utilizatorLogat->rol->value;

                    self::$logger->log("ManagerSesiune::incearcaLogin: login-ul a avut succes, rol: $rolUtilizator");
                    header('Location:index.php');
                }
                else
                    self::$logger->log('ManagerSesiune::incearcaLogin: login-ul nu a avut succes');
            }
            else
                self::$logger->log('ManagerSesiune::incearcaLogin: validarea input-urilor de login nu a avut succes');
        }
        else
            self::$logger->log('ManagerSesiune::incearcaLogin: nu s-a facut POST sau input-ul submit lipseste');
    }

    public function incearcaRegister()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['submit-register']))
        {
            // functia de validare returneaza un obiect de tip Client daca validarea a avut succes, atlfel returneaza nul
            $clientNou = valideazaInputuriRegister();

            // daca validarea a avut succes
            if($clientNou != null)
            {
                // atunci se verifica daca mai exista deja in baza de date un utilizator cu acelasi email sau username
                $repoUtilizator = new UtilizatorRepo();
                if($repoUtilizator->contineEmail($clientNou->email) === false)
                    $repoUtilizator->adaugaUtilizator($clientNou);
                else
                    self::$logger->log('ManagerSesiune::incearcaRegister: username-ul sau email-ul exista deja in baza de date');
            }
            else
                self::$logger->log('ManagerSesiune::incearcaRegister: client este nul, validarea nu a avut succes');
        }
        else
            self::$logger->log('ManagerSesiune::incearcaRegister: nu s-a facut POST sau input-ul submit lipseste');
    }

    public function logoutSesiune()
    {
        self::$logger->log('ManagerSesiune::logoutSesiune: inceput rularea');
    
        session_unset();
        session_destroy();
    
        header('Location:login.php');
    }

    public function existaUtilizatorLogat(): bool
    {
        return isset($_SESSION['email_utilizator']) and !empty($_SESSION['email_utilizator']);
    }

    public function iaEmailUtilizator(): ?string
    {
        self::$logger->log('ManagerSesiune::iaEmailUtilizator: inceput rularea');

        if( isset($_SESSION['email_utilizator']) and !empty($_SESSION['email_utilizator']) )
            return $_SESSION['email_utilizator'];
        else
            return null;
    }

    public function iaRolUtilizator(): RolUtilizator
    {
        self::$logger->log('ManagerSesiune::iaRolUtilizator: inceput rularea');

        if( isset($_SESSION['rol_utilizator']) and !empty($_SESSION['rol_utilizator']) )
        {
            if($_SESSION['rol_utilizator'] === 'Obisnuit')
                return RolUtilizator::Obisnuit;

            if($_SESSION['rol_utilizator'] === 'Admin')
                return RolUtilizator::Admin;
        }
        else
            return RolUtilizator::Obisnuit;
    }

    public function iaNumeDepartamentUtilizator(): string
    {
        self::$logger->log('ManagerSesiune::iaNumeDepartamentUtilizator: inceput rularea');

        $numeDepartament = '';

        if(isset($_SESSION['departament_utilizator']) and !empty($_SESSION['departament_utilizator']))
            $numeDepartament = $_SESSION['departament_utilizator'];
        else
            self::$logger->log('ManagerSesiune::iaNumeDepartamentUtilizator: EROARE: departament_utilizator nu exista sau este gol');

        return $numeDepartament;
    }
}

?>