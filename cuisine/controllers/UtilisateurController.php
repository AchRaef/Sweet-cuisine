<?php

namespace app\controllers;

use app\models\Utilisateur;
use app\router;

/**
 * Class utilisateurController
 * @package app\controllers
 */
class UtilisateurController
{
  static public function create(router $router)
  {
    session_start();

    $errors = [];
    $utilisateurData = [
      'utili_nom' => '',
      'password' => '',
      'email' => ''
    ];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $utilisateurData['utili_nom'] = $_POST['utili_nom'];
      $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $utilisateurData['password'] = $hashed_password;
      $utilisateurData['email'] = $_POST['email'] ?? null;

      $utilisateur = new Utilisateur();
      $utilisateur->load($utilisateurData);
      $errors = $utilisateur->save();
    }
    $router->renderView('utilisateur/inscrire', [
      'errors' => $errors,
      'utilisateur' => $utilisateurData,
    ]);
  }

  static public function update(router $router)
  {
    session_start();

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location :/utilisateur/gerer/u_profile');
      exit;
    }
    $errors = [];
    $utilisateurData = $router->database->getUtilisateurById($id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $utilisateurData['utili_nom'] = $_POST['utili_nom'];
      $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $utilisateurData['password'] = $hashed_password;
      $utilisateurData['email'] = $_POST['email'];

      $utilisateur = new utilisateur();
      $utilisateur->load($utilisateur);
      $errors = $utilisateur->save();
    }
    $router->renderView('utilisateur/recette/u_recette', [
      'errors' => $errors,
      'utilisateur' => $utilisateurData,
    ]);
  }
}
