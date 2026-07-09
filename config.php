<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @license Proprietary
 */

use humhub\components\Application;
use humhub\modules\magicLinkAuth\Events;
use yii\console\Application as ConsoleApplication;

$events = [
    ['class' => Application::class, 'event' => Application::EVENT_BEFORE_REQUEST, 'callback' => [Events::class, 'onBeforeRequest']],
    ['class' => ConsoleApplication::class, 'event' => ConsoleApplication::EVENT_BEFORE_REQUEST, 'callback' => [Events::class, 'onBeforeRequest']],
];

return [
    'id' => 'magic-link-auth',
    'class' => humhub\modules\magicLinkAuth\Module::class,
    'namespace' => 'humhub\modules\magicLinkAuth',
    'events' => $events,
];
