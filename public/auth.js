var app = new angular.module('auth-module', []);

app.factory('auth', [
  '$http',
  function($http){
    var auth = {};

    auth.register = function(user, then){
        user_info = {
            username: user.username,
            password: user.password,
            last_name: user.nom,
            first_name: user.prenom,
            organisation: user.organisation,
            team: user.equipe
        };
        return $http.post('register', user_info).then(function(response){
                if(response.data.status == "succeed"){
                    then({success : true});
                }
                else if(response.data.status == "invalid"){
                    then({success: false, error: "Utilisateur invalide : compte déjà existant !"});
                }
                else{
                    then({success: false, error: "Erreur inconnue !"});
                }
            },
            function(response){
                then({success: false, error: "Erreur inconnue lors de l'accès au service !"});
            }
        );
    };

    auth.login = function(user, then){
        user_info = {
          username: user.username,
          password: user.password
        };

        return $http.post('login', user_info).then(function(response){
                if(response.data.status == "succeed"){
                    then({success: true});
                }
                else if(response.data.status == "db_error"){
                    then({success: false, error: "Impossible de se connecter à la base de donnée"});
                }
                else if(response.data.status == "invalid"){
                    then({success: false, error: "Mauvais identifiant et/ou mot de passe !"});
                }
                else if(response.data.status == "already_connected"){
                  then({success: false, error: "Vous êtes déjà connecté."})
                }
                else{
                    then({success: false, error: "Erreur inconnue !"});
                }
            },
            function(response){
                then({success: false, error: "Erreur inconnue lors de l'accès au service !"});
            }
        );
    };

    auth.logout = function(then){
      $http.get('logout').then(function(response){
        if(response.data.status == "succeed"){
          then({success: true});
        }
        else if(response.data.status == "was_not_connected"){
          then({success: true, error: "Vous n'êtes pas connecté."});
        }
        else{
          then({success: false, error: "Erreur inconnue"});
        }
      }, function(response){
        then({success: false, error: "Erreur inconnue"});
      });
    };

    auth.currentUser = function(then){
      $http.get('compte').then(function(response){
        if(response.data.status == "invalid"){
          then({success: false})
        } else {
          $http.get('compte/' + response.data.id).then(
            function(response){
              then({
                success: true,
                id: response.data.id,
                username: response.data.username,
                last_name: response.data.last_name,
                first_name: response.data.first_name,
                organisation: response.data.organisation,
                team: response.data.team
              });
            }, function(response){
              then({success: false, error: "Erreur inconnue"});
            }
          )
          then({success: true, response: response});
        }
      }, function(response){
          then({success: false, error: "Erreur inconnue"});
      });
    };

    return auth;
}]);
