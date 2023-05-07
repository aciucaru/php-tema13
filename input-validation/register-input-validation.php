<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/input-validation.php';
require_once __DIR__ . '/../model/utilizator.php';

require_once __DIR__ . '/../log/logging.php';

// functie ce valideaza toate input-urile necesare paginii 'regsiter'
function valideazaInputuriRegister(): ?Utilizator
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputuriRegister: inceput rularea");
    $inputurileSuntValide = true;

    // variabile folosite pt. a cosntrui un nou obiect de tip Utilizator
    $id = -1;
    $nume = "";
    $email = "";
    $parolaRegister1 = '';
    $parolaRegister2 = '';
    $departament = new Departament(-1, '');
    $rol = RolUtilizator::Obisnuit;

    $inputuriDeValidat =
    [
        'nume' =>
        [
            'tip' => TipInputUtilizator::Nume,
            'reguliValidare' => new ReguliValidareNume()
        ],

        'email' =>
        [
            'tip' => TipInputUtilizator::Email,
            'reguliValidare' => new ReguliValidare()
        ],

        'parolaRegister1' =>
        [
            'tip' => TipInputUtilizator::ParolaRegister1,
            'reguliValidare' => new ReguliValidareParola()
        ],

        'parolaRegister2' =>
        [
            'tip' => TipInputUtilizator::ParolaRegister2,
            'reguliValidare' => new ReguliValidareParola()
        ]
    ];

    foreach($inputuriDeValidat as $numeInput => $detaliiValidareInput)
    {
        switch($detaliiValidareInput['tip'])
        {
            case TipInputUtilizator::Nume:
                if(valideazaInputNume($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $nume = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputUtilizator::Email:
                if(valideazaInputEmail($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $email = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputUtilizator::ParolaRegister1:
                if(valideazaInputParola($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                {
                    $parolaRegister1 = htmlspecialchars($_POST[$numeInput]);
                    // $hashParola1 = password_hash($parola1, PASSWORD_DEFAULT);
                }
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputUtilizator::ParolaRegister2:
                if(valideazaInputParola($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                {
                    $parolaRegister2 = htmlspecialchars($_POST[$numeInput]);
                    // $hashParola2 = password_hash($parola2, PASSWORD_DEFAULT);
                }
                else
                    $inputurileSuntValide = false;
                break;

            default:
                break;
        }
    }

    if($inputurileSuntValide === true and $parolaRegister1 === $parolaRegister2)
    {
        $logger->log("valideazaInputuriRegister: validare cu succes");

        $hashParola = password_hash($parolaRegister1, PASSWORD_DEFAULT);
        return new Utilizator(
            $id,
            $nume,
            $email,
            $hashParola,
            $departament->nume,
            $rol
        );
    }
    else if($inputurileSuntValide === false)
    {
        $logger->log("valideazaInputuriRegister: validare esuata, input-urile nu sunt valide");
        return null;
    }
    else if($parolaRegister1 !== $parolaRegister2)
    {
        $logger->log("valideazaInputuriRegister: validare esuata, parolele nu sunt identice");
        return null;
    }
}

?>