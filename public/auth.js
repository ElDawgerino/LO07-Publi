var app = new angular.module('auth-module', []);

app.factory('auth', [
  '$http',
  function($http){
    var auth = {};

    auth.register = function(user, then){
        console.log(user);

        user_info = {
            username: user.login,
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
                    then({success: false, error: "Utilisateur invalide !"});
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
                else if(response.data.status == "invalid"){
                    then({success: false, error: "Mauvais identifiant et/ou mot de passe !"});
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

    auth.logout = function(){
      $http.post('logout').then(function(response){

      }, function(response){

      });
    };

    auth.currentUser = function(){

    };

    return auth;
}]);
