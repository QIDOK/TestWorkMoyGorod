<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php',
);

return [
    'id'                  => 'app-api',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'modules'             => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => api\modules\v1\Module::class,
        ]
    ],
    'components'          => [
        'user'         => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager'   => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
                [
                    'class' => yii\rest\UrlRule::class,
                    'controller' => ['v1/auth', 'v1/task'],
                    'prefix' => 'api',
                ],
            ],
        ],
        'formatter' => [
            'dateFormat' => 'Y-m-d H:i:s',
            'timeZone' => 'Europe/Moscow',
        ],
    ],
    'params'              => $params,
];
