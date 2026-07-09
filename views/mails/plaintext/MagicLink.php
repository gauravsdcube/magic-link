<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @author D Cube Consulting <info@dcubeconsulting.co.uk>
 */

use humhub\modules\user\models\User;

/* @var string $magicLinkUrl */
/* @var int $expiryMinutes */
/* @var User $user */
?>
<?= Yii::t('UserModule.auth', 'Hello {displayName}', ['{displayName}' => $user->displayName]) ?>


<?= Yii::t('MagicLinkAuthModule.base', 'Click the link below to sign in to {siteName}:', ['{siteName}' => Yii::$app->name]) ?>

<?= $magicLinkUrl ?>


<?= Yii::t('MagicLinkAuthModule.base', 'This link expires in {minutes} minutes and can only be used once.', ['{minutes}' => (int)$expiryMinutes]) ?>

<?= Yii::t('MagicLinkAuthModule.base', 'If you did not request this link, you can ignore this email and sign in with your password instead.') ?>
