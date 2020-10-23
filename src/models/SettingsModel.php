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
     * @var string
     */
    public $dsn;

    /**
     * @var string
     */
    public $release;

    /**
     * @var array
     */
    public $levels = ['error', 'warning'];

    /**
     * @var array
     */
    public $exceptCodes = [403, 404];

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['dsn', 'release'],
                'trim',
            ],[
                ['enabled', 'anonymous'],
                'filter',
                'filter' => function($value) {
                    return (bool) $value;
                },
            ],[
                ['levels', 'exceptCodes'],
                'filter',
                'filter' => function($value) {
                    return is_string($value) ? explode(',', $value) : $value;
                },
            ],[
                ['levels', 'exceptCodes'],
                'filter',
                'filter' => function($value) {
                    return is_array($value) ? array_map('trim', $value) : $value;
                },
            ],[
                'dsn',
                'required',
                'message' => Craft::t('yii', '{attribute} cannot be blank.', [
                    'attribute' => 'Client Key (DSN)'
                ]),
            ],[
                'dsn',
                function($attribute, $params, $validator) {
                    try {
                        Dsn::createFromString(Craft::parseEnv($this->$attribute));
                    } catch (\Throwable $e) {
                        $this->addError($attribute, Craft::t('yii', '{attribute} is invalid.', [
                            'attribute' => Craft::t('sentry-logger', 'Client Key (DSN)')
                        ]));
                    }
                }
            ],[
                'exceptCodes',
                function($attribute, $params, $validator) {
                    foreach($this->$attribute as $value) {
                        if (!empty($value) && (!is_numeric($value) || strlen($value) != 3)) {
                            $this->addError($attribute, Craft::t('yii', '{attribute} is invalid.', [
                                'attribute' => Craft::t('sentry-logger', 'Excluded HTTP status codes')
                            ]));
                            break;
                        }
                    }
                },
            ],[
                'exceptCodes',
                'filter',
                'filter' => function($value) {
                    $value = array_unique($value);

                    if (count($value) > 1) {
                        $value = array_filter($value);
                        sort($value);
                    }

                    return $value;
                },
            ],
        ];
    }
}
