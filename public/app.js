var app = angular.module('LO07-publi', ['ui.router', 'auth-module', 'naif.base64']);

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
    'auth',
    function($scope, $http, $state, auth){

        $scope.text = 'Une application web de gestion du publication!';

        $scope.register = function(){
            $state.go("register");
        };

        $scope.login = function(){
            $state.go("login");
        };

        $scope.logout = function(){
          //TODO: faire ça avec une navbar
          auth.logout(function(status){
            $scope.errors = status.error;
          });
        };

}]);


app.controller('Register', [
    '$scope',
    '$http',
    '$state',
    'auth',
    function($scope, $http, $state, auth){

      $scope.equipes = ["CREIDD", "ERA", "GAMMA3", "LASMIS", "LM2S", "LNIO", "LOSI", "Tech-CICO"];

      $scope.annuler = function(){
        $state.go("home");
      };

      $scope.register = function(){
        if(!$scope.user.prenom || !$scope.user.nom || !$scope.user.username || !$scope.user.password
        || !$scope.user.organisation || !$scope.user.equipe){
            $scope.errors = "Le formulaire d'inscription n'est pas complet !";
            return;
        }

        auth.register($scope.user, function(result){
            $scope.errors = status.error;
        });
      };
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

            auth.login($scope.login_info, function(result){
                $scope.errors = result.error;
            });
        };

    }]
);
