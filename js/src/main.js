
// App Code

var kickAss = angular.module('kickass', ['ui.router', 'angularMoment']);

kickAss.config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/');

    $stateProvider.state('initial', {
        url: '/',
        views: {
            "nav": {
                templateUrl: 'partials/menus/qanda.html'
            },
            "main": {
                templateUrl: 'partials/initial.html'
            }
        }
    }).state('firstgame', {
        url: '/firstgame',
        views: {
            "nav": {
                templateUrl: 'partials/menus/qanda.html'
            },
            "main": {
                controller: 'FirstgameCtrl',
                templateUrl: 'partials/firstgame.html'
            }
        }
    });
}]);

// UI Code
$(function(){

  $('#sidebar').affix({
      offset: {
        top: 191
      }
    });

});
