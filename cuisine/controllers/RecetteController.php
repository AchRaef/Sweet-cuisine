<?php

namespace app\controllers;

use app\models\Recette;
use app\router;

/**
 * Class recetteController
 * @package app\controllers
 */
class recetteController
{
  static public function u_recette(router $router)
  {

    $search = $_GET['search'] ?? '';
    $page = $_GET['page'] ?? 1;
    $size = 20;
    $startrow = (($page - 1) * $size) + 1;
    $endrow = $page * $size;
    $recettes = $router->database->getrecettelimited($search, $startrow, $endrow);
    foreach ($recettes as $recette) {
    }
    $pr_nr = $recette['TotalNb'] ?? $size;
    $pages = ceil($pr_nr / $size);

    $router->renderView('/utilisateur/u_recette', [
      'search' => $search,
      'products' => $recettes,
      'pages' => $pages,
      'page' => $page,
    ]);
  }

  static public function recette(router $router)
  {

    $search = $_GET['search'] ?? '';
    $page = $_GET['page'] ?? 1;
    $size = 5;
    $startrow = (($page - 1) * $size) + 1;
    $endrow = $page * $size;
    $recettes = $router->database->getrecettelimited($search, $startrow, $endrow);
    foreach ($recettes as $recette) {
    }
    $pr_nr = $recette['TotalNb'] ?? $size;
    $pages = ceil($pr_nr / $size);
    $router->renderView('/recette', [
      'search' => $search,
      'products' => $recettes,
      'pages' => $pages
    ]);
  }

  static public function details(router $router)
  {

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location :/recette_liste');
      exit;
    }
    $recetteData = $router->database->getRecetteById($id);
    $search = $_GET['search'];
    $page = $_GET['page'] ?? 1;
    $size = 100;
    $startrow = (($page - 1) * $size) + 1;
    $endrow = $page * $size;
    $recettes = $router->database->getRecetteLimited($search, $startrow, $endrow);
    shuffle($recettes);
    $router->renderView('/recette_details', [
      'recettedata' => $recetteData,
      'search' => $search,
      'recettes' => $recettes,
    ]);
  }

  static public function create(router $router)
  {
    $errors = [];
    $recetteData = [
      'nom' => '',
      'ingred' => '',
      'etapes' => '',
      'duree' => '',
      'img' => '',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $recetteData['nom'] = $_POST['nom'] ?? null;
      $recetteData['ingred'] = $_POST['ingred'] ?? null;
      $recetteData['etapes'] = $_POST['etapes'];
      $recetteData['duree'] = $_POST['duree'];
      $recetteData['img'] = $_FILES['image'] ?? null;
      
      $utilisateur = $router->database->getutilisateur($recetteData['proprietaire']) ;
      $recette = new recette();
      $recette->load($recetteData);
      $errors = $recette->save();
      if (empty($errors)) {
        header('Location: /utilisateur/u_recette');
        exit;
      }
    }
    $router->renderView('admin/recette/formul_recette', [
      'errors' => $errors,
      'recette' => $recetteData,
      'utilisateur' => $utilisateur,

    ]);
  }

  static public function update(router $router)
  {

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location :/u_recette');
      exit;
    }
    $errors = [];
    $recetteData = $router->database->getrecetteById($id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $recetteData['nom'] = $_POST['nom'];
      $recetteData['ingred'] = $_POST['ingred'];
      $recetteData['etapes'] = $_POST['etapes'];
      $recetteData['duree'] = $_POST['duree'];
      $recetteData['img'] = $_FILES['image'] ?? null;
      $recette = new recette();
      $recette->load($recetteData);
      $errors = $recette->save();
      if (empty($errors)) {
        header('Location: /u_recette');
        exit;
      }
    }
    $router->renderView('utilisateur/recette/modifier', [
      'errors' => $errors,
      'recette' => $recetteData,
    ]);
  }

  static public function delete(router $router)
  {
    session_start();

    $id = $_POST['id'] ?? null;
    if (!$id) {
      header('Location: /utilisateur/recette');
      exit;
    }
    $router->database->deleterecette($id);
    header('Location: /utilisateur/recette');
    exit;
  }
}
