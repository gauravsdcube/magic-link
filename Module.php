<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @license Proprietary
 * @package humhub\modules\magicLinkAuth
 */

namespace humhub\modules\magicLinkAuth;

use humhub\components\Module as BaseModule;
use Yii;
use yii\helpers\Url;

class Module extends BaseModule
{
    public const VERSION = '1.0.0';

    public const SETTING_ENABLED = 'enabled';
    public const SETTING_LINK_EXPIRY_MINUTES = 'linkExpiryMinutes';
    public const SETTING_RESEND_COOLDOWN_MINUTES = 'resendCooldownMinutes';
    public const SETTING_REQUIRE_CAPTCHA = 'requireCaptcha';
    public const SETTING_SIGNING_KEY = 'signingKey';

    public const DEFAULT_LINK_EXPIRY_MINUTES = 10;
    public const DEFAULT_RESEND_COOLDOWN_MINUTES = 10;

    public $icon = 'magic';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Yii::setAlias('@magic-link-auth', $this->getBasePath());
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return Yii::t('MagicLinkAuthModule.base', 'Magic Link Auth');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('MagicLinkAuthModule.base', 'Sign in with a one-time magic link sent to your email.');
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/magic-link-auth/config/index']);
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Whether magic link login is enabled.
     */
    public function isEnabled(): bool
    {
        return (bool)$this->settings->get(self::SETTING_ENABLED, true);
    }

    /**
     * Configured link expiry in minutes.
     */
    public function getLinkExpiryMinutes(): int
    {
        return max(1, (int)$this->settings->get(self::SETTING_LINK_EXPIRY_MINUTES, self::DEFAULT_LINK_EXPIRY_MINUTES));
    }

    /**
     * Configured resend cooldown in minutes.
     */
    public function getResendCooldownMinutes(): int
    {
        return max(1, (int)$this->settings->get(self::SETTING_RESEND_COOLDOWN_MINUTES, self::DEFAULT_RESEND_COOLDOWN_MINUTES));
    }

    /**
     * Whether captcha is required on the magic link request form.
     */
    public function isCaptchaRequired(): bool
    {
        return (bool)$this->settings->get(self::SETTING_REQUIRE_CAPTCHA, false);
    }

    /**
     * Secret key used to hash magic link tokens at rest.
     */
    public function getSigningKey(): string
    {
        $key = (string)$this->settings->get(self::SETTING_SIGNING_KEY, '');
        if ($key === '') {
            $legacyKey = (string)$this->settings->get('encryptionKey', '');
            $key = $legacyKey !== '' ? $legacyKey : Yii::$app->security->generateRandomString(32);
            $this->settings->set(self::SETTING_SIGNING_KEY, $key);
        }

        return $key;
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        if (class_exists(models\MagicLinkToken::class)) {
            models\MagicLinkToken::deleteAll();
        }

        parent::disable();
    }
}
