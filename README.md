<img src="src/icon.svg" width="100" alt="Sentry Logger Icon">

# Sentry Logger for Craft CMS

Pushes Craft CMS logs to [Sentry](https://sentry.io/) through a native Yii 2 log target.

<img src="screenshot.png" width="500" alt="Sentry Logger Screenshot">

## Features

- Updated to the latest Sentry SDK version 3
- Native Yii 2 log target that is fully customisable
- All errors and warnings for each request are sent 
- Plugin settings can be defined in the CP or with a config file
- Calls for `Craft::error()` and `Craft::warning()` are sent and categorized
- Anonymous option to prevent sensitive visitor and user data from being sent to Sentry

Additional data pushed to Sentry:

- Request type (web, ajax or console)
- Request method, headers and body
- Request route including query string
- Script executed including parameters (console request)
- User ID, email, username and groups (sensitive data)
- Visitor cookies (sensitive data)
- Visitor IP address (sensitive data)
- Craft edition, licence, schema and version
- Craft `devMode` status taken from general config
- Craft current environment taken from `CRAFT_ENVIRONMENT`
- Twig template path and line number for exceptions in compiled templates

## Requirements

This plugin requires PHP 7.2 or later and Craft CMS 3.5 or later.

## Installation

To install this plugin, search for **Sentry Logger** on the Craft Plugin Store and click **Install** or run the 
following terminal commands in your Craft project folder to install it with Composer:

```bash
composer require diginov/craft-sentry-logger
php craft plugin/install sentry-logger
```

## Basic configuration

You can configure the plugin settings directly in the CP or you can create a `config/sentry-logger.php` config file 
with the following contents:

```php
<?php

use craft\helpers\App;

return [

    '*' => [
        'enabled' => false,
        'anonymous' => false,
        'dsn' => App::env('SENTRY_DSN'),
        'release' => App::env('SENTRY_RELEASE'),
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

This is a better method because it adds this Sentry log target to the existing log component before loading any Craft 
plugins and modules. This way you are assured that all logs are sent to Sentry.

Please note that this method bypass the basic configuration method and the `config/sentry-logger.php` config file.

To activate the advanced configuration, add the following `log` component to your existing `config/app.php` config file:

```php
<?php

use craft\helpers\App;

return [
    
    'components' => [
        'log' => function() {
            $config = App::logConfig();

            if ($config && class_exists('\diginov\sentry\log\SentryTarget')) {
                $config['targets'][] = [
                    'class' => 'diginov\sentry\log\SentryTarget',
                    'enabled' => CRAFT_ENVIRONMENT != 'dev',
                    'anonymous' => false,
                    'dsn' => App::env('SENTRY_DSN'),
                    'release' => App::env('SENTRY_RELEASE'),
                    'levels' => ['error', 'warning'],
                    'exceptCodes' => [403, 404],
                ];
            }

            return $config ? Craft::createObject($config) : null;
        },
    ],

];
```

## Configuration parameters

This plugin adds a native Yii 2 log target that is an instance of the [yii\log\Target](https://www.yiiframework.com/doc/api/2.0/yii-log-target) 
class. See the [Yii 2 API Documentation](https://www.yiiframework.com/doc/api/2.0/yii-log-target) for all available 
properties.

### `enabled`

This parameter is a boolean that indicates whether this log target is enabled.

### `anonymous`

This parameter is a boolean that indicates whether this log target will NOT send sensitive visitor and user data to 
Sentry.

### `dsn`

This parameter is a string that contain the Client Key (DSN) that Sentry gave you in your [project settings](https://sentry.io/settings/).

### `release`

This parameter is a string that contain the version of your code that is deployed to an environment. See more 
information about [releases](https://docs.sentry.io/platforms/php/configuration/releases/) in Sentry documentation.

### `levels`

This parameter is an array of log level names that this log target is interested in. Defaults to `error` and `warning`. 
We have intentionally disabled reporting `info` log level to Sentry because Craft generates a lot of messages for this 
log level.

### `categories`

This parameter is an array of message categories that this log target is interested in. Defaults to empty, meaning all 
categories. You can use an asterisk at the end of a category so that the category may be used to match those categories 
sharing the same common prefix. For example, `yii\db*` will match categories starting with `yii\db\`, such as 
`yii\db\Connection`.

### `except`

This parameter is an array of message categories that this log target is NOT interested in. Defaults to empty, meaning 
no uninteresting messages. If this property is not empty, then any category listed here will be excluded from the 
`categories` parameter. You can use an asterisk at the end of a category so that the category can be used to match 
those categories sharing the same common prefix. For example, `yii\db*` will match categories starting with `yii\db\`, 
such as `yii\db\Connection`.

### `exceptCodes`

This parameter is an array of HTTP status codes that this log target is NOT interested in. This is a shortcut for the 
`except` parameter to make it easier. Defaults to `403` and `404`, meaning that `yii\web\HttpException:403` and 
`yii\web\HttpException:404` messages will be excluded from the `categories` parameter.

## Credits

Inspired by the [olegtsvetkov/yii2-sentry](https://github.com/olegtsvetkov/yii2-sentry) package and by official 
[sentry/sentry-symfony](https://github.com/getsentry/sentry-symfony) and 
[sentry/sentry-laravel](https://github.com/getsentry/sentry-laravel) packages. 
