<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @license Proprietary
 */

use humhub\components\Migration;

/**
 * Initial schema for Magic Link Auth v1.0.0.
 */
class m260709_000001_initial extends Migration
{
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('magic_link_auth_token', true) !== null) {
            return;
        }

        $this->createTable('magic_link_auth_token', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'requested_email' => $this->string(150)->notNull(),
            'token_hash' => $this->string(64)->notNull(),
            'remember_me' => $this->boolean()->notNull()->defaultValue(false),
            'expires_at' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'consumed_at' => $this->dateTime()->null(),
            'ip_address' => $this->string(45)->null(),
        ]);

        $this->createIndex('idx_magic_link_auth_token_user_id', 'magic_link_auth_token', 'user_id');
        $this->createIndex('idx_magic_link_auth_token_requested_email', 'magic_link_auth_token', 'requested_email');
        $this->createIndex('idx_magic_link_auth_token_expires_at', 'magic_link_auth_token', 'expires_at');
        $this->addForeignKey(
            'fk_magic_link_auth_token_user_id',
            'magic_link_auth_token',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE',
        );
    }

    public function safeDown()
    {
        $this->dropTable('magic_link_auth_token');
    }
}
