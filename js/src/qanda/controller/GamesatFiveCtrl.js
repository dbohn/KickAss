kickAss.controller('GamesAtFiveCtrl', ['$scope', 'Seasons', function($scope, Seasons) {
    $scope.mytime = new Date();
    $scope.mytime.setHours(12);
    $scope.mytime.setMinutes(0);
    $scope.mytime.setSeconds(0);
    $scope.queryExpanded = false;
    $scope.query = "";
    $scope.spieltage = [];


    $scope.data = [];

    Seasons.spieltage().success(function(data) {
        $scope.spieltage = data.payload;
    });

    $scope.update = function() {
        if (!$scope.spieltag) {
            var spieltag = 1;
        } else {
            var spieltag = $scope.spieltag.id;
        }
        Seasons.gamesatfive(spieltag, $scope.mytime.getHours(), $scope.mytime.getMinutes()).success(function(data) {
            $scope.data = data.payload;
            $scope.query = data.sql;
        });
    }
}]);