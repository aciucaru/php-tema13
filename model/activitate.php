<?php

class Activitate
{
    private int $id = -1;
    public string $departament = '';
    public string $emailUtilizator = '';
    public DateTime $dataTimp;
    public string $categorie = '';
    public int $ore = 0;
    public string $descriere = '';

    public function __construct(
        int $id,
        string $emailUtilizator,
        DateTime $dataTimp,
        string $departament,
        string $categorie,
        float $ore,
        string $descriere
    )
    {
        $this->id = $id;

        if(isset($emailUtilizator))
            $this->emailUtilizator = $emailUtilizator;

        if(isset($dataTimp))
            $this->dataTimp = $dataTimp;

        if(isset($departament))
            $this->departament = $departament;

        if(isset($categorie))
            $this->categorie = $categorie;

        $this->ore = $ore;

        if(isset($descriere))
            $this->descriere = $descriere;
    }

    public function iaId(): int
    {
        return $this->id;
    }

    public function __toString()
    {
        return "{ 
                    id: $this->id, email: $this->emailUtilizator, departament: $this->departament, categorie: $this->categorie, ore: $this->ore
                    descriere: $this->descriere
                }";
    }

}

?>