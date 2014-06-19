
// App Code

var kickAss = angular.module('kickass', ['ui.router', 'ui.bootstrap', 'angularMoment']);

kickAss.config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/');

    $stateProvider.state('initial', {
        url: '/',
        views: {
            "nav": {
                templateUrl: 'partials/menus/qanda.html'
            },
            "main": {
                controller: 'TableCtrl',
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
    }).state('gamesatfive', {
        url: '/gamesatfive',
        views: {
            "nav": {
                templateUrl: 'partials/menus/qanda.html'
            },
            "main": {
                controller: 'GamesAtFiveCtrl',
                templateUrl: 'partials/gamesatfive.html'
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
