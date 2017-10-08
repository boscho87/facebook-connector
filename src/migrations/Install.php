<?php
/**
 * FacebookConnector plugin for Craft CMS 3.x
 *
 * @link      www.itscoding.ch
 * @copyright Copyright (c) 2017 boscho87\itscoding
 */

namespace itscoding\facebookconnector\migrations;

use Craft;
use craft\db\Migration;

/**
 * FacebookConnector Install Migration
 *
 * @author    boscho87\itscoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class Install extends Migration
{

    /**
     * @var string The database driver to use
     */
    public $driver;

    /**
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            Craft::$app->db->schema->refresh();
        }
        return true;
    }

    /**
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();
        return true;
    }

    /**
     * Creates the tables needed for the Records used by the plugin
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%facebookconnector_accesstoken}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%facebookconnector_accesstoken}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'data' => $this->text()->notNull()->defaultValue(''),
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%facebookconnector_postmemorize}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%facebookconnector_postmemorize}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'entryId' => $this->integer()->unique(),
                    'facebookId' => $this->string(255)->unique(),
                    'checksum' => $this->string(255)->notNull()
                ]
            );
        }
        return $tablesCreated;
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%facebookconnector_accesstoken}}');
        $this->dropTableIfExists('{{%facebookconnector_postmemorize}}');
    }
}
