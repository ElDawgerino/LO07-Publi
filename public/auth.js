var app = new angular.module('auth-module', []);

app.factory('auth', [
  '$http',
  function($http){
    var auth = {};

    auth.register = function(user){
      console.log(user);
      return $http.post('/register', user).then(function(response){
        if(response.data.status == "succeed"){
          return {success : true};
        }
        else{
          return{success: false, error: "Erreur inconnue !"};
        }
      }, function(response){
        return {success: false, error: "Erreur inconnue !"};
      });
    };

    auth.login = function(user){
      user.password = sha256_digest(user.password);

      return $http.post('/login', user).then(function(respone){
        if(response.data.status == "succeed"){
          return {success: true};
        }
        else if(response.data.status == "invalid"){
            return {success: false, error:"Mauvais identifiant et/ou mot de passe !"};
        }
        else{
            return {success: false, error: "Erreur inconnue !"};
        }
      }, function(response){
        return {success: false, error: "Erreur inconnue lors de l'accès au service !"};
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
