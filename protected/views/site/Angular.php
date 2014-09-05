<!doctype html>
<html ng-app>
<script>
	function Article($scope, $http) {
		$http.get('http://blogs.org/api/articles/').
			success(function(data) {
				$scope.article = data.data;
				console.log(data);
			});
	}
</script>

<body>
	<div ng-controller="Article">
		<div ng-repeat="item in article">
			<p>ID: {{item.id}}</p>
			<p>Title: {{item.title}}</p>
			<p>Content: {{item.text}}</p>
		</div>
	</div>
</body>
</html>

