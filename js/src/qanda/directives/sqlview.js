kickAss.directive('sqlView', [function() {
    return {
        restrict: 'A',
        template: '<pre><code></code></pre>',
        scope: {
            'sql': '='
        },
        link: function(scope, element, attributes) {
            scope.$watch(function() {
                return scope.sql;
            }, function() {
                if (scope.sql) {
                    element.find('code').html(hljs.highlightAuto(scope.sql).value);
                }
            });
        }
    }
}]);