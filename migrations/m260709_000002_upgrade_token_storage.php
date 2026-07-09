<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @license Proprietary
 */

use humhub\components\Migration;

/**
 * Upgrades early development installs to the v1.0.0 schema.
 */
class m260709_000002_upgrade_token_storage extends Migration
{
    public function safeUp()
    {
        $table = $this->db->schema->getTableSchema('magic_link_auth_token', true);
        if ($table === null) {
            return;
        }

        if ($table->getColumn('requested_email') === null) {
            $this->addColumn('magic_link_auth_token', 'requested_email', $this->string(150)->null()->after('user_id'));
            $this->createIndex('idx_magic_link_auth_token_requested_email', 'magic_link_auth_token', 'requested_email');
        }

        if ($table->getColumn('token_hash') === null && $table->getColumn('token_encrypted') !== null) {
            $this->delete('magic_link_auth_token');
            $this->renameColumn('magic_link_auth_token', 'token_encrypted', 'token_hash');
            $this->alterColumn('magic_link_auth_token', 'token_hash', $this->string(64)->notNull());
        }
    }

    public function safeDown()
    {
        echo "m260709_000002_upgrade_token_storage cannot be reverted.\n";

        return false;
    }
}
