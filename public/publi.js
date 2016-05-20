var app = new angular.module('publi-module', []);

app.factory('publi', [
  '$http',
  function($http){
    var publi = {};

    publi.getAll =function(then){
      $http.get('publi').then(function(response){
        if(response.data.status == "db_error"){
          then({success: false, error: "Impossible de se connecter à la base de donnée."});
        } else if(response.data.status == "empty"){
          then({success: false, message: "empty"});
        }
        else{
          then({success: true, content: response.data});
        }
      }, function(response){
        then({success: false, error: "Erreur inconnue"});
      });
    };

    publi.get = function(id, then){
      $http.get('publi/' + id).then(function(response){
        if(response.data.status == "db_error"){
          then({success: false, error: "Impossible de se connecter à la base de donnée."});
        } else if(response.data.status == "invalid"){
          then({success: false, error: "Publication introuvable"})
        } else if(response.data.status == "succeed"){
          then({success: true, content: response.data.publication});
        } else {
          then({success: false, error: "Erreur inconnue"});
        }
      }, function(response){
        then({success: false, error: "Erreur inconnue"});
      });
    };

    publi.post = function(content, then){
      $http.post('publi', content).then(function(response){
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

    publi.getAuteurs = function(then){
      $http.get('auteurs', function(response){
        if(response.data.status == "db_error"){
          then({success: false, error: "Impossible de se connecter à la base de donnée."});
        } else if(response.data.status == "empty"){
          then({success: false, message: "empty"});
        }
        else{
          then({success: true, content: response.data.auteurs});
        }
      }, function(response){
        then({success: false, error: "Erreur inconnue"});
      });
    };

    publi.getJournaux = function(then){
      $http.get('journaux', function(response){
        if(response.data.status == "db_error"){
          then({success: false, error: "Impossible de se connecter à la base de donnée."});
        } else if(response.data.status == "empty"){
          then({success: false, message: "empty"});
        }
        else{
          then({success: true, content: response.data.journaux});
        }
      }, function(response){
        then({success: false, error: "Erreur inconnue"});
      });
    };

    publi.getConferences = function(then){
      $http.get('conferences', function(response){
        if(response.data.status == "db_error"){
          then({success: false, error: "Impossible de se connecter à la base de donnée."});
        } else if(response.data.status == "empty"){
          then({success: false, message: "empty"});
        }
        else{
          then({success: true, content: response.data.conferences});
        }
      }, function(response){
        then({success: false, error: "Erreur inconnue"});
      });
    };

    return publi;
}]);
