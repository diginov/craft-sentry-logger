<?php

namespace diginov\sentry\log;

use Craft;
use craft\helpers\App;

use Sentry;
use Sentry\Severity;
use Sentry\State\Scope;

use Twig\Environment as TwigEnvironment;

use Yii;
use yii\helpers\VarDumper;
use yii\i18n\PhpMessageSource;
use yii\log\Logger;
use yii\web\HttpException;

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
     * @var string
     */
    public $environment;

    /**
     * @var array
     */
    public $exceptCodes = [403, 404];

    /**
     * @var array
     */
    public $exceptPatterns = [];

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

        if (is_string($this->except)) {
            $this->except = explode(',', $this->except);
            $this->except = array_map('trim', $this->except);
        }

        if (is_string($this->exceptCodes)) {
            $this->exceptCodes = explode(',', $this->exceptCodes);
            $this->exceptCodes = array_map('trim', $this->exceptCodes);
        }

        if (is_string($this->exceptPatterns)) {
            $this->exceptPatterns = explode(',', $this->exceptPatterns);
            $this->exceptPatterns = array_map('trim', $this->exceptPatterns);
        }

        $except = [
            PhpMessageSource::class . ':*',
        ];

        foreach ($this->exceptCodes as $exceptCode) {
            if (preg_match('/[0-9]{3}/', $exceptCode)) {
                $except[] = HttpException::class . ':' . $exceptCode;
            }
        }

        $this->except = array_unique(array_merge($this->except, $except));

        $options = [
            'dsn'                  => $this->dsn ?: null,
            'release'              => $this->release ?: null,
            'environment'          => $this->environment ?: CRAFT_ENVIRONMENT,
            'context_lines'        => 10,
            'send_default_pii'     => !$this->anonymous,
            'default_integrations' => true,

            'integrations' => function (array $integrations) {
                return self::getIntegrations($integrations);
            },
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

        foreach ($messages as $message) {
            Sentry\withScope(function(Scope $scope) use ($user, $groups, $request, $message) {
                [$message, $level, $category, $timestamp, $traces, $memory] = $message;

                foreach($this->exceptPatterns as $exceptPattern) {
                    if (!empty($exceptPattern) && @preg_match("~{$exceptPattern}~", $message) === 1) {
                        return;
                    }
                }

                $scope->setTag('app.name', Craft::$app->getSystemName());

                if (!empty($category)) {
                    $scope->setTag('category', $category);
                }

                if ($user && !$this->anonymous) {
                    $scope->setUser([
                        'email'      => $user->email,
                        'id'         => $user->id,
                        'ip_address' => $request->getRemoteIP(),
                        'username'   => $user->username,
                        'Admin'      => $user->admin ? 'Yes' : 'No',
                        'Groups'     => $groups ? implode(', ', $groups) : null,
                    ]);
                }

                $scope->setExtras([
                    'App Name'      => Craft::$app->getSystemName(),
                    'Craft Edition' => App::editionName(Craft::$app->getEdition()),
                    'Craft Schema'  => Craft::$app->getInstalledSchemaVersion(),
                    'Craft Version' => Craft::$app->getVersion(),
                    'Dev Mode'      => Craft::$app->getConfig()->getGeneral()->devMode ? 'Yes' : 'No',
                    'Environment'   => CRAFT_ENVIRONMENT,
                    'PHP Version'   => App::phpVersion(),
                    'Request Type'  => $request->getIsConsoleRequest() ? 'Console' : ($request->getIsAjax() ? 'Ajax' : 'Web'),
                    'Twig Version'  => TwigEnvironment::VERSION,
                    'Yii Version'   => Yii::getVersion(),
                ]);

                if ($request->getIsConsoleRequest()) {
                    try {
                        $scriptFile = $request->getScriptFile() . ' ';
                    } catch(\Throwable $e) {
                        $scriptFile = '';
                    }

                    $scope->setExtra('Request Command', $scriptFile . implode(' ', $request->getParams()));
                } else {
                    $scope->setExtra('Request Route', $request->getAbsoluteUrl());
                }

                try {
                    $db = Craft::$app->getDb();
                    $dbVersion = App::normalizeVersion($db->getSchema()->getServerVersion());

                    if ($db->getIsMysql()) {
                        $scope->setExtra('MySQL Version', $dbVersion);
                    } else {
                        $scope->setExtra('PostgreSQL Version', $dbVersion);
                    }
                } catch (\Throwable $e) {
                    // Database is unavailable. Continue and send original error...
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
            foreach ($levels as $key => $level) {
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
     * Returns Sentry Integrations to be loaded.
     *
     * @param Sentry\Integration\IntegrationInterface[] $integrations
     * @return Sentry\Integration\IntegrationInterface[]
     */
    protected function getIntegrations(array $integrations): array
    {
        $integrations[] = new CraftIntegration();

        return array_filter($integrations, static function (\Sentry\Integration\IntegrationInterface $integration): bool {
            return !$integration instanceof \Sentry\Integration\AbstractErrorListenerIntegration;
        });
    }

    /**
     * Translates log level to Sentry Severity level.
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
