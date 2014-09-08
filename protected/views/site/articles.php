
<body ng-app="articleRoutes">

<a href="#/ArticleCreate">Create Article</a>
<a href="#/ArticleList">Get Articles</a>


<div ng-view></div>

<script>
	var module = angular.module("articleRoutes", ['ngRoute']);

	module.config(['$routeProvider',
		function($routeProvider) {
			$routeProvider.
				when('/ArticleCreate', {
					templateUrl: 'CreateArticle',
					controller: 'Route1Controller'
				}).
				when('/ArticleDelete', {
					templateUrl: 'route2',
					controller: 'Route2Controller'
				}).
				when('/ArticleList', {
					templateUrl: 'ListArticles',
					controller: 'Route3Controller'
				}).
				when('/view/:id', {
					templateUrl: 'ViewArticles',
					controller: 'Route4Controller'
				}).
				otherwise({
					redirectTo: '/'
				});
		}]);

	module.controller("Route1Controller", function($scope) {
		console.log('route1 control');
	})

	module.controller("Route2Controller", function($scope) {
		console.log('route2 control');
	})

	module.controller("Route3Controller", function($scope) {
		console.log('route3 control');
	})

	module.controller("Route4Controller", function($scope) {
		console.log('route4 control');
	})
</script>