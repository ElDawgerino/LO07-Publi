var app = new angular.module('publi-module', []);

app.factory('publi', [
  '$http',
  function($http){
    var publi = {};

    publi.getAll =function(then){
        $http.get('index.php/publi').then(function(response){
            then({success: true, content: response.data});
        }, function(response){
            if(response.status === 500){
                then({success: false, error: "Erreur interne au serveur"});
            }
            else {
                then({success: false, error: "Erreur inconnue"});
            }
        });
    };

    publi.get = function(id, then){
        $http.get('index.php/publi/' + id).then(function(response){
                then({success: true, content: response.data.publication});
            }, function(response){
                if(response.status === 404) {
                    then({success: false, error: "Publication n°" + id + " introuvable !"});
                }
                else if(response.status === 500){
                    then({success: false, error: "Erreur interne au serveur"});
                }
                else {
                    then({success: false, error: "Erreur inconnue"});
                }
            }
        );
    };

    publi.post = function(content, then){
        $http.post('index.php/publi', content).then(function(response){
                then({success: true, id: response.data.id});
            }, function(response){
                if(response.status === 401){
                    then({success: false, error: "Il faut être connecté pour ajouter une nouvelle publication !"});
                }
                else if(response.status === 500){
                    then({success: false, error: "Erreur interne au serveur"});
                }
                else{
                    then({success: false, error: "Impossible d'ajouter la publication : erreur inconnue"});
                }
            }
        );
    };

    publi.delete = function(id, then){
        $http.delete('index.php/publi/' + id).then(function(respone){
            then({success: true})
        }, function(response){
            if(response.status === 500){
                then({success: false, error: "Erreur interne au serveur"});
            } else if(response.status === 401){
                then({success: false, error: "Vous n'êtes pas autorisé à mettre à jour cette publication !"});
            } else {
                then({success: false, error: "Erreur inconnue"});
            }
        });
    };

    publi.put = function(id, content, then){
        $http.put('index.php/publi/' + id, content).then(function(response){
            then({success: true, id: response.data.id});
        }, function(response){
            if(response.status === 401){
                then({success: false, error: "Vous n'êtes pas autorisé à mettre à jour cette publication !"});
            }
            else if(response.status === 500){
                then({success: false, error: "Erreur interne au serveur"});
            }
            else{
                then({success: false, error: "Impossible d'ajouter la publication : erreur inconnue"});
            }
        });
    };

    publi.search = function(params, then){
        $http.post('index.php/recherche', params).then(function(response){
            then({success: true, content: response.data.resultat});
        }, function(response){
            if(response.status === 500){
                then({success: false, error: "Erreur interne au serveur"});
            }
            else {
                then({success: false, error: "Erreur inconnue"});
            }
        });
    };

    publi.getAuteurs = function(then){
        $http.get('index.php/auteurs').then(function(response){
                then({success: true, content: response.data.auteurs});
            }, function(response){
                if(response.status === 500){
                    then({success: false, error: "Erreur interne au serveur"});
                }
                else {
                    then({success: false, error: "Erreur inconnue"});
                }
            }
        );
    };

    publi.getJournaux = function(then){
        $http.get('index.php/journaux').then(function(response){
                then({success: true, content: response.data.journaux});
            }, function(response){
                if(response.status === 500){
                    then({success: false, error: "Erreur interne au serveur"});
                }
                else {
                    then({success: false, error: "Erreur inconnue"});
                }
            }
        );
    };

    publi.getConferences = function(then){
        $http.get('index.php/conferences').then(function(response){
                then({success: true, content: response.data.conferences});
            }, function(response){
                if(response.status === 500){
                    then({success: false, error: "Erreur interne au serveur"});
                }
                else {
                    then({success: false, error: "Erreur inconnue"});
                }
            }
        );
    };

    publi.getAuteur = function(id, then){
        $http.get('index.php/auteur/' + id).then(function(response){
                then({success: true, auteur: response.data.auteur, publis: response.data.publis});
            }, function(response){
                if(response.status === 404){
                    then({success: false, error: "L'auteur n°" + id + " n'existe pas !"});
                }
                else{
                    if(response.status === 500){
                        then({success: false, error: "Erreur interne au serveur"});
                    }
                    else {
                        then({success: false, error: "Erreur inconnue"});
                    }
                }
            }
        );
    };

    return publi;
}]);
