kickAss.service('Seasons', ['$http', 'BASE_URL', function($http, BASE_URL) {
    var serviceBackend = BASE_URL + '/api/';

    this.list = function () {
        return $http.get(serviceBackend + '/list/saison');
    };

    this.firstgame = function (id) {
        return $http.get(serviceBackend + '/games/firstgame/' + id);
    };

    this.gamesatfive = function(spieltag, hour, minute) {
        return $http.get(serviceBackend + '/games/spieltag/' + spieltag + '/' + hour + '/' + minute);
    };

    this.spieltage = function() {
        return $http.get(serviceBackend + '/list/spieltag');
    };
}]);