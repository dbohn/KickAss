kickAss.controller('DocumentationCtrl', ['$scope', 'Docs', '$stateParams', function($scope, Docs, $stateParams){
	// console.log($stateParams);
	$scope.page = {};
	$scope.page.title = $stateParams.page;

	Docs.showPage($stateParams.page).success(function(data) {
		$scope.page.contents = data.docs;
	});
}]);