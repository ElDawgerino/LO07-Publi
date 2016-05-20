var app = new angular.module('routes-module', ['ui.router']);

app.config([
  '$stateProvider',
  '$urlRouterProvider',
  function($stateProvider, $urlRouterProvider, $rootScope){

    $stateProvider.state('home',{
      url: '/home',
      templateUrl: 'public/templates/home.html',
      controller: 'Home'
    });

    $stateProvider.state('register', {
      url: '/register',
      templateUrl: 'public/templates/register.html',
      controller: 'Register'
    });

    $stateProvider.state('login', {
      url: '/login',
      templateUrl: 'public/templates/login.html',
      controller: 'Login'
    });

    $stateProvider.state('publish', {
      url: '/publish',
      templateUrl: 'public/templates/publish.html',
      controller: 'Publish'
    });

    $stateProvider.state('publi', {
      url: '/publi/{id}',
      templateUrl: 'public/templates/publi.html',
      controller: 'Publi',
    });

    $stateProvider.state('profile',{
        url: '/profile/{id}',
        templateUrl: 'public/tempates/profile.html',
        controller: 'Profile'
    })

    $urlRouterProvider.otherwise('home');
}]);
