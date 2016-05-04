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
    function($scope, $http, $state){

        $scope.annuler = function(){
            $state.go("home");
        };

        $scope.login = function(login_info){

            $scope.errors = "";

            if(!login_info || !login_info.username || !login_info.password){
                $scope.errors = "Le formulaire de connexion n'est pas complet !";
                return;
            }

            $http.post('login', { username: login_info.username, password: login_info.password }).then( //TODO: Hasher le mot de passe
                function(response){
                    //The request is successful
                    console.log("Login request OK");
                    console.log("Received data from server :");
                    console.log(response.data); //TODO: A supprimer

                    if(response.data.status == "succeed"){

                    }
                    else if(response.data.status == "invalid"){
                        $scope.errors = "Mauvais identifiant et/ou mot de passe !";
                    }
                    else{
                        $scope.errors = "Erreur inconnue !";
                    }
                    //TODO:
                    //Stocker les données utilisateur dans une variable de session du navigateur et créer un service Angular permettant d'y accéder facilement
                },
                function(response){
                    //The request is not successful
                    $scope.errors = "Erreur inconnue lors de l'accès au service !";
                }
            );
        };

    }]
);
