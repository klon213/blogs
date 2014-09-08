<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	public function actionAngular()
	{
		$this->render('angular');
	}


	public function actionArticles()
	{
		$this->render('articles');
	}

	public function actionRoute1()
	{
		echo "route1";
	}

	public function actionRoute2()
	{
		echo "route2";
	}

	public function actionListArticles()
	{
		//echo  "test"; //file_get_contents("site/angular.php");
		echo
		'<html ng-app>
			<script>
				function Article($scope, $http) {
					$http.get("http://blogs.org/api/articles/").
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
						<p><a href="#/view/{{item.id}}">{{item.title}}</a></p>
					</div>
				</div>
			</body>
		</html>';

	}

	public function actionViewArticles()
	{
		echo
		'<html ng-app>
			<script>
				function Article($scope, $http, $routeParams) {
					var currentId = $routeParams.id;
					$http.get("http://blogs.org/api/articles/view/id/" + currentId).
					success(function(data) {
					$scope.article = data;
					console.log(data);
					});
				}
			</script>
			<body>
				<div ng-controller="Article">
					<div ng-repeat="item in article">
						<p>Title: {{item.title}}</p>
						<p>Text: {{item.text}}</p>
					</div>
				</div>
			</body>
		</html>';
	}

	public function actionCreateArticle()
	{
		echo '
		<script>
			function formController($scope, $http){
    $scope.submit=function(){
    	//$http.post("http://blogs.org/api/articles/create", angular.toJSON($scope.LovelyFormData)).
    	 var request = $http({
                    method: "post",
                    url: "http://blogs.org/api/articles/create",

                    data: {
                        title: $scope.LovelyFormData.Title,
                        text: $scope.LovelyFormData.Text
                    },
                    headers:{
                    Authorization: "3bAfLaRxyJk7HyBfR6FgImF+9K38rERD"
                    }
                });
    }
}
	</script>
	<div ng-app="">
    <form name="LovelyForm" novalidate ng-controller="formController" ng-submit="submit()" >
        <input type="text" name="Title" placeholder="Title" ng-model="LovelyFormData.Title" required/>
        <br>
        <textarea name="Text" placeholder="Text" ng-model="LovelyFormData.Text" required/>
        <br>
        <input type="submit" value="Send Text" ng-disabled="LovelyForm.$invalid">
    </form>
</div>';
	}
	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}