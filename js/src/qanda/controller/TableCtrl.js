kickAss.controller('TableCtrl', ['$scope', 'Table', function($scope, Table) {
    $scope.table = [];
    $scope.query = "";

    Table.fetch(1).success(function(data) {
        $scope.table = data.payload;
        $scope.query = data.sql;
    });

}]);