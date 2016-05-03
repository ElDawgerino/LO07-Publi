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
