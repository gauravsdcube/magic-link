<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @author D Cube Consulting <info@dcubeconsulting.co.uk>
 */

namespace humhub\modules\magicLinkAuth\widgets;

use humhub\helpers\Html;
use humhub\modules\magicLinkAuth\Module;
use humhub\widgets\modal\ModalButton;
use Yii;
use yii\base\Widget;

class MagicLinkLoginButton extends Widget
{
    public bool $modal = false;

    /**
     * Whether magic link sign-in is enabled.
     */
    public static function isEnabled(): bool
    {
        if (!Yii::$app->hasModule('magic-link-auth')) {
            return false;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('magic-link-auth');

        return $module->isEnabled();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!self::isEnabled()) {
            return '';
        }

        $label = Yii::t('MagicLinkAuthModule.base', 'Sign in with magic link');
        $route = ['/magic-link-auth/auth/request'];

        if ($this->modal) {
            return ModalButton::light($label)
                ->load($route)
                ->cssClass('btn btn-secondary w-100')
                ->id('magic-link-login-button-modal');
        }

        return Html::a(
            $label,
            $route,
            [
                'class' => 'btn btn-secondary w-100',
                'id' => 'magic-link-login-button',
                'data' => ['pjax-prevent' => true],
            ],
        );
    }
}
