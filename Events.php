<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @license Proprietary
 */

namespace humhub\modules\magicLinkAuth;

use Yii;

class Events
{
    /**
     * Bootstrap module and register login view overrides.
     */
    public static function onBeforeRequest($event = null): void
    {
        if (!Yii::$app->hasModule('magic-link-auth')) {
            return;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('magic-link-auth');
        if (!$module->isEnabled()) {
            return;
        }

        self::registerLoginViewOverrides();
    }

    /**
     * @return array<string, mixed>
     */
    public static function getEmailDefinition(): array
    {
        return [
            'key' => 'magic_link_login',
            'title' => Yii::t('MagicLinkAuthModule.base', 'Magic link sign-in'),
            'category' => 'user',
            'description' => Yii::t('MagicLinkAuthModule.base', 'Sent when a registered user requests a one-time magic sign-in link.'),
            'trigger' => Yii::t('MagicLinkAuthModule.base', 'When a user submits the magic link request form on the login page.'),
            'view' => '@magic-link-auth/views/mails/MagicLink',
            'variables' => [
                'display_name' => Yii::t('MagicLinkAuthModule.base', 'Recipient display name'),
                'first_name' => Yii::t('MagicLinkAuthModule.base', 'Recipient first name'),
                'magic_link_url' => Yii::t('MagicLinkAuthModule.base', 'Magic sign-in link'),
                'expiry_minutes' => Yii::t('MagicLinkAuthModule.base', 'Link expiry in minutes'),
                'app_name' => Yii::t('MagicLinkAuthModule.base', 'Application name'),
            ],
            'defaults' => [
                'header_bg_color' => '#f0f4f8',
                'footer_bg_color' => '#f8f9fa',
                'header_font_color' => '#1f2937',
                'footer_font_color' => '#6b7280',
                'subject' => 'Your sign-in link for {app_name}',
                'header' => '<h2 style="margin:0;">Magic link sign-in</h2>',
                'body' => "Hello {display_name},\n\nClick the button below to sign in to {app_name}:\n\n{button:Sign in|{magic_link_url}}\n\nThis link expires in {expiry_minutes} minutes and can only be used once.\n\nIf you did not request this link, you can ignore this email and sign in with your password instead.",
                'footer' => '<p style="margin:0;">{app_name}</p>',
            ],
        ];
    }

    /**
     * Override core login views to inject the magic link button.
     */
    protected static function registerLoginViewOverrides(): void
    {
        $overridePath = Yii::getAlias('@magic-link-auth/overrides/user/views/auth');
        $corePath = '@humhub/modules/user/views/auth';

        $pathMap = Yii::$app->view->theme->pathMap ?? [];
        $existing = $pathMap[$corePath] ?? $corePath;
        $existingPaths = is_array($existing) ? $existing : [$existing];

        if (!in_array($overridePath, $existingPaths, true)) {
            $pathMap[$corePath] = array_merge([$overridePath], $existingPaths);
            Yii::$app->view->theme->pathMap = $pathMap;
        }
    }
}
