kickAss.controller('TableCtrl', ['$scope', 'Table', function($scope, Table) {
    $scope.table = [];
    $scope.query = "";
    $scope.ligen = [];
    $scope.queryExpanded = false;

    Table.ligen().success(function(data) {
        $scope.ligen = data.payload;
    });

    Table.fetch(1).success(function(data) {
        $scope.table = data.payload;
        $scope.query = data.sql;
    });

    $scope.selectedLiga = function() {
        Table.fetch($scope.liga.id).success(function(data) {
            $scope.query = data.sql;
            $scope.table = data.payload;
        });
    }

}]);