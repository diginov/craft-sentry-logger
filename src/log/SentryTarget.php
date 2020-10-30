<?php

namespace diginov\sentry\log;

use Craft;

use Sentry;
use Sentry\Integration\RequestIntegration;
use Sentry\Integration\TransactionIntegration;
use Sentry\Integration\FrameContextifierIntegration;
use Sentry\Integration\EnvironmentIntegration;
use Sentry\Severity;
use Sentry\State\Scope;

use yii\helpers\VarDumper;
use yii\log\Logger;

class SentryTarget extends \yii\log\Target
{
    // Public Properties
    // =========================================================================

    /**
     * @var boolean
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
    public $exceptCodes = [403, 404];

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->enabled || !$this->dsn || !$this->levels) {
            return;
        }

        $this->except[] = 'yii\i18n\PhpMessageSource:*';

        if (is_array($this->exceptCodes)) {
            foreach($this->exceptCodes as $exceptCode) {
                $this->except[] = 'yii\web\HttpException:'.$exceptCode;
            }
        }

        $options = [
            'dsn'                  => $this->dsn ?: null,
            'release'              => $this->release ?: null,
            'environment'          => CRAFT_ENVIRONMENT,
            'context_lines'        => 10,
            'send_default_pii'     => !$this->anonymous,
            'default_integrations' => false,

            'integrations' => [
                new CraftIntegration(),
                new RequestIntegration(),
                new TransactionIntegration(),
                new FrameContextifierIntegration(),
                new EnvironmentIntegration(),
            ],
        ];

        Sentry\init($options);
    }

    /**
     * @inheritdoc
     */
    public function export()
    {
        if (!$this->enabled || !$this->dsn || !$this->levels) {
            return;
        }

        $user = Craft::$app->getUser()->getIdentity();
        $groups = [];

        if ($user) {
            $groups = array_map(function($group) {
                return $group->name;
            }, $user->getGroups());
        }

        $request = Craft::$app->getRequest();
        $messages = static::filterMessages($this->messages, $this->getLevels(), $this->categories, $this->except);

        foreach($messages as $message) {
            Sentry\withScope(function(Scope $scope) use ($user, $groups, $request, $message) {
                [$message, $level, $category, $timestamp, $traces, $memory] = $message;

                if (!empty($category)) {
                    $scope->setTag('category', $category);
                }

                if ($user && !$this->anonymous) {
                    $scope->setUser([
                        'id'         => $user->id,
                        'ip_address' => $request->getRemoteIP(),
                        'username'   => $user->username,
                        'email'      => $user->email,
                        'Admin'      => $user->admin ? 'Yes' : 'No',
                        'Groups'     => $groups ? implode(', ', $groups) : null,
                    ]);
                }

                $scope->setExtras([
                    'App Name'      => Craft::$app->getSystemName(),
                    'Craft Edition' => Craft::$app->getEditionName() ?? 'Solo',
                    'Craft Licence' => Craft::$app->getLicensedEditionName(),
                    'Craft Schema'  => Craft::$app->getInstalledSchemaVersion(),
                    'Craft Version' => Craft::$app->getVersion(),
                    'Dev Mode'      => Craft::$app->getConfig()->getGeneral()->devMode ? 'Yes' : 'No',
                    'PHP Version'   => phpversion(),
                    'Request Type'  => $request->getIsConsoleRequest() ? 'Console' : 'Web',
                    'Yii Version'   => Craft::$app->getYiiVersion(),
                ]);

                if ($request->getIsConsoleRequest()) {
                    try {
                        $scriptFile = $request->getScriptFile() . ' ';
                    } catch(\Throwable $e) {
                        $scriptFile = '';
                    }

                    $scope->setExtra('Request Route', $scriptFile . implode(' ', $request->getParams()));
                } else {
                    $scope->setExtra('Request Route', $request->getAbsoluteUrl());
                }

                if ($message instanceof \Throwable) {
                    Sentry\captureException($message);
                } else {
                    if (!is_string($message)) {
                        $message = VarDumper::export($message);
                    }

                    Sentry\captureMessage($message, $this->getSeverityLevel($level));
                }
            });
        }
    }

    /**
     * @inheritdoc
     */
    public function setLevels($levels)
    {
        if (is_array($levels)) {
            foreach($levels as $key => $level) {
                if (!in_array($level, ['error', 'warning'])) {
                    unset($levels[$key]);
                }
            }
        }

        parent::setLevels($levels);
    }

    // Protected Methods
    // =========================================================================

    /**
     * Translates log level to Sentry Severity.
     *
     * @param int $level
     * @return Severity
     */
    protected function getSeverityLevel(int $level)
    {
        switch($level) {
            case Logger::LEVEL_PROFILE_END:
            case Logger::LEVEL_PROFILE_BEGIN:
            case Logger::LEVEL_PROFILE:
            case Logger::LEVEL_TRACE:
                return Severity::debug();

            case Logger::LEVEL_INFO:
                return Severity::info();

            case Logger::LEVEL_WARNING:
                return Severity::warning();

            case Logger::LEVEL_ERROR:
                return Severity::error();

            default:
                throw new \UnexpectedValueException("An unsupported log level \"{$level}\" given.");
        }
    }
}
