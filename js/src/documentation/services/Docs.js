kickAss.service('Docs', ['$http', 'BASE_URL', function($http, BASE_URL){
	var serviceBackend = BASE_URL + '/documentation';

	this.listPages = function() {
		return $http.get(serviceBackend);
	};

	this.showPage = function(page) {
		return $http.get(serviceBackend + '/' + page);
	};
}]);