<?php

use humhub\modules\magicLinkAuth\models\forms\RequestMagicLinkForm;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\SiteLogo;
use yii\helpers\Url;

/* @var $model RequestMagicLinkForm */

$this->pageTitle = Yii::t('MagicLinkAuthModule.base', 'Magic link sign-in');
?>
<div id="magic-link-auth-success" class="container">
    <?= SiteLogo::widget(['place' => SiteLogo::PLACE_LOGIN]) ?>
    <br>

    <div class="panel panel-default animated fadeIn">
        <div class="panel-heading">
            <?= Yii::t('MagicLinkAuthModule.base', '<strong>Check</strong> your email') ?>
        </div>
        <div class="panel-body">
            <p><?= Yii::t('MagicLinkAuthModule.base', 'If an account exists for this email address, a sign-in link has been sent. The link expires after a short time and can only be used once.') ?></p>
            <br/>
            <?= Button::light(Yii::t('MagicLinkAuthModule.base', 'Back to sign in'))->link(Url::to(['/user/auth/login']))->pjax(false) ?>
        </div>
    </div>
</div>
