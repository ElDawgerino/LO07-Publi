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
  '$state',
  '$http',
  function($scope, $http, $state){

    $scope.annuler = function(){
      $state.go("home");
    };

    $scope.logIn = function(){
      //TODO : envoyer les données du formulaire au serveur
    };

}]);
