kickAss.controller('FirstgameCtrl', ['$scope', 'Seasons', function($scope, Seasons) {
    $scope.seasons = [];
    $scope.query = "";
    $scope.saisonstart = "";

    Seasons.list().success(function(data) {
        $scope.seasons = data.payload;
    });

    $scope.selectedSeason = function() {

        Seasons.firstgame($scope.season.id).success(function(data) {
            $scope.query = data.sql;
            $scope.saisonstart = data.payload[0].anpfiff_datum;
        });
    }

}]);