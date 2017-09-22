<?php
/**
 * FacebookConnector plugin for Craft CMS 3.x
 *
 * @link      www.itscoding.ch
 * @copyright Copyright (c) 2017 boscho87\itscoding
 */

namespace boscho87fbconn\facebookconnector\migrations;


use Craft;
use craft\db\Migration;

/**
 * FacebookConnector Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    boscho87\itscoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
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
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();
        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;
        // facebookconnector_accesstoken table
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
     *
     * @return void
     */
    protected function removeTables()
    {
        // facebookconnector_accesstoken table
        $this->dropTableIfExists('{{%facebookconnector_accesstoken}}');
        $this->dropTableIfExists('{{%facebookconnector_postmemorize}}');
    }
}
