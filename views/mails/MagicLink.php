<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @author D Cube Consulting <info@dcubeconsulting.co.uk>
 */

use humhub\components\View;
use humhub\helpers\Html;
use humhub\helpers\MailStyleHelper;
use humhub\modules\user\models\User;

/* @var View $this */
/* @var string $magicLinkUrl */
/* @var int $expiryMinutes */
/* @var User $user */
?>
<tr>
    <td align="center" valign="top" class="fix-box">
        <table width="600" align="center" border="0" cellspacing="0" cellpadding="0" class="container"
               style="background-color: <?= MailStyleHelper::getBackgroundColorMain() ?>; ">
            <tr>
                <td valign="top">
                    <table width="540" align="center" border="0" cellspacing="0" cellpadding="0" class="full-width"
                           style="background-color:<?= MailStyleHelper::getBackgroundColorMain() ?>">
                        <tr>
                            <td valign="top">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                                    <tr>
                                        <td valign="top" width="auto" align="center">
                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td width="auto" align="center" valign="middle" height="28"
                                                        style="background-color: <?= MailStyleHelper::getBackgroundColorMain() ?>; background-clip: padding-box; font-size: 26px; font-family: <?= MailStyleHelper::getFontFamily() ?>; text-align: center; font-weight: 300; padding: 0 18px">
                                                        <span style="color: <?= MailStyleHelper::getTextColorHighlight() ?>; font-weight: 300">
                                                            <?= Yii::t('MagicLinkAuthModule.base', '<strong>Magic link</strong> sign-in') ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>

<tr>
    <td align="center" valign="top" class="fix-box">
        <table width="600" align="center" border="0" cellspacing="0" cellpadding="0" class="container"
               style="background-color: <?= MailStyleHelper::getBackgroundColorMain() ?>; border-radius: 0 0 4px 4px">
            <tr>
                <td valign="top">
                    <table width="540" align="center" border="0" cellspacing="0" cellpadding="0" class="full-width"
                           style="background-color:<?= MailStyleHelper::getBackgroundColorMain() ?>">
                        <tr>
                            <td valign="top">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
                                    <tr>
                                        <td valign="top">
                                            <table border="0" cellspacing="0" cellpadding="0" align="left">
                                                <tr>
                                                    <td height="30"></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 14px; line-height: 22px; font-family: <?= MailStyleHelper::getFontFamily() ?>; color: <?= MailStyleHelper::getTextColorMain() ?>; font-weight: 300; text-align: left">
                                                        <?= Yii::t('UserModule.auth', 'Hello {displayName}', ['{displayName}' => Html::encode($user->displayName)]) ?>
                                                        <br><br>
                                                        <?= Yii::t('MagicLinkAuthModule.base', 'Click the button below to sign in to {siteName}.', ['{siteName}' => Html::encode(Yii::$app->name)]) ?>
                                                        <br>
                                                        <?= Yii::t('MagicLinkAuthModule.base', 'This link expires in {minutes} minutes and can only be used once.', ['{minutes}' => (int)$expiryMinutes]) ?>
                                                        <br>
                                                        <?= Yii::t('MagicLinkAuthModule.base', 'If you did not request this link, you can ignore this email and sign in with your password instead.') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="30"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="top" width="auto" align="center">
                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td width="auto" align="center" valign="middle" height="32"
                                                        style="background-color: <?= MailStyleHelper::getColorPrimary() ?>; border-radius: 5px; background-clip: padding-box; font-size: 14px; font-family: <?= MailStyleHelper::getFontFamily() ?>; text-align: center; color: <?= MailStyleHelper::getTextColorContrast() ?>; font-weight: 600; padding: 5px 30px">
                                                        <span style="color: <?= MailStyleHelper::getTextColorContrast() ?>; font-weight: 300">
                                                            <a href="<?= Html::encode($magicLinkUrl) ?>"
                                                               style="text-decoration: none; color: <?= MailStyleHelper::getTextColorContrast() ?>; font-weight: 300">
                                                                <strong><?= Yii::t('MagicLinkAuthModule.base', 'Sign in') ?></strong>
                                                            </a>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="20"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
