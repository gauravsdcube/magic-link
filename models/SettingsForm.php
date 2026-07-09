<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @author D Cube Consulting <info@dcubeconsulting.co.uk>
 */

namespace humhub\modules\magicLinkAuth\models;

use humhub\modules\magicLinkAuth\Module;
use Yii;
use yii\base\Model;

class SettingsForm extends Model
{
    public bool $enabled = true;
    public int $linkExpiryMinutes = Module::DEFAULT_LINK_EXPIRY_MINUTES;
    public int $resendCooldownMinutes = Module::DEFAULT_RESEND_COOLDOWN_MINUTES;
    public bool $requireCaptcha = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        /** @var Module $module */
        $module = Yii::$app->getModule('magic-link-auth');

        $this->enabled = $module->isEnabled();
        $this->linkExpiryMinutes = $module->getLinkExpiryMinutes();
        $this->resendCooldownMinutes = $module->getResendCooldownMinutes();
        $this->requireCaptcha = $module->isCaptchaRequired();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled', 'requireCaptcha'], 'boolean'],
            [['linkExpiryMinutes', 'resendCooldownMinutes'], 'integer', 'min' => 1, 'max' => 1440],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'enabled' => Yii::t('MagicLinkAuthModule.base', 'Enable magic link sign-in'),
            'linkExpiryMinutes' => Yii::t('MagicLinkAuthModule.base', 'Link expiry (minutes)'),
            'resendCooldownMinutes' => Yii::t('MagicLinkAuthModule.base', 'Resend cooldown (minutes)'),
            'requireCaptcha' => Yii::t('MagicLinkAuthModule.base', 'Require captcha on request form'),
        ];
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('magic-link-auth');

        $module->settings->set(Module::SETTING_ENABLED, (bool)$this->enabled);
        $module->settings->set(Module::SETTING_LINK_EXPIRY_MINUTES, (int)$this->linkExpiryMinutes);
        $module->settings->set(Module::SETTING_RESEND_COOLDOWN_MINUTES, (int)$this->resendCooldownMinutes);
        $module->settings->set(Module::SETTING_REQUIRE_CAPTCHA, (bool)$this->requireCaptcha);

        return true;
    }
}
