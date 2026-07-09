<?php

use humhub\widgets\form\ActiveForm;
use humhub\widgets\bootstrap\Button;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model \humhub\modules\magicLinkAuth\models\SettingsForm */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('MagicLinkAuthModule.base', 'Magic Link Auth') ?>
    </div>
    <div class="panel-body">
        <p><?= Yii::t('MagicLinkAuthModule.base', 'Configure magic link sign-in for registered users.') ?></p>
        <br/>

        <?php $form = ActiveForm::begin(); ?>

        <div class="mb-3">
            <?= $form->field($model, 'enabled')->checkbox()->hint(Yii::t(
                'MagicLinkAuthModule.base',
                'When enabled, registered users can request a one-time sign-in link from the login page.',
            )) ?>
        </div>

        <div class="mb-3">
            <?= $form->field($model, 'linkExpiryMinutes')->input('number', ['min' => 1, 'max' => 1440])->hint(Yii::t(
                'MagicLinkAuthModule.base',
                'How long a magic link remains valid after it is sent.',
            )) ?>
        </div>

        <div class="mb-3">
            <?= $form->field($model, 'resendCooldownMinutes')->input('number', ['min' => 1, 'max' => 1440])->hint(Yii::t(
                'MagicLinkAuthModule.base',
                'Minimum wait time before the same email address can request another pending magic link.',
            )) ?>
        </div>

        <div class="mb-3">
            <?= $form->field($model, 'requireCaptcha')->checkbox()->hint(Yii::t(
                'MagicLinkAuthModule.base',
                'Require captcha verification on the magic link request form.',
            )) ?>
        </div>

        <hr>
        <?= Button::save()->submit() ?>
        <?= Button::light(Yii::t('MagicLinkAuthModule.base', 'Back to modules'))
            ->link(Url::to(['/admin/module']))
            ->cssClass('float-end') ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
