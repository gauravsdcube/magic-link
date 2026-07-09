<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @author D Cube Consulting <info@dcubeconsulting.co.uk>
 */

namespace humhub\modules\magicLinkAuth\models\forms;

use humhub\components\access\ControllerAccess;
use humhub\modules\magicLinkAuth\services\MagicLinkService;
use humhub\modules\user\models\User;
use Yii;
use yii\base\Model;

class RequestMagicLinkForm extends Model
{
    public string $email = '';
    public bool $rememberMe = false;
    public string $captcha = '';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['email'], 'required'],
            [['email'], 'email'],
            [['rememberMe'], 'boolean'],
            [['email'], 'filter', 'filter' => 'trim'],
            [['email'], 'validateRequest'],
        ];

        if ($this->isCaptchaRequired()) {
            $rules[] = [['captcha'], Yii::$app->captcha->getValidatorClass()];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('MagicLinkAuthModule.base', 'Email'),
            'rememberMe' => Yii::t('UserModule.auth', 'Remember me'),
            'captcha' => Yii::t('MagicLinkAuthModule.base', 'Captcha'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->rememberMe = (bool)Yii::$app->getModule('user')->loginRememberMeDefault;
    }

    /**
     * Validate whether a magic link can be sent.
     */
    public function validateRequest(string $attribute): void
    {
        if ($this->hasErrors('captcha')) {
            return;
        }

        $normalizedEmail = MagicLinkService::normalizeEmail($this->email);
        $user = $this->findUser();

        if ($user === null) {
            return;
        }

        $service = new MagicLinkService($user);
        if ($service->isRateLimitedForEmail($normalizedEmail)) {
            $this->addError(
                $attribute,
                Yii::t('MagicLinkAuthModule.base', 'A magic link was already sent to this email address. Please wait {minutes} minutes before requesting another one, or try a different email address if you made a typo.', [
                    '{minutes}' => Yii::$app->getModule('magic-link-auth')->getResendCooldownMinutes(),
                ]),
            );
        }
    }

    /**
     * Request a magic sign-in link.
     */
    public function request(): bool
    {
        if (Yii::$app->settings->get('maintenanceMode')) {
            $this->addError('email', ControllerAccess::getMaintenanceModeWarningText());

            return false;
        }

        if (!$this->validate()) {
            return false;
        }

        $normalizedEmail = MagicLinkService::normalizeEmail($this->email);
        $user = $this->findUser();
        if ($user === null) {
            return true;
        }

        return (new MagicLinkService($user))->sendMagicLink($this->rememberMe, $normalizedEmail);
    }

    /**
     * Find an enabled user by email.
     */
    public function findUser(): ?User
    {
        $normalizedEmail = MagicLinkService::normalizeEmail($this->email);
        if ($normalizedEmail === '') {
            return null;
        }

        return User::find()
            ->where(['status' => User::STATUS_ENABLED])
            ->andWhere(['LOWER(email)' => $normalizedEmail])
            ->one();
    }

    protected function isCaptchaRequired(): bool
    {
        return Yii::$app->getModule('magic-link-auth')->isCaptchaRequired();
    }
}
