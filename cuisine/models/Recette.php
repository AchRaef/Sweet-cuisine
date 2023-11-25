<?php

namespace app\models;


use app\database;
use app\helpers\utilHelper;

/**
 * Class recette
 * @package app\models
 */
class recette
{
    public ?int $id = null;
    public ?string $nom = null;
    public ?string $ingred = null;
    public ?string $etapes = null;
    public ?string $duree = null;
    public ?array $imageFile = null;
    public ?string $imgPath = null;
    public ?int $proprietaire = null;

    // Load Data
    public function load($data)
    {
        $this->id = $data['recette_id'] ?? null;
        $this->nom = $data['nom'] ?? null;
        $this->ingred = $data['ingred'] ?? null;
        $this->etapes = $data['etapes'];
        $this->duree = $data['duree'] ?? null;
        $this->imageFile = $data['imageFile'] ?? null;
        $this->imgPath = $data['img'] ?? null;
        $this->proprietaire = $data['proprietaire'];
    }

    // Save Data
    public function save()
    {
        $errors = [];
        if (!is_dir(__DIR__ . '/../public/images/recette')) {
            mkdir(__DIR__ . '/../public/images/recette');
        }
        elseif (!$this->nom) {
            $errors[] = 'Le Nom du recette est obligatoire !';
        }
        elseif (!$this->ingred) {
            $errors[] = 'veuillez inserer les ingredients du recette !';
        }
        elseif (!$this->etapes) {
            $errors[] = 'Ingredients sans étapes bizare !';
        }
        elseif (!$this->duree) {
            $errors[] = 'va rendre boucoup de temp ou vite fait donner moi une durée ?';
        }
        if (empty($errors)) {
            if ($this->imageFile && $this->imageFile['tmp_name']) {
                if ($this->imgPath) {
                    unlink(__DIR__ . '/../public/' . $this->imgPath);
                }
                $this->imgPath = 'images/recette/' . utilHelper::randomString(8) . '/' . $this->imageFile['name'];
                mkdir(dirname(__DIR__ . '/../public/' . $this->imgPath));
                move_uploaded_file($this->imageFile['tmp_name'], __DIR__ . '/../public/' . $this->imgPath);
            }
            $database = database::$database;
            if ($this->id) {
                $database->updaterecette($this);
            } else {
                $database->createrecette($this);
            }
        }
        return $errors;
    }
}
