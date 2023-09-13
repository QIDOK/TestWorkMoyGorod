<?php

namespace api\modules\v1\controllers;

use common\models\Task;
use common\models\UserLoginApi;
use Yii;
use yii\rest\ActiveController;

class TaskController extends ActiveController
{
    public $modelClass = "common\models\Task";

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

    public function actionViewAll() {
        $token = Yii::$app->request->getHeaders()['Authorization'];

        $response = [
            'status' => '',
            'request' => [
                'token' => $token,
            ],
        ];

        $validate = $this->tokenValidate($token);
        if(is_array($validate)) {
            $response['status'] = 'error';
            $response += $validate;
            return $response;
        }

        $tasks = Task::find()->where(['owner' => $validate])->all();

        $response['status'] = 'success';
        $response += ['response' => []];

        if($tasks) {
            foreach ($tasks as $task) {
                $response['response'][] = [
                    'id' => $task->id,
                    'owner_id' => $task->owner,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => [
                        'code' => $task->status,
                        'name' => Task::TASK_STATUSES[$task->status]
                    ],
                    'is_active' => [
                        'code' => $task->is_active,
                        'name' => Task::TASK_ACTIVITIES[$task->is_active]
                    ],
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at
                ];
            }
        }

        return $response;
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

        $model = $this->taskValidate($id, $token);
        if(isset($model['error']) && is_array($model)) {
            $response['status'] = 'error';
            $response += $model;
            return $response;
        }

        $response['status'] = 'success';
        $response += ['response' => [
            'id' => $model->id,
            'owner_id' => $model->owner,
            'title' => $model->title,
            'description' => $model->description,
            'status' => [
                'code' => $model->status,
                'name' => Task::TASK_STATUSES[$model->status]
            ],
            'is_active' => [
                'code' => $model->is_active,
                'name' => Task::TASK_ACTIVITIES[$model->is_active]
            ],
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
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

        $model = $this->taskValidate($id, $token);
        if(isset($model['error']) && is_array($model)) {
            $response['status'] = 'error';
            $response += $model;
            return $response;
        }

        $model->delete();

        $response['status'] = 'success';
        return $response + ['response' => 'Задача удалена'];
    }

    public function actionCreate() {
        $token = Yii::$app->request->getHeaders()['Authorization'];
        $title = Yii::$app->request->get('title');
        $description = Yii::$app->request->get('description');
        $status = Yii::$app->request->get('status');
        $is_active = Yii::$app->request->get('is_active');

        $response = [
            'status' => '',
            'request' => [
                'token' => $token,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'is_active' => $is_active,
            ],
        ];

        if(!$title || $status === null || $is_active === null) {
            $response += ['errors' => []];
            $response['status'] = 'error';

            if(!$title) {
                $response['errors'] += [[
                    'type' => 'title empty',
                    'message' => 'Не передано обязательное поле title'
                ]];
            }

            if($status === null) {
                $response['errors'] += [[
                    'type' => 'status empty',
                    'message' => 'Не передано обязательное поле status'
                ]];
            }

            if($is_active === null) {
                $response['errors'] += [[
                    'type' => 'is_active empty',
                    'message' => 'Не передано обязательное поле is_active'
                ]];
            }

            return $response;
        }

        if(strlen($title) > 128 || strlen($title) < 1) {
            $response['status'] = 'error';
            $response += ['errors'=> [
                [
                    'type' => 'invalid title size',
                    'message' => 'Заголовок не может быть короче 1 или длиннее 128 символов'
                ]
            ]];

            return $response;
        }

        if(!isset(Task::TASK_ACTIVITIES[$is_active])) {
            $response['status'] = 'error';
            $response += ['errors'=> [
                [
                    'type' => 'invalid is_active value',
                    'message' => 'Дозволенные значения для is_active: 0 - Не активна, 1 - Активна'
                ]
            ]];

            return $response;
        }

        if(!isset(Task::TASK_STATUSES[$status])) {
            $response['status'] = 'error';
            $response += ['errors'=> [
                [
                    'type' => 'invalid status value',
                    'message' => 'Дозволенные значения для status: 0 - Новая, 1 - В работе, 2 - На тестировании, 3 - На доработке, 4 - Завершена, 5 - Отменена'
                ]
            ]];

            return $response;
        }

        $validate = $this->tokenValidate($token);
        if(is_array($validate)) {
            $response['status'] = 'error';
            $response += $validate;
            return $response;
        }

        $model = new Task([
            'owner' => $validate,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'is_active' => $is_active
        ]);

        $model->save();

        $response['status'] = 'success';
        $response += ['response' => [
            'id' => $model->id,
            'owner_id' => $model->owner,
            'title' => $model->title,
            'description' => $model->description,
            'status' => [
                'code' => $model->status,
                'name' => Task::TASK_STATUSES[$model->status]
            ],
            'is_active' => [
                'code' => $model->is_active,
                'name' => Task::TASK_ACTIVITIES[$model->is_active]
            ]
        ]];

        return $response;
    }

    public function actionUpdate() {
        $token = Yii::$app->request->getHeaders()['Authorization'];
        $id = Yii::$app->request->get('id');
        $title = Yii::$app->request->get('title');
        $description = Yii::$app->request->get('description');
        $status = Yii::$app->request->get('status');
        $is_active = Yii::$app->request->get('is_active');

        $response = [
            'status' => '',
            'request' => [
                'token' => $token,
                'id' => $id,
            ],
        ];

        $response['request'] += $title ? ['title' => $title] : [];
        $response['request'] += $description ? ['description' => $description] : [];
        $response['request'] += $status ? ['status' => $status] : [];
        $response['request'] += $is_active ? ['is_active' => $is_active] : [];

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

        if(!$title && !$description && $status === null && $is_active === null) {
            $response += ['errors' => [
                [
                    'type' => 'all fields is empty',
                    'message' => 'Требуется передать хотя бы один параметр'
                ]
            ]];
            $response['status'] = 'error';

            return $response;
        }

        if(strlen($title) > 128 || strlen($title) < 1) {
            $response['status'] = 'error';
            $response += ['errors'=> [
                [
                    'type' => 'invalid title size',
                    'message' => 'Заголовок не может быть короче 1 или длиннее 128 символов'
                ]
            ]];

            return $response;
        }

        if($is_active !== null && !isset(Task::TASK_ACTIVITIES[$is_active])) {
            $response['status'] = 'error';
            $response += ['errors'=> [
                [
                    'type' => 'invalid is_active value',
                    'message' => 'Дозволенные значения для is_active: 0 - Не активна, 1 - Активна'
                ]
            ]];

            return $response;
        }

        if($status !== null && !isset(Task::TASK_STATUSES[$status])) {
            $response['status'] = 'error';
            $response += ['errors'=> [
                [
                    'type' => 'invalid status value',
                    'message' => 'Дозволенные значения для status: 0 - Новая, 1 - В работе, 2 - На тестировании, 3 - На доработке, 4 - Завершена, 5 - Отменена'
                ]
            ]];

            return $response;
        }

        $model = $this->taskValidate($id, $token);
        if(isset($model['error']) && is_array($model)) {
            $response['status'] = 'error';
            $response += $model;
            return $response;
        }

        $model->title = $title ?? $model->title;
        $model->description = $description ?? $model->description;
        $model->status = $status ?? $model->status;
        $model->is_active = $is_active ?? $model->is_active;
        $model->save();

        $response['status'] = 'success';
        $response += ['response' => [
            'id' => $model->id,
            'owner_id' => $model->owner,
            'title' => $model->title,
            'description' => $model->description,
            'status' => [
                'code' => $model->status,
                'name' => Task::TASK_STATUSES[$model->status]
            ],
            'is_active' => [
                'code' => $model->is_active,
                'name' => Task::TASK_ACTIVITIES[$model->is_active]
            ],
            'created_at' => $model->created_at
        ]];

        return $response;
    }

    public function taskValidate($id, $token) {
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
                    'message' => 'Задача не существует'
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
