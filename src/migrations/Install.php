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
                    'data' => $this->text(),
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
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%facebookconnector_facebook_entry}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%facebookconnector_facebook_entry}}',
                [
                    'id' => $this->primaryKey(),
                    'fbId' => $this->string(255)->unique(),
                    'slug' => $this->string(255)->unique(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'created' => $this->string(255)->notNull(),
                    'start_time' => $this->string(45),
                    'end_time' => $this->string(45),
                    'event_cover_source' => $this->string(255),
                    'event_cover_offset_x' => $this->string(45),
                    'event_cover_offset_y' => $this->string(45),
                    'has_detail' => $this->boolean(),
                    'type' => $this->string(),
                    'image_src_1' => $this->string(510),
                    'image_src_2' => $this->string(),
                    'image_src_3' => $this->string(),
                    'image_src_4' => $this->string(),
                    'image_src_5' => $this->string(),
                    'image_src_6' => $this->string(),
                    'image_src' => $this->string(510),
                    'image_height' => $this->string(),
                    'image_width' => $this->string(),
                    'target' => $this->string(510),
                    'url' => $this->string(510),
                    'internal' => $this->boolean(),
                    'uid' => $this->uid(),
                    'title' => $this->string(510),
                    'video' => $this->string(510),
                    'content' => $this->text()
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
        $this->dropTableIfExists('{{%facebookconnector_facebook_entry}}');
    }
}
