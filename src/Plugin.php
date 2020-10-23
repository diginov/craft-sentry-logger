<?php

namespace diginov\sentry;

use diginov\sentry\log\SentryTarget;
use diginov\sentry\models\SettingsModel;

use Craft;
use craft\helpers\ArrayHelper;

use yii\base\InvalidConfigException;

class Plugin extends \craft\base\Plugin
{
    // Public Properties
    // =========================================================================

    /**
     * @inheritdoc
     */
    public $schemaVersion = '1.0.0';

    /**
     * @inheritdoc
     */
    public $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public $hasCpSection = false;

    // Protected Properties
    // =========================================================================

    /**
     * @var bool
     */
    protected $isAdvancedConfig = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        $dispatcher = Craft::getLogger()->dispatcher;

        if ($dispatcher instanceof \yii\log\Dispatcher) {
            foreach($dispatcher->targets as $target) {
                if ($target instanceof SentryTarget) {
                    $this->isAdvancedConfig = true;
                    break;
                }
            }

            if (!$this->isAdvancedConfig) {
                $settings = $this->getSettings();

                if ($settings->validate()) {
                    $target = ArrayHelper::merge($settings->toArray(), ['class' => SentryTarget::class]);
                    $target['dsn'] = Craft::parseEnv($target['dsn']);
                    $target['release'] = Craft::parseEnv($target['release']);
                    $dispatcher->targets[] = Craft::createObject($target);
                }
            }
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new SettingsModel();
    }

    /**
     * @inheritdoc
     * @throws \Twig\Error\Error
     * @throws \yii\base\Exception
     */
    protected function settingsHtml()
    {
        $settings = $this->getSettings();
        $overrides = Craft::$app->getConfig()->getConfigFromFile($this->handle);

        return Craft::$app->getView()->renderTemplate($this->handle.'/settings', [
            'plugin' => $this,
            'settings' => $settings,
            'overrides' => $overrides,
            'isAdvancedConfig' => $this->isAdvancedConfig,
        ]);
    }
}
