<img src="src/icon.svg" width="100" height="100" alt="Sentry Logger">

# Sentry Logger for Craft CMS

Pushes Craft CMS logs to [Sentry](https://sentry.io/) through a native Yii 2 log target.

## Features

- Updated to the latest Sentry SDK version 3
- Calls for `Craft::error()`, `Craft::warning()` are picked up
- Plugin settings can be set in the CP settings or via a plugin config file
- Anonymous option to prevent sensitive visitor and user data from being sent to Sentry

Additional data pushed to Sentry:

- Request type (web or console)
- Request method, headers and body
- Request route including query string
- Script executed including parameters (console request)
- User ID, email, username and groups (sensitive data)
- Visitor cookies (sensitive data)
- Visitor IP address (sensitive data)
- Craft edition, licence, schema and version
- Craft `devMode` status taken from general config
- Craft environment taken from `CRAFT_ENVIRONMENT`
- Twig template path and line number for compiled templates
- Complete stack trace for exception

## Requirements

This plugin requires PHP 7.2 or later and Craft CMS 3.4.0 or later.

## Installation

To install this plugin, search for **Sentry Logger** on the Craft Plugin Store and click **Install** or run the 
following terminal commands in your Craft project folder to install it with Composer:

```bash
composer require diginov/craft-sentry-logger
php craft plugin/install sentry-logger
```

## Basic configuration

You can configure plugin settings directly in the CP or you can create a `config/sentry-logger.php` config file with 
the following contents:

```php
<?php

return [

    '*' => [
        'enabled' => false,
        'anonymous' => false,
        'dsn' => getenv('SENTRY_DSN'),
        'release' => getenv('SENTRY_RELEASE'),
        'levels' => ['error', 'warning'],
        'exceptCodes' => [403, 404],
    ],

    'staging' => [
        'enabled' => true,
    ],

    'production' => [
        'enabled' => true,
    ],

];
```

## Advanced configuration

This method is suggested because it adds this Sentry log target to the existing log component before loading any 
Craft plugins and modules. This way you are assured that all logs are picked up by Sentry.

Please note that with this method, the basic configuration method and the `config/sentry-logger.php` config file are 
useless.

To activate the advanced configuration, add the following `log` component to your existing `config/app.php` config file:

```php
<?php

return [
    
    'components' => [
        'log' => function() {
            $config = craft\helpers\App::logConfig();

            if ($config && class_exists('\diginov\sentry\log\SentryTarget')) {
                $config['targets'][] = [
                    'class' => 'diginov\sentry\log\SentryTarget',
                    'enabled' => CRAFT_ENVIRONMENT != 'dev',
                    'anonymous' => false,
                    'dsn' => getenv('SENTRY_DSN'),
                    'release' => getenv('SENTRY_RELEASE'),
                    'levels' => ['error', 'warning'],
                    'exceptCodes' => [403, 404],
                ];
            }

            return $config ? Craft::createObject($config) : null;
        },
    ],

];
```

## Configuration options

This plugin add a native Yii 2 log target that is an instance of the [yii\log\Target](https://www.yiiframework.com/doc/api/2.0/yii-log-target) 
class. See the [Yii 2 API Documentation](https://www.yiiframework.com/doc/api/2.0/yii-log-target) for all available 
properties.

### `enabled`

This parameter is a boolean that indicates whether this log target is enabled.

### `anonymous`

This parameter is a boolean that indicates whether this log target will hide sensitive visitor and user data.

### `dsn`

This parameter is a string that contain the Client Key (DSN) that Sentry gave you in your [project settings](https://sentry.io/settings/).

### `release`

This optional parameter is a string that contain the version of your code that is deployed to an environment. See more 
information about [releases](https://docs.sentry.io/platforms/php/configuration/releases/) in Sentry documentation.

### `levels`

This parameter is an array of log level names that this log target is interested in. Valid level names include `error` 
and `warning`. We have intentionally disabled reporting `info` log level to Sentry because Craft generates too many 
messages with this level.

### `exceptCodes`

This parameter is an array of HTTP status codes that this log target is not interested in. For example `403` for the
forbidden or `404` for the not found status codes.

## Credits

Inspired by the [olegtsvetkov/yii2-sentry](https://github.com/olegtsvetkov/yii2-sentry) package and by official 
[sentry/sentry-symfony](https://github.com/getsentry/sentry-symfony) and 
[sentry/sentry-laravel](https://github.com/getsentry/sentry-laravel) packages. 
