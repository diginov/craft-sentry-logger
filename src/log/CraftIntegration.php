<?php

namespace diginov\sentry\log;

use Craft;
use craft\helpers\StringHelper;

use Sentry\Event;
use Sentry\SentrySdk;
use Sentry\Stacktrace;
use Sentry\State\Scope;

final class CraftIntegration implements \Sentry\Integration\IntegrationInterface
{
    /**
     * @inheritdoc
     */
    public function setupOnce(): void
    {
        Scope::addGlobalEventProcessor(static function(Event $event): Event {
            $client = SentrySdk::getCurrentHub()->getClient();

            if (null === $client) {
                return $event;
            }

            $integration = $client->getIntegration(self::class);

            if (null === $integration) {
                return $event;
            }

            $stacktrace = $event->getStacktrace();

            if (null !== $stacktrace) {
                $integration->addTwigTemplateToStacktraceFrames($stacktrace);
            }

            foreach ($event->getExceptions() as $exception) {
                if (null !== $exception->getStacktrace()) {
                    $integration->addTwigTemplateToStacktraceFrames($exception->getStacktrace());
                }
            }

            return $event;
        });
    }

    /**
     * Add source Twig template to stack trace frames vars when Twig template is compiled
     *
     * @param Stacktrace $stacktrace
     */
    private function addTwigTemplateToStacktraceFrames(Stacktrace $stacktrace)
    {
        $compiledTemplatesPath = Craft::$app->getPath()->getCompiledTemplatesPath(false);

        if (!$compiledTemplatesPath) {
            return;
        }

        foreach($stacktrace->getFrames() as $frame) {
            if ($frame->isInternal() || null === $frame->getAbsoluteFilePath()) {
                continue;
            }

            if (StringHelper::startsWith($frame->getAbsoluteFilePath(), $compiledTemplatesPath)) {
                $content = file_get_contents($frame->getAbsoluteFilePath());

                if (preg_match('/return new Source\(".*", ".*", "(.*)"\);/', $content, $matches)) {
                    $sourceFile = $matches[1] ?? null;

                    if ($sourceFile && file_exists($sourceFile)) {
                        $lineNumber = null;

                        try {
                            $file = new \SplFileObject($frame->getAbsoluteFilePath());
                            $file->seek($frame->getLine() - 1);

                            $seek = 0;
                            while($seek < 10) {
                                $lineContent = $file->current();

                                if (preg_match('/line (\d+)$/', $lineContent, $matches)) {
                                    $lineNumber = $matches[1] ?? null;
                                    break;
                                }

                                $key = $file->key();

                                if ($key > 0) {
                                    --$key;
                                    $file->seek($key);
                                }

                                $seek++;
                            }
                        } catch (\Throwable $e) {
                            $lineNumber = null;
                        }

                        $vars = $frame->getVars();
                        $vars['template'] = $sourceFile;

                        if ($lineNumber) {
                            $vars['template'] .= " (line {$lineNumber})";
                        }

                        $frame->setVars($vars);
                    }
                }
            }
        }
    }
}
