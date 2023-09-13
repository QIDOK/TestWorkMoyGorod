<?php

namespace api\modules\v1\controllers;

use common\models\Task;
use common\models\TaskComment;
use common\models\UserLoginApi;
use Yii;
use yii\rest\ActiveController;

class TaskCommentController extends ActiveController
{
    public $modelClass = "common\models\TaskComment";

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ]
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['delete'], $actions['create'], $actions['view'], $actions['update']);

        return $actions;
    }

    public function actionView() {
        $token = Yii::$app->request->getHeaders()['Authorization'];
        $id = Yii::$app->request->get('id');

        $response = [
            'status' => '',
            'request' => [
                'token' => $token,
                'id' => $id
            ],
        ];

        if(!$id) {
            $response['status'] = 'error';
            $response += ['errors' => [
                [
                    'type' => 'id empty',
                    'message' => 'Не передано обязательное поле id'
                ]
            ]];

            return $response;
        }

        $model = $this->commentValidate($id, $token);
        if(isset($model['errors']) && is_array($model)) {
            $response['status'] = 'error';
            $response += $model;
            return $response;
        }

        $response['status'] = 'success';
        $response += ['response' => [
            'id' => $model->id,
            'task_id' => $model->task_id,
            'owner' => $model->owner,
            'text' => $model->text,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ]];

        return $response;
    }

    public function actionCreate() {
        $token = Yii::$app->request->getHeaders()['Authorization'];
        $task_id = Yii::$app->request->get('task_id');
        $text = Yii::$app->request->get('text');

        $response = [
            'status' => '',
            'request' => [
                'token' => $token,
                'task_id' => $task_id,
                'text' => $text
            ],
        ];

        if(!$task_id || !$text) {
            $response['status'] = 'error';
            $response += ['errors' => []];

            if(!$task_id) {
                $response['errors'][] = [
                    'type' => 'task_id empty',
                    'message' => 'Не передано обязательное поле task_id'
                ];
            }

            if(!$text) {
                $response['errors'][] = [
                    'type' => 'text empty',
                    'message' => 'Не передано обязательное поле text'
                ];
            }

            return $response;
        }

        if(!strlen($text)) {
            $response['status'] = 'error';
            $response += ['errors' => [
                [
                    'type' => 'invalid title size',
                    'message' => 'Комментарий не может быть короче 1 символа'
                ]
            ]];

            return $response;
        }

        $task = $this->taskValidate($task_id, $token, false);
        if(isset($task['errors']) && is_array($task)) {
            $response['status'] = 'error';
            $response += $task;
            return $response;
        }

        $model = new TaskComment([
            'task_id' => $task_id,
            'owner' => $task->owner,
            'text' => $text,
        ]);
        $model->save();

        $response['status'] = 'success';
        $response += ['response' => [
            'id' => $model->id,
            'task_id' => $model->task_id,
            'owner' => $model->owner,
            'text' => $model->text
        ]];

        return $response;
    }

    public function actionUpdate() {
        $token = Yii::$app->request->getHeaders()['Authorization'];
        $id = Yii::$app->request->get('id');
        $text = Yii::$app->request->get('text');

        $response = [
            'status' => '',
            'request' => [
                'token' => $token,
                'id' => $id,
                'text' => $text
            ],
        ];

        if(!$id || !$text) {
            $response['status'] = 'error';
            $response += ['errors' => []];

            if(!$id) {
                $response['errors'][] = [
                    'type' => 'id empty',
                    'message' => 'Не передано обязательное поле id'
                ];
            }

            if(!$text) {
                $response['errors'][] = [
                    'type' => 'text empty',
                    'message' => 'Не передано обязательное поле text'
                ];
            }

            return $response;
        }

        if(!strlen($text)) {
            $response['status'] = 'error';
            $response += ['errors' => [
                [
                    'type' => 'invalid title size',
                    'message' => 'Комментарий не может быть короче 1 символа'
                ]
            ]];

            return $response;
        }

        $model = $this->commentValidate($id, $token);
        if(isset($model['errors']) && is_array($model)) {
            $response['status'] = 'error';
            $response += $model;
            return $response;
        }

        $model->text = $text;
        $model->save();

        $response['status'] = 'success';
        $response += ['response' => [
            'id' => $model->id,
            'task_id' => $model->task_id,
            'owner' => $model->owner,
            'text' => $model->text,
            'created_at' => $model->created_at
        ]];

        return $response;
    }

    public function actionDelete() {
        $token = Yii::$app->request->getHeaders()['Authorization'];
        $id = Yii::$app->request->get('id');

        $response = [
            'status' => '',
            'request' => [
                'token' => $token,
                'id' => $id
            ],
        ];

        if(!$id) {
            $response['status'] = 'error';
            $response += ['errors' => [
                [
                    'type' => 'id empty',
                    'message' => 'Не передано обязательное поле id'
                ]
            ]];

            return $response;
        }

        $model = $this->commentValidate($id, $token);
        if(isset($model['errors']) && is_array($model)) {
            $response['status'] = 'error';
            $response += $model;
            return $response;
        }

        $model->delete();

        $response['status'] = 'success';
        $response += ['response' => 'Комментарий удалён'];

        return $response;
    }

    public function actionViewAllForTask() {
        $token = Yii::$app->request->getHeaders()['Authorization'];
        $task_id = Yii::$app->request->get('task_id');

        $response = [
            'status' => '',
            'request' => [
                'token' => $token,
                'task_id' => $task_id
            ],
        ];

        if(!$task_id) {
            $response['status'] = 'error';
            $response += ['errors' => [
                [
                    'type' => 'task_id empty',
                    'message' => 'Не передано обязательное поле task_id'
                ]
            ]];

            return $response;
        }

        $model = $this->taskValidate($task_id, $token, false);
        if(isset($model['errors']) && is_array($model)) {
            $response['status'] = 'error';
            $response += $model;
            return $response;
        }

        $response['status'] = 'success';
        $response += ['response' => []];

        $comments = TaskComment::find()->where(['task_id' => $model->id])->andWhere(['owner' => $model->owner])->all();
        if($comments) {
            foreach ($comments as $comment) {
                $response['response'][] = [
                    'id' => $comment->id,
                    'task_id' => $comment->task_id,
                    'owner' => $comment->owner,
                    'text' => $comment->text,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at
                ];
            }
        }

        return $response;
    }

    public function commentValidate($id, $token) {
        $model = TaskComment::findOne($id);
        if(!$model) {
            return ['errors' => [
                [
                    'type' => 'comment invalid',
                    'message' => 'Комментарий не существует'
                ]
            ]];
        }

        $validate = $this->taskValidate($model->task_id, $token);
        if(isset($validate['error']) && is_array($validate)) {
            return $validate;
        }

        return $model;
    }

    public function taskValidate($id, $token, $validate_for_one_comment = true) {
        $validate = $this->tokenValidate($token);
        if(is_array($validate)) {
            return $validate;
        }

        $model = Task::findOne([
            'id' => $id,
            'owner' => $validate
        ]);

        if(!$model) {
            return ['errors' => [
                [
                    'type' => 'id invalid',
                    'message' => $validate_for_one_comment ? 'Комментарий с таким id не принадлежит ни к одной задаче' : 'Задача не найдена'
                ]
            ]];
        }

        return $model;
    }

    public function tokenValidate($token) {
        if(!$token) {
            $response['errors'][] = [
                'type' => 'token empty',
                'message' => 'Не передан заголовок Authorization. Пример: "Authorization: <type> <credentials>"'
            ];
        }

        $token_array = [
            'type' => explode(' ', $token)[0] ?? null,
            'credentials' => explode(' ', $token)[1] ?? null
        ];

        if($token_array['type'] != 'Bearer' || !$token_array['credentials']) {
            $response = ['errors' => []];

            if(!$token_array['Bearer']) {
                $response['errors'][] = [
                    'type' => 'token type invalid',
                    'message' => 'Неверный тип токена авторизации'
                ];
            }

            if(!$token_array['credentials']) {
                $response['errors'][] = [
                    'type' => 'token credentials empty',
                    'message' => 'Токен не содержит реквизитов'
                ];
            }

            return $response;
        }

        $user_token = UserLoginApi::findOne(['token' => $token]);
        if(!$user_token || strtotime($user_token->expire_at) <= time()) {
            $response = ['errors' => []];

            if(!$user_token) {
                $response['errors'][] = [
                    'type' => 'token invalid',
                    'message' => 'Неверный token'
                ];
            }

            if(strtotime($user_token->expire_at) <= time()) {
                $response['errors'][] = [
                    'type' => 'token expired',
                    'message' => 'token истёк'
                ];
            }

            return $response;
        }

        return $user_token->user_id;
    }
}
