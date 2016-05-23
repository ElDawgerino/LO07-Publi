var app = new angular.module('publi-module', []);

app.factory('publi', [
  '$http',
  function($http){
    var publi = {};

    publi.getAll =function(then){
        $http.get('publi').then(function(response){
            then({success: true, content: response.data});
        }, function(response){
            then({success: false, error: "Erreur inconnue"});
        });
    };

    publi.get = function(id, then){
        $http.get('publi/' + id).then(function(response){
                then({success: true, content: response.data.publication});
            }, function(response){
                if(response.status === 404) {
                    then({success: false, error: "Publication n°" + id + " introuvable !"});
                }
                else {
                    then({success: false, error: "Erreur inconnue"});
                }
            }
        );
    };

    publi.post = function(content, then){
        $http.post('publi', content).then(function(response){
                then({success: true, id: response.data.id});
            }, function(response){
                if(response.status === 401){
                    then({success: false, error: "Il faut être connecté pour ajouter une nouvelle publication !"});
                }
                else{
                    then({success: false, error: "Impossible d'ajouter la publication : erreur inconnue"});
                }
            }
        );
    };

    publi.delete = function(id, then){

    };

    publi.put = function(id, content, then){

    };

    publi.search = function(params, then){

    };

    publi.getAuteurs = function(then){
        $http.get('auteurs').then(function(response){
                then({success: true, content: response.data.auteurs});
            }, function(response){
                then({success: false, error: "Erreur inconnue"});
            }
        );
    };

    publi.getJournaux = function(then){
        $http.get('journaux').then(function(response){
                then({success: true, content: response.data.journaux});
            }, function(response){
                then({success: false, error: "Erreur inconnue"});
            }
        );
    };

    publi.getConferences = function(then){
        $http.get('conferences').then(function(response){
                then({success: true, content: response.data.conferences});
            }, function(response){
                then({success: false, error: "Erreur inconnue"});
            }
        );
    };

    publi.getAuteur = function(id, then){
        $http.get('auteur/' + id).then(function(response){
                then({success: true, auteur: response.data.auteur, publis: response.data.publis});
            }, function(response){
                if(response.status === 404){
                    then({success: false, error: "L'auteur n°" + id + " n'existe pas !"});
                }
                else{
                    then({success: false, error: "Erreur inconnue"});
                }
            }
        );
    };

    return publi;
}]);
