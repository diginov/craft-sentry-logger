<?php

namespace diginov\sentry;

use diginov\sentry\log\SentryTarget;
use diginov\sentry\models\SettingsModel;

use Craft;
use craft\base\Model;
use craft\helpers\ArrayHelper;

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
     */
    public function init(): void
    {
        parent::init();

        $dispatcher = Craft::$app->getLog();

        foreach ($dispatcher->targets as $target) {
            if ($target instanceof SentryTarget) {
                $this->isAdvancedConfig = true;
                break;
            }
        }

        if (!$this->isAdvancedConfig) {
            $settings = $this->getSettings();

            if ($settings && $settings->validate()) {
                $target = ArrayHelper::merge($settings->toArray(), ['class' => SentryTarget::class]);
                $target['dsn'] = Craft::parseEnv($target['dsn']);
                $target['release'] = Craft::parseEnv($target['release']);
                $target['environment'] = Craft::parseEnv($target['environment']);
                $dispatcher->targets['__craftSentryTarget'] = Craft::createObject($target);
            }
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
    {
        return new SettingsModel();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): ?string
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
