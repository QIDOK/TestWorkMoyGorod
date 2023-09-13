<?php

namespace api\modules\v1\controllers;

use common\models\LoginForm;
use common\models\Task;
use common\models\User;
use common\models\UserLoginApi;
use Yii;
use yii\db\Expression;
use yii\rest\ActiveController;

class AuthController extends ActiveController
{
    public $modelClass = "common\models\User";

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

    public function actionRegister() {
        $username = Yii::$app->request->post('username');
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('password');

        $response = [
            'status' => '',
            'request' => [
                'username' => $username,
                'email' => $email,
                'password' => $password
            ],
        ];

        if(!$username || !$email || !$password) {
            $response += ['errors' => []];
            $response['status'] = 'error';

            if(!$username) {
                $response['errors'][] = [
                    'type' => 'username empty',
                    'message' => 'Не передано обязательное поле username'
                ];
            }

            if(!$email) {
                $response['errors'][] = [
                    'type' => 'email empty',
                    'message' => 'Не передано обязательное поле email'
                ];
            }

            if(!$password) {
                $response['errors'][] = [
                    'type' => 'password empty',
                    'message' => 'Не передано обязательное поле password'
                ];
            }

            return $response;
        }

        if($users = User::find()->where(['username' => $username])->orWhere(['email' => $email])->all()) {
            $response += ['errors' => []];
            $response['status'] = 'error';
            foreach ($users as $user) {
                $response['errors'][] = [
                    'type' => $user->username == $username ? 'username exist' : ($user->email == $email ? 'email exist' : ''),
                    'message' => $user->username == $username ? 'Данное имя пользователя уже используется' : ($user->email == $email ? 'Данная электронная почта уже используется' : '')
                ];
            }

            return $response;
        }

        $user = new User();
        $user->username = $username;
        $user->status = 10;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->save();

        $api = new UserLoginApi([
            'user_id' => $user->id,
            'token' => $this->generateToken(),
            'expire_at' => date('Y-m-d H:i:s', time() + 3600)
        ]);
        $api->save();

        $response['status'] = 'success';
        $response += ['response' => [
            'token' => $api->token,
            'expire_at' => $api->expire_at
        ]];

        return $response;
    }

    public function actionLogin() {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');

        $response = [
            'status' => '',
            'request' => [
                'username' => $username,
                'password' => $password
            ],
        ];

        if(!$username || !$password) {
            $response += ['errors' => []];
            $response['status'] = 'error';

            if(!$username) {
                $response['errors'][] = [
                    'type' => 'username empty',
                    'message' => 'Не передано обязательное поле username'
                ];
            }

            if(!$password) {
                $response['errors'][] = [
                    'type' => 'password empty',
                    'message' => 'Не передано обязательное поле password'
                ];
            }

            return $response;
        }

        $model = new LoginForm([
            'username' => $username,
            'password' => $password
        ]);
        $user = User::findByUsername($username);
        if (!$user || !$user->validatePassword($model->password)) {
            $response += ['errors' => [
                'type' => 'username or password invalid',
                'message' => 'Неправильное имя пользователя и/или пароль'
            ]];
            $response['status'] = 'error';

            return $response;
        }

        $api = UserLoginApi::find()->where(['user_id' => $user->id])->andWhere(['>', 'expire_at', new Expression("NOW()")])->one();

        if(!$api) {
            $api = new UserLoginApi([
                'user_id' => $user->id,
                'token' => $this->generateToken(),
                'expire_at' => date('Y-m-d H:i:s', time() + 3600)
            ]);

            $api->save();
        }

        $response['status'] = 'success';
        $response += ['response' => [
            'token' => $api->token,
            'expire_at' => $api->expire_at
        ]];

        return $response;
    }

    public function generateToken(): string
    {
        $token = 'Bearer ';
        $symbols = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));

        for ($i = 0; $i < 32; $i++) {
            $token .= $symbols[rand(0, count($symbols)-1)];
        }

        return $token;
    }
}
