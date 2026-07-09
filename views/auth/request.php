<?php

use humhub\helpers\Html;
use humhub\modules\magicLinkAuth\models\forms\RequestMagicLinkForm;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\form\CaptchaField;
use humhub\widgets\LanguageChooser;
use humhub\widgets\SiteLogo;
use yii\helpers\Url;

/* @var $model RequestMagicLinkForm */

$this->pageTitle = Yii::t('MagicLinkAuthModule.base', 'Magic link sign-in');
?>
<div id="magic-link-auth-request" class="container">
    <?= SiteLogo::widget(['place' => SiteLogo::PLACE_LOGIN]) ?>
    <br>

    <div id="magic-link-request-form" class="panel panel-default animated bounceIn">
        <div class="panel-heading">
            <?= Yii::t('MagicLinkAuthModule.base', '<strong>Sign in</strong> with magic link') ?>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

            <p><?= Yii::t('MagicLinkAuthModule.base', 'Enter your email address and we will send you a one-time sign-in link.') ?></p>

            <?= $form->field($model, 'email')->input('email', [
                'id' => 'magic-link-email',
                'placeholder' => Yii::t('MagicLinkAuthModule.base', 'Your email'),
            ])->label(false) ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <?php if (Yii::$app->getModule('magic-link-auth')->isCaptchaRequired()) : ?>
                <div class="mb-3">
                    <?= $form->field($model, 'captcha')->widget(CaptchaField::class)->label(false) ?>
                </div>
            <?php endif; ?>

            <?= Button::light(Yii::t('MagicLinkAuthModule.base', 'Back'))->link(Url::to(['/user/auth/login']))->pjax(false) ?>
            <?= Html::submitButton(Yii::t('MagicLinkAuthModule.base', 'Send magic link'), [
                'class' => 'btn btn-primary',
                'data-ui-loader' => '',
            ]) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?= LanguageChooser::widget(['vertical' => true, 'hideLabel' => true]) ?>
</div>

<script <?= Html::nonce() ?>>
    $(function () {
        $('#magic-link-email').focus();
    });

    <?php if ($model->hasErrors()) : ?>
    $('#magic-link-request-form').removeClass('bounceIn');
    $('#magic-link-request-form').addClass('shake');
  <?php endif; ?>
</script>
