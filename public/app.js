var app = angular.module('LO07-publi', ['ui.router']);

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
'$state',
function($scope, $state){

  $scope.text = 'Une application web de gestion du publication!';

  $scope.register = function(){
    $state.go("register");
  };

  $scope.login = function(){
    $state.go("login");
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
    function($scope, $http, $state){

        $scope.annuler = function(){
            $state.go("home");
        };

        $scope.logIn = function(){
            $http.post('api/login', { username: "test1", password: "angular" }).then(
                function(response){
                    //The request is successful
                    console.log("Login request OK");
                    console.log("Received data from server :");
                    console.log(response.data); //TODO: A supprimer
                    //TODO:
                    //Stocker le token dans une variable de session du navigateur et créer un service Angular permettant d'y accéder facilement
                },
                function(response){
                    //The request is not successful
                }
            );
        };

    }]
);
