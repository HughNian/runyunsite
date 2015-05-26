//入口文件
var mainApp = angular.module('mainApp', ['ngRoute']);
var mid; //菜单id名称全局变量
mainApp.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider){
	//$locationProvider.html5Mode(true);
	$routeProvider
	    .when('/', {
	    	templateUrl : 'views/index.html',
	    })
		.when('/template', {
			templateUrl : 'views/template.html',
		})
		.when('/templatedetail/:aid/:typeid', {
			templateUrl : 'views/templatedetail.html',
		})
		.when('/dynamiclist', {
			templateUrl : 'views/dynamiclist.html',
		})
		.when('/dynamiclist/:typeid', {
			templateUrl : 'views/dynamiclist.html',
		})
		.when('/dynamicdetail/:typeid/:aid', {
			templateUrl : 'views/dynamicdetail.html',
		})
		.otherwise({
            redirectTo: '/'
        });
}]);

mainApp.directive('onFinishRenderFilters', function ($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attr) {
      if (scope.$last === true) {
        $timeout(function() {
          scope.$emit('ngRepeatFinished');
        });
      }
    }
  };
});

mainApp.directive('onFinishRenderList', function ($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attr) {
      if (scope.$last === true) {
        $timeout(function() {
          scope.$emit('ngRepeatFinishedList');
        });
      }
    }
  };
});