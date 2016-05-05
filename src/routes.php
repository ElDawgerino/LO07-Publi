<?php
// Routes

require_once 'src/database.php';
require_once 'src/user.php';

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("'/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->post('/login', function ($request, $response, $args) {
    $request_params = $request->getParsedBody();

    $username = $request_params["username"];
    $password = $request_params["password"];

    return $response->withJson(user_management::login($username, $password));
});

$app->post('/logout', function ($request, $response, $args) {
    $request_params = $request->getParsedBody();

    return $response->withJson(user_management::logout());
});

$app->post('/register', function ($request, $response, $args) {
  $request_params = $request->getParsedBody();

  $user["username"] = $request_params["username"];
  $user["password"] = $request_params["password"];
  $user["prenom"] = $request_params["prenom"];
  $user["nom"] = $request_params["nom"];
  $user["organisation"] = $request_params["organisation"];
  $user["equipe"] = $request_params["equipe"];

  return $response->withJson(user_management::register($user));
});

$app->get('/publi', function($request, $response, $args) {
  //TODO : Obtenir la liste des publis
});

$app->get('/publi/{id}', function ($request, $response, $args) {
  //TODO : Obtenir le document lié à la publication
});

$app->get('/publi/{id}/infos', function ($request, $response, $args) {
  //TODO : Obternir les infos sur la publication
});

$app->post('/publi', function ($request, $response, $args) {
  //TODO : poster une publi
});

$app->put('/publi/{id}', function ($request, $response, $args) {
  //TODO : mettre à jour une publi
});

$app->delete('/publi/{id}', function ($request, $response, $args) {
  //TODO : supprimer une publi
});

$app->post('/recherche', function ($request, $response, $args) {
  //TODO : Obtenir la liste des publis suivant les critères de recherches transférer par le client dans un objet
  //Alternative : le faire entièrement côté client
});

$app->get('/compte', function ($request, $response, $args) {
  //TODO : vérifier que l'utilisateur est admin
  $request_params = $request->getParsedBody();
  return $response->withJson(user_management::getComptes());
});

$app->get('/compte/{id}', function ($request, $response, $args) {
  //TODO : Visualiser un compte spécifique
});

$app->delete('/compte/{id}', function ($request, $response, $args) {
  //TODO : supprimer un compte
});

$app->get('/anomalies', function ($request, $response, $args) {
  //TODO : envoyer les anomalies
});

$app->get('/stats', function ($request, $response, $args) {
  //TODO : envoyer les stats
});

?>
