
<body ng-app="articleRoutes">

<a href="#/ArticleCreate">Create Article</a>
<a href="#/ArticleDelete">Delete Article</a>
<a href="#/ArticleList">Get Articles</a>


<div ng-view></div>

<script>
	var module = angular.module("articleRoutes", ['ngRoute']);

	module.config(['$routeProvider',
		function($routeProvider) {
			$routeProvider.
				when('/ArticleCreate', {
					templateUrl: 'route1',
					controller: 'RouteController'
				}).
				when('/ArticleDelete', {
					templateUrl: 'route2',
					controller: 'RouteController'
				}).
				when('/ArticleList', {
					templateUrl: 'ListArticles',
					controller: 'RouteController'
				}).
				otherwise({
					redirectTo: '/'
				});
		}]);

	module.controller("RouteController", function($scope) {

	})
</script>