<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @license Proprietary
 */

namespace humhub\modules\magicLinkAuth\services;

use humhub\libs\SafeBaseUrl;
use humhub\modules\magicLinkAuth\models\MagicLinkToken;
use humhub\modules\magicLinkAuth\Module;
use humhub\modules\user\models\User;
use Yii;

class MagicLinkService
{
    public function __construct(private readonly ?User $user = null)
    {
    }

    /**
     * Normalize an email address for lookup and rate limiting.
     */
    public static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    /**
     * Whether a new magic link can be sent for the same email address.
     *
     * Only applies while an unused, unexpired link is still pending.
     */
    public function isRateLimitedForEmail(string $requestedEmail): bool
    {
        if ($this->user === null) {
            return false;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('magic-link-auth');
        $cooldownSeconds = $module->getResendCooldownMinutes() * 60;
        $requestedEmail = self::normalizeEmail($requestedEmail);

        $latest = MagicLinkToken::find()
            ->where([
                'user_id' => $this->user->id,
                'requested_email' => $requestedEmail,
                'consumed_at' => null,
            ])
            ->andWhere(['>', 'expires_at', date('Y-m-d H:i:s')])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if ($latest === null) {
            return false;
        }

        return strtotime($latest->created_at) + $cooldownSeconds >= time();
    }

    /**
     * Create and email a magic sign-in link.
     */
    public function sendMagicLink(bool $rememberMe = false, ?string $requestedEmail = null): bool
    {
        if ($this->user === null) {
            return false;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('magic-link-auth');
        $requestedEmail = self::normalizeEmail($requestedEmail ?? $this->user->email);

        MagicLinkToken::cleanup();
        MagicLinkToken::invalidatePendingForUser($this->user->id);

        $token = MagicLinkToken::generateToken();
        $expiresAt = time() + ($module->getLinkExpiryMinutes() * 60);

        $record = new MagicLinkToken();
        $record->user_id = $this->user->id;
        $record->requested_email = $requestedEmail;
        $record->token_hash = MagicLinkToken::hashToken($token);
        $record->remember_me = $rememberMe;
        $record->expires_at = date('Y-m-d H:i:s', $expiresAt);
        $record->created_at = date('Y-m-d H:i:s');
        $record->ip_address = Yii::$app->request->userIP;

        if (!$record->save()) {
            return false;
        }

        Yii::$app->setLanguage($this->user->language);

        $magicLinkUrl = SafeBaseUrl::to([
            '/magic-link-auth/auth/verify',
            'token' => $token,
            'guid' => $this->user->guid,
        ], true);

        $mail = Yii::$app->mailer->compose([
            'html' => '@magic-link-auth/views/mails/MagicLink',
            'text' => '@magic-link-auth/views/mails/plaintext/MagicLink',
        ], [
            'user' => $this->user,
            'magicLinkUrl' => $magicLinkUrl,
            'expiryMinutes' => $module->getLinkExpiryMinutes(),
        ]);

        $mail->setTo($this->user->email);
        $mail->setSubject(Yii::t('MagicLinkAuthModule.base', 'Your sign-in link for {siteName}', [
            '{siteName}' => Yii::$app->name,
        ]));

        if (!$mail->send()) {
            $record->delete();

            return false;
        }

        return true;
    }

    /**
     * Validate a token for the given user.
     */
    public function validateToken(User $user, ?string $token): ?MagicLinkToken
    {
        if ($token === null || $token === '') {
            return null;
        }

        return MagicLinkToken::findValidToken($user, $token);
    }

    /**
     * Atomically consume a token before login to prevent replay.
     */
    public function consumeToken(MagicLinkToken $record): bool
    {
        return MagicLinkToken::consumeById((int)$record->id);
    }
}
