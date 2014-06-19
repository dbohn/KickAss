kickAss.service('Table', ['$http', 'BASE_URL', function($http, BASE_URL) {
    var serviceBackend = BASE_URL + '/api/';

    this.fetch = function (liga) {
        return $http.get(serviceBackend + '/tabelle/' + liga);
    };

    this.ligen = function () {
        return $http.get(serviceBackend + '/list/liga');
    };
}]);