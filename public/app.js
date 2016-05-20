var app = angular.module('LO07-publi', ['ui.router', 'auth-module', 'naif.base64', 'publi-module', 'routes-module']);

app.controller('Home', [
    '$scope',
    '$state',
    'publi',
    function($scope, $state, publi){
      $scope.hasPublis = true;

      publi.getAll(function(response){
        if(response.success){
          $scope.publis = response.content;
        } else {
          $scope.hasPublis = false;
          $scope.errors = response.error;
        }
      });
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

    $scope.goProfile = function(){
        $state.go('profile', {id: $rootScope.id});
    };

    $scope.goPublish = function(){
      $state.go('publish');
    };

    $scope.goHome = function(){
      $state.go('home');
    };

    $scope.login = function(){
      $state.go('login');
    };

    $scope.register = function(){
      $state.go('register');
    };

    $scope.logout = function(){
      auth.logout(function(status){
        $scope.loggedIn = !status.success;
        $state.go('home');
      });
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
    $scope.auteur = {};
    $scope.auteurs= [];
    $scope.journaux = [];
    $scope.conferences = [];

    publi.getAuteurs(function(status){
      if(status.success){
        $scope.auteurs = status.content;
        } else if(status.message != "empty"){
        $scope.errors = status.error;
      }
    });

    publi.getJournaux(function(status){
      if(status.success){
        $scope.journaux = status.content;
      } else if(status.message != "empty"){
        $scope.errors = status.error;
      }
    });

    publi.getConferences(function(status){
      if(status.success){
        $scope.conferences = status.content;
      } else if(status.message != "empty"){
        $scope.errors = status.error;
      }
    });

    $scope.associateAuteur = function(){
        for(var i = 0; i < $scope.auteurs.length; i++){
            if($scope.auteurs[i].nom == $scope.auteur.nom){
                $scope.auteur.prenom = $scope.auteurs[i].prenom;
                $scope.auteur.organisation = $scope.auteurs[i].organisation;
                $scope.auteur.equipe = $scope.auteurs[i].equipe;
                return;
            }
        }
    };

    $scope.associateJournal = function(){
        for(var i = 0; i < $scope.journaux.length; i++){
            if($scope.journaux[i].titre == $scope.publi.journal_titre){
                $scope.publi.journal_editeur = $scope.journaux[i].editeur;
            }
        }
    };

    $scope.associateConf = function(){
        for(var i = 0; i < $scope.conferences.length; i++){
            if($scope.conferences[i].nom == $scope.publi.conference_nom){
                $scope.publi.conference_lieu = $scope.conferences[i].lieu;
                $scope.publi.conference_date = $scope.conferences[i].date_conference;
            }
        }
    };

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
      if($scope.publi.titre.length && $scope.publi.statut.length
        && $scope.publi.categorie.length && $scope.publi.annee_publication.length){
          var publication = angular.copy( $scope.publi );

          if($scope.isAuteur){
            publication.auteurs.push({ id: $rootScope.id} );
          }
          publi.post(publication, function(status){
            if(status.success){
              console.log("success");
              //aller à la publication
            }
            else{
              $scope.errors = status.error;
            }
          });
        }
    };

}]);

app.controller('Publi', [
  '$scope',
  '$stateParams',
  'publi',
  function($scope, $stateParams, publi){
    $scope.hasJournal = false;
    $scope.hasConference = false;

    $scope.download = function(){
        window.open("download/" + $scope.publi.id, '_blank');
    }

    publi.get($stateParams.id, function(response){
      if(response.success){
        $scope.publi = response.content;
        if(response.content.journal_titre != null){
          $scope.hasJournal = true;
        }
        if(response.content.conference_nom != null){
          $scope.hasConference = true;
        }
      } else {
        $scope.errors = response.error;
      }
    })
}]);

app.controller('Profile', [
    '$scope',
    '$stateParams',
    'publi',
    function($scope, $stateParams, publi){
        $scope.hasPublis = false;

        publi.getAuteur($stateParams.id, function(response){
            if(response.success){
                $scope.auteur = response.auteur[0];
                if(response.publis.length){
                    $scope.publis = response.publis;
                    $scope.hasPublis = true;
                } else {
                    $scope.hasPublis = false;
                }
            }
            else if(response.message != "empty"){
                $scope.errors = response.error;
            }
        });
}]);
