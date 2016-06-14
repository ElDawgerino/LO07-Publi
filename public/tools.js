var app = new angular.module('tools-module', []);

//Filtre spécial pour afficher une taille de façon user-friendly
app.filter('bytes', function() {
    return function(bytes) {
        if (isNaN(parseFloat(bytes)) || !isFinite(bytes) || bytes == 0) return '0';
        var units = {1: 'Kio', 2: 'Mio', 3: 'Gio', 4: 'Tio'},
            measure, floor, precision;
        if (bytes > 1099511627775) {
            measure = 4;
        } else if (bytes > 1048575999 && bytes <= 1099511627775) {
            measure = 3;
        } else if (bytes > 1024000 && bytes <= 1048575999) {
            measure = 2;
        } else if (bytes <= 1024000) {
            measure = 1;
        }
        floor = Math.floor(bytes / Math.pow(1024, measure)).toString().length;
        if (floor > 3) {
            precision = 0
        } else {
            precision = 3 - floor;
        }
        return (bytes / Math.pow(1024, measure)).toFixed(precision) + units[measure];
    }
});

//Filtre pour garantir l'unicité
app.filter('unique', function() {
   return function(collection, keyname) {
      var output = [],
          keys = [];

      angular.forEach(collection, function(item) {
          var key = item[keyname];
          if(keys.indexOf(key) === -1) {
              keys.push(key);
              output.push(item);
          }
      });

      return output;
   };
});

/**
 * Directive pour afficher une liste de publications
 *
 * Paramètres :
 * - liste = la variable contenant une liste des publications à afficher
 */
app.directive('projetListePublications', function(){
    return {
        restrict: 'E',
        scope: {
            liste: '=liste',
        },
        templateUrl: 'public/templates/directives/listepublications.html',
        controller: function($scope){

            //Chargement du groupage sauvegardé par l'utilisateur
            if(localStorage.getItem("listePublicationsGroupBy")) {
                $scope.groupBy = localStorage.getItem("listePublicationsGroupBy");
            }
            else {
                $scope.groupBy = 'categorie';
            }

            //Chargement du tri sauvegardé par l'utilisateur
            if(localStorage.getItem("listePublicationsOrderBy")) {
                $scope.orderBy = localStorage.getItem("listePublicationsOrderBy");
            }
            else {
                $scope.orderBy = '-annee_publication';
            }

            //Fonction appelée par le bouton "Réinitialiser"
            $scope.reset = function() {
                $scope.groupBy = 'categorie';
                $scope.orderBy = '-annee_publication';

                this.saveGroupByToLocalStorage();
                this.saveOrderByToLocalStorage();
            }

            //Appelée lors d'un changement dans le select du groupage
            $scope.saveGroupByToLocalStorage = function() {
                localStorage.setItem("listePublicationsGroupBy", $scope.groupBy);
            }

            //Appelée lors d'un changement dans le select du tri
            $scope.saveOrderByToLocalStorage = function() {
                localStorage.setItem("listePublicationsOrderBy", $scope.orderBy);
            }
        }
    };
});

app.directive('auteurs', function(){
    return {
        restrict: 'E',
        scope: {
            liste: '=liste',
        },
        templateUrl: 'public/templates/directives/listeauteurs.html',
    };
});
