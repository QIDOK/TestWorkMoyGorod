<?php

namespace frontend\controllers;

use common\models\Task;
use common\models\TaskComment;
use frontend\models\TaskCommentForm;
use frontend\models\TaskForm;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\SignupForm;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return Yii::$app->response->redirect(['site/login']);
        }

        $tasks = Task::find()->where(['owner' => Yii::$app->user->id])->all();

        return $this->render('index', ['tasks' => $tasks]);
    }

    public function actionView($id = null) {
        if (Yii::$app->user->isGuest) {
            return Yii::$app->response->redirect(['site/login']);
        }

        if(!$id) return throw new NotFoundHttpException("Задача не найдена");

        $model = Task::findOne($id);
        if(!$model) return throw new NotFoundHttpException("Задача не найдена");

        $comments = TaskComment::find()->where(['task_id' => $id])->all();

        return $this->render('task/view', [
            'model' => $model,
            'comments' => $comments
        ]);
    }

    public function actionCreate() {
        if (Yii::$app->user->isGuest) {
            return Yii::$app->response->redirect(['site/login']);
        }

        $model = new TaskForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $task = new Task([
                'owner' => Yii::$app->user->id,
                'title' => $model->title,
                'description' => $model->description,
                'is_active' => $model->status == Task::TASK_STATUS_COMPLETED || $model->status == Task::TASK_STATUS_CANCELED ? Task::TASK_NOT_ACTIVE : $model->is_active,
                'status' => $model->status
            ]);

            if ($task->save()) {
                Yii::$app->session->setFlash('success', 'Задача создана!');
                return Yii::$app->response->redirect(['site/view', 'id' => $task->id]);
            }
        }

        $model->is_active = true;

        return $this->render('task/create', ['model' => $model]);
    }

    public function actionUpdate($id = null) {
        if (Yii::$app->user->isGuest) {
            return Yii::$app->response->redirect(['site/login']);
        }

        if(!$id) return throw new NotFoundHttpException("Задача не найдена");

        $model = Task::findOne($id);
        if(!$model) return throw new NotFoundHttpException("Задача не найдена");

        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->is_active = $model->status == Task::TASK_STATUS_COMPLETED || $model->status == Task::TASK_STATUS_CANCELED ? Task::TASK_NOT_ACTIVE : $model->is_active;

            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Задача изменена!');
                return Yii::$app->response->redirect(['site/view', 'id' => $model->id]);
            }
        }

        return $this->render('task/update', ['model' => $model]);
    }

    public function actionDelete($id = null) {
        if (Yii::$app->user->isGuest) {
            return Yii::$app->response->redirect(['site/login']);
        }

        if(!$id) return throw new NotFoundHttpException("Задача не найдена");

        $model = Task::findOne($id);
        if(!$model) return throw new NotFoundHttpException("Задача не найдена");

        $model->delete();

        return Yii::$app->response->redirect('/');
    }

    public function actionCommentCreate($task_id = null) {
        if (Yii::$app->user->isGuest) {
            return Yii::$app->response->redirect(['site/login']);
        }

        if(!$task_id) return throw new NotFoundHttpException("Задача не найдена");

        $task = Task::findOne($task_id);
        if(!$task) return throw new NotFoundHttpException("Задача не найдена");

        $model = new TaskCommentForm();
        $model->task_id = $task_id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $task = new TaskComment([
                'task_id' => $model->task_id,
                'owner' => Yii::$app->user->id,
                'text' => $model->text
            ]);

            if ($task->save()) {
                Yii::$app->session->setFlash('success', 'Комментарий создан!');
                return Yii::$app->response->redirect(['site/view', 'id' => $model->task_id]);
            }
        }

        return $this->render('task/comment/create', ['model' => $model]);
    }

    public function actionCommentUpdate($id = null) {
        if (Yii::$app->user->isGuest) {
            return Yii::$app->response->redirect(['site/login']);
        }

        if(!$id) return throw new NotFoundHttpException("Комментарий не найден");

        $model = TaskComment::findOne($id);
        if(!$model) return throw new NotFoundHttpException("Комментарий не найден");

        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            Yii::$app->session->setFlash('success', 'Задача изменена!');
            return Yii::$app->response->redirect(['site/view', 'id' => $model->task_id]);
        }

        return $this->render('task/comment/update', ['model' => $model]);
    }

    public function actionCommentDelete($id = null) {
        if (Yii::$app->user->isGuest) {
            return Yii::$app->response->redirect(['site/login']);
        }

        if(!$id) return throw new NotFoundHttpException("Комментарий не найден");

        $model = TaskComment::findOne($id);
        if(!$model) return throw new NotFoundHttpException("Комментарий не найден");

        $task_id = $model->task_id;
        $model->delete();

        return Yii::$app->response->redirect(['site/view', 'id' => $model->task_id]);
    }

    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Успешная регистрация!');
            return $this->goHome();
        }

        return $this->render('auth/signup', [
            'model' => $model,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', 'Успешная авторизация!');
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('auth/login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
