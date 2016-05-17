var app = angular.module('LO07-publi', ['ui.router', 'auth-module', 'naif.base64', 'publi-module']);

app.config([
  '$stateProvider',
  '$urlRouterProvider',
  function($stateProvider, $urlRouterProvider, $rootScope){

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

    $stateProvider.state('publish', {
      url: '/publish',
      templateUrl: 'publish',
      controller: 'Publish'
    });

    $urlRouterProvider.otherwise('home');
}]);

app.controller('Home', [
    '$scope',
    '$state',
    function($scope, $state){

      $scope.publier = function(){
        $state.go('publish')
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
            if(result.success){
              $state.go('home');
            }
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
            if(result.success){
              $state.go('home');
            }
          });

      };
}]);

app.controller('NavBar', [
  '$scope',
  '$state',
  '$rootScope',
  'auth',
  function($scope, $state, $rootScope, auth){
    $rootScope.$on('$stateChangeStart',
      function(event, toState, toParams, fromState, fromParams, options){
        auth.currentUser(function(status){
          $scope.loggedIn = status.success;
          if(status.success){
            $scope.username = status.username;
            $rootScope.id = status.id;
          }else{
            $scope.username = null;
          }
        });
    });

    $scope.goPublish = function(){
      $state.go('publish');
    }

    $scope.goHome = function(){
      $state.go('home');
    }

    $scope.login = function(){
      $state.go('login');
    };

    $scope.register = function(){
      $state.go('register');
    };

    $scope.logout = function(){
      auth.logout(function(status){
        $scope.loggedIn = !status.success;
      });
      $state.go('home');
    };
}]);

app.controller('Publish', [
  '$scope',
  '$http',
  '$rootScope',
  'publi',
  'auth',
  function($scope, $http, $rootScope, publi, auth){
    $scope.statuts = ["Soumis", "En révision", "Publié"];
    $scope.categories = ['RI', 'CI', 'RF', 'CF', 'OS', 'TD', 'BV', 'AP'];
    $scope.publi = {};
    $scope.publi.auteurs = [];

    $scope.addAuteur = function(){
      $scope.publi.auteurs.push({
        prenom : $scope.auteur.prenom,
        nom : $scope.auteur.nom,
        organisation : $scope.auteur.organisation,
        equipe : $scope.auteur.equipe
      });
    };

    $scope.removeAuteur = function(index){
      $scope.publi.auteurs.splice(index, 1);
    };

    $scope.publish = function(){
      if($scope.isAuteur){
        $scope.publi.auteurs.push($rootScope.id);
      }
      publi.post($scope.publi, function(status){
        if(status.success){
          console.log("success");
          //aller à la publication
        }
        else{
          $scope.errors = status.error;
        }
      });
    };

}]);
