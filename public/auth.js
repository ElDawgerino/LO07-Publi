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
                then({success : true});
            },
            function(response){
                if(response.status === 400){
                    then({success: false, error: "Impossible d'ajouter l'utilisateur, il y a probablement un utilisateur portant le même login !"})
                }
                else{
                    then({success: false, error: "Erreur inconnue !"});
                }
            }
        );
    };

    auth.login = function(user, then){
        user_info = {
            username: user.username,
            password: user.password
        };

        return $http.post('login', user_info).then(function(response){
                then({success: true});
            },
            function(response){
                if(response.status === 403){
                    then({success: false, error: "Vous êtes déjà connecté !"});
                }
                else if(response.status === 401){
                    then({success: false, error: "Mauvais identifiant/mot de passe !"});
                }
                else{
                    then({success: false, error: "Erreur inconnue !"});
                }
            }
        );
    };

    auth.logout = function(then){
        $http.get('logout').then(function(response){
                then({success: true});
            }, function(response){
                then({success: false, error: "Impossible de vous déconnecter, vous n'êtes probablement pas connecté !"});
            }
        );
    };

    auth.currentUser = function(then){
        $http.get('compte').then(function(response){
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
                        then({success: false, error: "Erreur inconnue !"});
                    }
                );
            }, function(response){
                if(response.status === 401){
                    then({success: false, error: "Vous n'êtes pas connecté !"});
                }
                else{
                    then({success: false, error: "Erreur inconnue !"});
                }
            }
        );
    };

    return auth;
}]);
