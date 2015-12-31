<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class NF_Upgrade_Database_Migrations extends NF_Upgrade
{
    public $name = 'database_migrations';

    public $priority = "0.0.1";

    public $description = 'The database needs to be updated to support the new version.';

    public $args = array();

    public $errors = array();

    public function loading()
    {
        $already_run = $this->isComplete();

        $this->total_steps = ( $already_run ) ? 0 : 1;
    }

    public function step( $step )
    {
        $this->createObjectTable();
        $this->createObjectMetaTable();
        $this->createObjectRelationshipsTable();
    }

    public function complete()
    {
        update_option( 'nf_database_migrations', true);
    }

    public function isComplete()
    {
        if( $this->existsObjectTable() ||
            $this->existsObjectMetaTable() ||
            $this->existsObjectRelationshipsTable()
        ) {
            return true;
        }
        return get_option( 'nf_database_migrations', false );
    }

    /*
     * PRIVATE METHODS
     */

    private function createObjectTable()
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql = "CREATE TABLE IF NOT EXISTS " . NF_OBJECTS_TABLE_NAME . " (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `type` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;";

        dbDelta( $sql );
    }

    private function existsObjectTable()
    {
        global $wpdb;
        return $wpdb->query( "SHOW TABLES LIKE '" . NF_OBJECTS_TABLE_NAME . "'" );
    }


    private function createObjectMetaTable()
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql = "CREATE TABLE IF NOT EXISTS ". NF_OBJECT_META_TABLE_NAME . " (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `object_id` bigint(20) NOT NULL,
        `meta_key` varchar(255) NOT NULL,
        `meta_value` longtext NOT NULL,
        PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;";

        dbDelta( $sql );
    }

    private function existsObjectMetaTable()
    {
        global $wpdb;
        return $wpdb->query( "SHOW TABLES LIKE '" . NF_OBJECT_META_TABLE_NAME . "'" );
    }

    private function createObjectRelationshipsTable()
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql = "CREATE TABLE IF NOT EXISTS " . NF_OBJECT_RELATIONSHIPS_TABLE_NAME . " (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `child_id` bigint(20) NOT NULL,
        `parent_id` bigint(20) NOT NULL,
        `child_type` varchar(255) NOT NULL,
        `parent_type` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;";

        dbDelta( $sql );
    }

    private function existsObjectRelationshipsTable()
    {
        global $wpdb;
        return $wpdb->query( "SHOW TABLES LIKE '" . NF_OBJECT_RELATIONSHIPS_TABLE_NAME . "'" );
    }
}
