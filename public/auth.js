var app = new angular.module('auth-module', []);

app.factory('auth', [
  '$http',
  function($http){
    var auth = {};

    auth.regiser = function(user){
      $http.post('/register', user).then(function(response){

      }, function(response){

      });
    };

    auth.login = function(user){
      return $http.post('/login', user).then(function(respone){
        if(response.data.status == "succeed"){
          return {success: true};
        }
        else if(response.data.status == "invalid"){
            return {success: true, error:"Mauvais identifiant et/ou mot de passe !"};
        }
        else{
            return {success: true, error: "Erreur inconnue !"};
        }
      }, function(response){
        return {success: true, error: "Erreur inconnue lors de l'accès au service !"};
      });
    };

    auth.logout = function(){
      $http.post('/login', user).then(function(response){

      }, function(response){

      });
    };

    auth.currentUser = function(){

    };

    return auth;
}]);
