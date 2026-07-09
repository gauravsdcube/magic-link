<?php

use humhub\modules\magicLinkAuth\models\forms\RequestMagicLinkForm;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $model RequestMagicLinkForm */

?>

<?php Modal::beginDialog([
    'id' => 'magic-link-auth-success-modal',
    'title' => Yii::t('MagicLinkAuthModule.base', '<strong>Check</strong> your email'),
    'footer' => ModalButton::light(Yii::t('MagicLinkAuthModule.base', 'Back to sign in'))->load(['/user/auth/login'])->pjax(false),
]) ?>
    <p><?= Yii::t('MagicLinkAuthModule.base', 'If an account exists for this email address, a sign-in link has been sent. The link expires after a short time and can only be used once.') ?></p>
<?php Modal::endDialog() ?>
