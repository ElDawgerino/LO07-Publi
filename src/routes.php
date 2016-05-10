<?php
// Routes

require_once 'src/database.php';
require_once 'src/publication.php';
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

    $username = $request_params["username"];
    $password = $request_params["password"];
    $last_name = $request_params["last_name"];
    $first_name = $request_params["first_name"];
    $organisation = $request_params["organisation"];
    $team = $request_params["team"];

    return $response->withJson(user_management::register($username, $password, $last_name, $first_name, $organisation, $team));
});

$app->get('/publi', function($request, $response, $args) {
    //TODO: Liste des publications
});

/**
 * Note : fonctionne différemment des autres routes : pas de Json.
 * Envoie comme réponse le fichier si c'est un succès ou un code d'erreur en cas d'échec
 */
$app->get('/download/{id}', function ($request, $response, $args) {
    $publication_id = $args["id"];

    $publi_file_info = publication::get_publication_file_info($publication_id);
    if($publi_file_info["status"] != "succeed")
    {
        return $response->withStatus(500);
    }

    $file_path = $publi_file_info["path_on_server"];
    $original_name = $publi_file_info["original_name"];

    if(file_exists($file_path))
    {
        $finfo = new finfo(FILEINFO_MIME);

        //Définition des headers
        $response = $response->withHeader('Content-Description', 'File Transfer');
        $response = $response->withHeader('Content-Type', $finfo->file($file_path));
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="'.$original_name.'"');
        $response = $response->withHeader('Content-Transfer-Encoding', 'binary');
        $response = $response->withHeader('Expires', '0');
        $response = $response->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $response = $response->withHeader('Pragma', 'public');
        $response = $response->withHeader('Content-Length', filesize($file_path));

        //Ajout du fichier dans le body
        $responseBody = $response->getBody();
        $handle = fopen($file_path, "r");
        $contents = fread($handle, filesize($file_path));
        $responseBody->write($contents);

        return $response;
    }
    else
    {
        return $response->withStatus(404);
    }
});

$app->get('/publi/{id}', function ($request, $response, $args) {
  //TODO : Obternir les infos sur la publication
});

$app->post('/publi', function ($request, $response, $args) {
    $request_params = $request->getParsedBody();

    $title = $request_params["title"];
    $description = $request_params["description"];
    $status = $request_params["status"];
    $publication_title = (isset($request_params["publication_title"]) ? $request_params["publication_title"] : null);
    $publication_year = (isset($request_params["publication_year"]) ? $request_params["publication_year"] : null);
    $conference_location = (isset($request_params["conference_location"]) ? $request_params["conference_location"] : null);
    $file_info = $request_params["file"];

    return $response->withJson(publication::add_publication($title, $description, $status, $publication_title, $publication_year, $conference_location, $file_info));
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

$app->get('/comptes', function ($request, $response, $args) {
  //TODO : vérifier que l'utilisateur est admin
  return $response->withJson(user_management::get_users());
});

$app->get('/compte', function ($request, $response, $args) {
    //TODO : vérifier que l'utilisateur est admin
    $current_user_id = user_management::get_current_logged_user();
    if($current_user_id["status"] == "succeed")
    {
        return $response->withJson(
            user_management::get_user($current_user_id['id'])
        );
    }
    else
    {
        return $response->withJson([
            "status" => "invalid"
        ]);
    }
});

$app->get('/compte/{id}', function ($request, $response, $args) {
  //TODO : vérifier que l'utilisateur est admin
    return $response->withJson(user_management::get_user($args['id']));
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
