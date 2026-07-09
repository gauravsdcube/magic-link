<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @license Proprietary
 */

namespace humhub\modules\magicLinkAuth\models;

use humhub\components\ActiveRecord;
use humhub\libs\Helpers;
use humhub\modules\magicLinkAuth\Module;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * Stores hashed one-time magic link tokens.
 *
 * @property int $id
 * @property int $user_id
 * @property string $requested_email
 * @property string $token_hash
 * @property bool $remember_me
 * @property string $expires_at
 * @property string $created_at
 * @property string|null $consumed_at
 * @property string|null $ip_address
 *
 * @property-read User $user
 */
class MagicLinkToken extends ActiveRecord
{
    public const TOKEN_BYTE_LENGTH = 32;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'magic_link_auth_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'requested_email', 'token_hash', 'expires_at', 'created_at'], 'required'],
            [['user_id'], 'integer'],
            [['remember_me'], 'boolean'],
            [['requested_email'], 'string', 'max' => 150],
            [['token_hash'], 'string', 'max' => 64],
            [['expires_at', 'created_at', 'consumed_at'], 'safe'],
            [['ip_address'], 'string', 'max' => 45],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Create a new cryptographically secure token.
     */
    public static function generateToken(): string
    {
        return Yii::$app->security->generateRandomString(self::TOKEN_BYTE_LENGTH);
    }

    /**
     * Hash a token for storage. Plain tokens are never persisted.
     */
    public static function hashToken(string $token): string
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('magic-link-auth');

        return hash_hmac('sha256', $token, $module->getSigningKey());
    }

    /**
     * Find a valid pending token for the given user and plain token string.
     */
    public static function findValidToken(User $user, string $token): ?self
    {
        if ($token === '' || strlen($token) > 255) {
            return null;
        }

        $hash = self::hashToken($token);

        return self::find()
            ->where([
                'user_id' => $user->id,
                'token_hash' => $hash,
                'consumed_at' => null,
            ])
            ->andWhere(['>', 'expires_at', date('Y-m-d H:i:s')])
            ->one();
    }

    /**
     * Atomically mark a token as consumed to prevent replay.
     */
    public static function consumeById(int $id): bool
    {
        return self::updateAll(
            ['consumed_at' => date('Y-m-d H:i:s')],
            ['id' => $id, 'consumed_at' => null],
        ) === 1;
    }

    /**
     * Invalidate all pending tokens for a user.
     */
    public static function invalidatePendingForUser(int $userId): void
    {
        self::updateAll(
            ['consumed_at' => date('Y-m-d H:i:s')],
            [
                'and',
                ['user_id' => $userId],
                ['consumed_at' => null],
            ],
        );
    }

    /**
     * Delete expired and consumed tokens older than the given days.
     */
    public static function cleanup(int $olderThanDays = 7): int
    {
        $threshold = date('Y-m-d H:i:s', time() - ($olderThanDays * 86400));

        return self::deleteAll([
            'or',
            ['<', 'expires_at', date('Y-m-d H:i:s')],
            [
                'and',
                ['not', ['consumed_at' => null]],
                ['<', 'consumed_at', $threshold],
            ],
        ]);
    }
}
