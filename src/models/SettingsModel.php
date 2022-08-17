<?php

namespace diginov\sentry\models;

use Craft;
use Sentry\Dsn;

class SettingsModel extends \craft\base\Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var bool
     */
    public $enabled = true;

    /**
     * @var bool
     */
    public $anonymous = false;

    /**
     * @var string|null
     */
    public $dsn = null;

    /**
     * @var string|null
     */
    public $release = null;

    /**
     * @var string|null
     */
    public $environment = null;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var array
     */
    public $levels = ['error', 'warning'];

    /**
     * @var array
     */
    public $exceptCodes = [403, 404];

    /**
     * @var array
     */
    public $exceptPatterns = [];

    /**
     * @var array
     */
    public $userPrivacy = ['id', 'email', 'username', 'ip_address', 'cookies', 'permissions'];

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [
                ['dsn', 'release', 'environment'],
                'trim',
            ],[
                ['anonymous', 'enabled'],
                'filter',
                'filter' => function($value): bool {
                    return (bool) $value;
                },
            ],[
                ['levels', 'exceptCodes', 'exceptPatterns', 'userPrivacy'],
                'filter',
                'filter' => function($value): array {
                    if (isset($value[0]) && count($value) === 1) {
                        $value = explode(',', $value[0]);
                    }

                    $value = array_unique($value);
                    $value = array_map('trim', $value);
                    sort($value);

                    return $value;
                },
            ],[
                ['levels', 'userPrivacy'],
                'filter',
                'filter' => function($value): array {
                    $value = array_filter($value);
                    sort($value);

                    return $value;
                },
            ],[
                ['dsn', 'environment', 'release'],
                'default',
                'value' => null,
            ],[
                'dsn',
                'required',
                'message' => Craft::t('yii', '{attribute} cannot be blank.', [
                    'attribute' => Craft::t('sentry-logger', 'Client Key (DSN)'),
                ]),
            ],[
                'dsn',
                function($attribute, $params, $validator) {
                    try {
                        Dsn::createFromString(Craft::parseEnv($this->$attribute));
                    } catch (\Throwable $e) {
                        $this->addError($attribute, Craft::t('yii', '{attribute} is invalid.', [
                            'attribute' => Craft::t('sentry-logger', 'Client Key (DSN)'),
                        ]));
                    }
                }
            ],[
                'userPrivacy',
                'required',
                'message' => Craft::t('yii', '{attribute} cannot be blank.', [
                    'attribute' => Craft::t('sentry-logger', 'User privacy settings'),
                ]),
            ],[
                'levels',
                'required',
                'message' => Craft::t('yii', '{attribute} cannot be blank.', [
                    'attribute' => Craft::t('sentry-logger', 'Included log levels'),
                ]),
            ],[
                'exceptCodes',
                function($attribute, $params, $validator) {
                    foreach($this->$attribute as $value) {
                        if (!empty($value) && !preg_match('/^[1-5][0-9]{2}$/', $value)) {
                            $this->addError($attribute, Craft::t('yii', '{attribute} is invalid.', [
                                'attribute' => Craft::t('sentry-logger', 'Excluded HTTP status codes'),
                            ]));
                            break;
                        }
                    }
                },
            ],[
                'exceptPatterns',
                function($attribute, $params, $validator) {
                    foreach($this->$attribute as $value) {
                        if (@preg_match("~{$value}~", null) === false) {
                            $this->addError($attribute, Craft::t('yii', '{attribute} is invalid.', [
                                'attribute' => Craft::t('sentry-logger', 'Excluded search patterns'),
                            ]));
                            break;
                        }
                    }
                }
            ],
        ];
    }
}
