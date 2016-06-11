var app = new angular.module('admin-module', []);

app.factory('admin', [
    '$http',
    function($http){
        var admin = {};

        admin.anomalies = function(then){
            $http.get('anomalies').then(function(response){
                then({success: true, doublons: response.data.doublons, publications: response.data.publis});
            }, function(response){
                if(response.status === 500){
                    then({success: false, error: "Erreur interne au serveur"});
                }
                else{
                    then({success: false, error: "Impossible d'ajouter la publication : erreur inconnue"});
                }
            });
        };

        admin.stats = function(then){
            $http.get('stats').then(function(response){
                then({success: true, auteurs: response.data.auteurs, annees: response.data.annees});
            }, function(response){
                if(response.status === 500){
                    then({success: false, error: "Erreur interne au serveur"});
                }
                else{
                    then({success: false, error: "Impossible d'ajouter la publication : erreur inconnue"});
                }
            });
        };

        admin.comptes = function(then){
            $http.get('comptes').then(function(response){
                then({success: true, utilisateurs: response.data})
            }, function(response){
                if(response.status === 500){
                    then({success: false, error: "Erreur interne au serveur"});
                }
                else {
                    then({success: false, error: "Erreur inconnue"});
                }
            });
        }

        return admin;
}]);
