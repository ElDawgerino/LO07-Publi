var app = angular.module('LO07-publi', []);

app.controller('Home', [
'$scope',
function($scope){
  $scope.text = 'Une application web de gestion du publication!';
}]);
