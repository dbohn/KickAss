kickAss.service('Seasons', ['$http', 'BASE_URL', function($http, BASE_URL) {
    var serviceBackend = BASE_URL + '/api/';

    this.list = function () {
        return $http.get(serviceBackend + '/list/saison');
    };

    this.firstgame = function (id) {
        return $http.get(serviceBackend + '/games/firstgame/' + id);
    };
}]);