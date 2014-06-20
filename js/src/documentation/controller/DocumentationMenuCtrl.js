kickAss.controller('DocumentationMenuCtrl', ['$scope', 'Docs', function($scope, Docs){
	$scope.pages = [];

	Docs.listPages().success(function(data) {
		$scope.pages = data.pages;
	});
}]);