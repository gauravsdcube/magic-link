<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @license Proprietary
 */

namespace humhub\modules\magicLinkAuth\services;

use Yii;

class LoginRedirectService
{
    /**
     * Resolve a safe post-login destination for magic link sign-in.
     */
    public function resolve(): string|array
    {
        $returnUrl = Yii::$app->user->getReturnUrl();

        if (!$this->shouldIgnoreReturnUrl($returnUrl)) {
            return $returnUrl;
        }

        if (Yii::$app->hasModule('homepage') && class_exists(\humhub\modules\homepage\models\Homepage::class)) {
            $userHomeUrl = \humhub\modules\homepage\models\Homepage::getUrlForUser();
            if ($userHomeUrl) {
                Yii::$app->homeUrl = $userHomeUrl;

                return $userHomeUrl;
            }
        }

        return ['/dashboard/dashboard'];
    }

    protected function shouldIgnoreReturnUrl(string|array $returnUrl): bool
    {
        if ($this->isAuthenticationUrl($returnUrl)) {
            return true;
        }

        if (Yii::$app->hasModule('homepage') && class_exists(\humhub\modules\homepage\models\Homepage::class)) {
            $guestHomeUrl = \humhub\modules\homepage\models\Homepage::getUrlForGuest();
            if ($guestHomeUrl && $this->urlsMatch($returnUrl, $guestHomeUrl)) {
                return true;
            }
        }

        return false;
    }

    protected function isAuthenticationUrl(string|array $url): bool
    {
        $path = $this->normalizeUrlPath($url);
        if ($path === '') {
            return false;
        }

        $authPrefixes = [
            '/user/auth/login',
            '/magic-link-auth/auth/request',
            '/magic-link-auth/auth/verify',
            '/user/password-recovery',
            '/user/registration',
        ];

        foreach ($authPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return true;
            }
        }

        return false;
    }

    protected function urlsMatch(string|array $left, string|array $right): bool
    {
        return $this->normalizeUrlPath($left) === $this->normalizeUrlPath($right);
    }

    protected function normalizeUrlPath(string|array $url): string
    {
        if (is_array($url)) {
            $url = Yii::$app->urlManager->createUrl($url);
        }

        $path = parse_url((string)$url, PHP_URL_PATH);

        return rtrim((string)$path, '/') ?: '/';
    }
}
