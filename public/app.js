var app = angular.module('LO07-publi', ['ui.router', 'auth-module']);

app.config([
  '$stateProvider',
  '$urlRouterProvider',
  function($stateProvider, $urlRouterProvider){

    $stateProvider.state('home',{
      url: '/home',
      templateUrl: 'home',
      controller: 'Home'
    });

    $stateProvider.state('register', {
      url: '/register',
      templateUrl: 'register',
      controller: 'Register'
    });

    $stateProvider.state('login', {
      url: '/login',
      templateUrl: 'login',
      controller: 'Login'
    });

    $urlRouterProvider.otherwise('home');
}]);

app.controller('Home', [
    '$scope',
    '$http',
    '$state',
    function($scope, $http, $state){

        $scope.text = 'Une application web de gestion du publication!';

        $scope.register = function(){
            $state.go("register");
        };

        $scope.login = function(){
            $state.go("login");
        };

        $scope.logout = function(){
            //TODO: Mettre ça dans un autre fichier JS
            $http.post('logout').then(
                function(response){
                    //The request is successful
                    console.log("Logout request OK");
                    console.log("Received data from server :");
                    console.log(response.data); //TODO: A supprimer
                    //TODO:
                    //Retirer le token de la variable de session !
                },
                function(response){
                    //The request is not successful
                }
            );
        }

}]);


app.controller('Register', [
    '$scope',
    '$http',
    '$state',
    function($scope, $http, $state){

      $scope.equipes = ["CREIDD", "ERA", "GAMMA3", "LASMIS", "LM2S", "LNIO", "LOSI", "Tech-CICO"];

      $scope.annuler = function(){
        $state.go("home");
      };

      $scope.register = function(){
        //TODO : envoyer les données du formulaire au serveur
      }
}]);

app.controller('Login',[
    '$scope',
    '$http',
    '$state',
    'auth',
    function($scope, $http, $state, auth){

      $scope.login_info = {};

      $scope.annuler = function(){
          $state.go("home");
      };

      $scope.login = function(login_info){

          $scope.errors = "";

          if(!$scope.login_info.username || !$scope.login_info.password){
              $scope.errors = "Le formulaire de connexion n'est pas complet !";
              return;
          }
          var result = auth.login($scope.login_info);
          $scope.errors = result;
      };

    }]
);
