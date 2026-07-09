<?php

use humhub\helpers\Html;
use humhub\modules\magicLinkAuth\models\forms\RequestMagicLinkForm;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\form\CaptchaField;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $model RequestMagicLinkForm */

?>

<?php $form = Modal::beginFormDialog([
    'id' => 'magic-link-auth-request-modal',
    'title' => Yii::t('MagicLinkAuthModule.base', '<strong>Sign in</strong> with magic link'),
    'footer'
        => ModalButton::light(Yii::t('MagicLinkAuthModule.base', 'Back'))->load(['/user/auth/login'])->pjax(false) . ' '
        . ModalButton::save(Yii::t('MagicLinkAuthModule.base', 'Send magic link'))->submit(['/magic-link-auth/auth/request']),
]) ?>
    <p><?= Yii::t('MagicLinkAuthModule.base', 'Enter your email address and we will send you a one-time sign-in link.') ?></p>
    <?= $form->field($model, 'email')->input('email', [
        'id' => 'magic-link-email-modal',
        'placeholder' => Yii::t('MagicLinkAuthModule.base', 'Your email'),
    ]) ?>
    <?= $form->field($model, 'rememberMe')->checkbox() ?>
    <?php if (Yii::$app->getModule('magic-link-auth')->isCaptchaRequired()) : ?>
        <?= $form->field($model, 'captcha')->widget(CaptchaField::class)->label(false) ?>
    <?php endif; ?>
<?php Modal::endFormDialog() ?>

<script <?= Html::nonce() ?>>
    $(document).on('humhub:ready', function () {
        $('#magic-link-email-modal').focus();
    });
</script>
