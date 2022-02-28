<?php

namespace diginov\sentrylogger\controllers;

use Craft;
use yii\web\Response;

class TestController extends \craft\web\Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Send test data to Sentry.
     */
    public function actionIndex(): Response
    {
        Craft::error(Craft::t('sentry-logger', 'This is an error test'), 'Test');
        Craft::warning(Craft::t('sentry-logger', 'This is a warning test'), 'Test');

        Craft::$app->getSession()->setNotice(Craft::t('sentry-logger', 'Test data created.'));

        return $this->redirectToPostedUrl();
    }
}
