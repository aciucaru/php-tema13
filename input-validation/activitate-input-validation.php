<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/input-validation.php';
require_once __DIR__ . '/../model/utilizator.php';
require_once __DIR__ . '/../model/departament.php';

require_once __DIR__ . '/../log/logging.php';

// functie ce valideaza toate input-urile necesare paginii 'login'
function valideazaInputuriLogin(): bool
{
    static $logger = new Logger(__FILE__);
    $logger->log("valideazaInputuriLogin: inceput rularea");

    $email = "";
    $hashParola = "";

    $inputurileSuntValide = true;

    $inputuriDeValidat =
    [
        'categorie' =>
        [
            'tip' => TipInputUtilizator::Email,
            'reguliValidare' => new ReguliValidare()
        ],

        'ore' =>
        [
            'tip' => TipInputUtilizator::ParolaLogin,
            'reguliValidare' => new ReguliValidareParola()
        ],

        'descriere' =>
        [
            'tip' => TipInputUtilizator::ParolaLogin,
            'reguliValidare' => new ReguliValidareParola()
        ]
    ];

    foreach($inputuriDeValidat as $numeInput => $detaliiValidareInput)
    {
        switch($detaliiValidareInput['tip'])
        {
            case TipInputUtilizator::Email:
                if(valideazaInputEmail($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                    $email = htmlspecialchars($_POST[$numeInput]);
                else
                    $inputurileSuntValide = false;
                break;

            case TipInputUtilizator::ParolaLogin:
                if(valideazaInputParola($numeInput, $detaliiValidareInput['reguliValidare']) === true)
                {
                    $parola = htmlspecialchars($_POST[$numeInput]);
                    $hashParola = password_hash($parola, PASSWORD_DEFAULT);
                }
                else
                    $inputurileSuntValide = false;
                break;

            default:
                break;
        }
    }

    if($inputurileSuntValide === true)
    {
        $logger->log("valideazaInputuriLogin: validare cu succes");
        return true;
    }

    else
    {
        $logger->log("valideazaInputuriLogin: validare esuata");
        return true;
    }
}

?>