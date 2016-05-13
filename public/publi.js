var app = new angular.module('publi-module', []);

app.factory('publi', [
  '$http',
  function($http){
    var publi = {};

    publi.getAll =function(then){

    };

    publi.get = function(id, then){

    };

    publi.post = function(content, then){
      postContent = {
        title: content.title,
        description: content.description,
        status: content.status,
        file: content.file
      };

      $http.post('publi', postContent).then(function(response){
        if(response.data.status == "unauthorized"){
          then({success: false, error: "Veuillez vous connecter."});
        }
        else if(response.data.status == "db_error"){
          then({success: false, error: "Impossible de se connecter à la base de donnée."});
        }
        else if(response.data.status == "insertion_error"){
          then({success: false, error: "Impossible d'insérer le fichier."})
        }
        else if(response.data.status == "succeed"){
          then({success: true, id: response.data.id});
        }
      }, function(response){
        then({success: false, error: "Erreur inconnue"});
      });
    };

    publi.download = function(id, then){

    };

    publi.delete = function(id, then){

    };

    publi.put = function(id, content, then){

    };

    publi.search = function(params, then){

    };

    return publi;
}]);
