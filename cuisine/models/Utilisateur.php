<?php

namespace app\models;

use app\database;

class utilisateur
{
  public ?int $id = null;
  public ?string $utili_nom = null;
  public ?string $password = null;
  public ?string $email = null;

  // Load Data
  public function load($data)
  {
    $this->id = $data['id'] ?? null;
    $this->utili_nom = $data['utili_nom'] ?? null;
    $this->password = $data['password'] ?? null;
    $this->email = $data['email'] ?? null;
  }
// connect
  public function connec($email, $pass)
  {
    $errors = [];
    if (!$this->email) {
      $errors[] = 'Inserer votre adresse Email !';
    } elseif (!$this->password) {
      $errors[] = 'Le mot de passe est obligatoire !';
    } elseif (($this->email != $email && $this->password != $pass)) {
      $errors[] = "Les informations d'identification invalides !<br> Veuillez réessayer.";
    }

    return $errors;
  }

  // Save Data
  public function save()
  {
    $errors = [];
    if (!$this->utili_nom) {
      $errors[] = 'Le nom d\'utilisateur est obligatoire !';
    }
    elseif (!$this->password) {
      $errors[] = 'Le mot de passe est obligatoire !';
    }
    elseif (!$this->email) {
        $errors[] = 'L\'Adresse Email est obligatoire !';
      }

    if (empty($errors)) {
      $database = database::$database;
      if ($this->id) {
        $database->createUtilisateur($this);
      }
    }
    return $errors;
  }
}
