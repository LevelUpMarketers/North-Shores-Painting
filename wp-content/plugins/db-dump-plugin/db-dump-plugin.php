<?php
/*
Plugin Name: DB Dump Plugin
Description: Provides a simple admin page to export the full WordPress database to an SQL file.
Version: 1.0.0
Author: Codex QA
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register the admin menu page.
 */
function db_dump_plugin_admin_menu() {
    add_management_page(
        'DB Dump',
        'DB Dump',
        'manage_options',
        'db-dump-plugin',
        'db_dump_plugin_admin_page'
    );
}
add_action( 'admin_menu', 'db_dump_plugin_admin_menu' );

/**
 * Render the admin page with the export button.
 */
function db_dump_plugin_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( isset( $_POST['db_dump_plugin_pull'] ) && check_admin_referer( 'db_dump_plugin_export', 'db_dump_plugin_nonce' ) ) {
        db_dump_plugin_export();
        echo '<div class="updated"><p>Database export completed.</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>DB Dump Plugin</h1>
        <form method="post">
            <?php wp_nonce_field( 'db_dump_plugin_export', 'db_dump_plugin_nonce' ); ?>
            <p>
                <input type="submit" name="db_dump_plugin_pull" class="button button-primary" value="Pull Database" />
            </p>
        </form>
    </div>
    <?php
}

/**
 * Export the entire database to an SQL file within the plugin directory.
 */
function db_dump_plugin_export() {
    global $wpdb;

    $dbname   = DB_NAME;
    $user     = DB_USER;
    $password = DB_PASSWORD;
    $host     = DB_HOST;
    $dumpfile = plugin_dir_path( __FILE__ ) . 'database-export.sql';

    // Attempt to use mysqldump via shell command.
    $command = sprintf(
        'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
        escapeshellarg( $user ),
        escapeshellarg( $password ),
        escapeshellarg( $host ),
        escapeshellarg( $dbname ),
        escapeshellarg( $dumpfile )
    );

    if ( function_exists( 'exec' ) ) {
        exec( $command, $output, $return_var );
        if ( $return_var !== 0 ) {
            error_log( 'DB Dump Plugin: mysqldump failed. Output: ' . implode( '\n', $output ) );
        }
    } else {
        // Fallback: use php to export (may be slower, but avoids exec dependency).
        $tables = $wpdb->get_col( 'SHOW TABLES' );
        $sql = "SET foreign_key_checks = 0;\n";
        foreach ( $tables as $table ) {
            $create_table = $wpdb->get_row( "SHOW CREATE TABLE `{$table}`", ARRAY_N );
            $sql .= "\nDROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $create_table[1] . ";\n\n";

            $rows = $wpdb->get_results( "SELECT * FROM `{$table}`", ARRAY_A );
            if ( $rows ) {
                $sql .= "INSERT INTO `{$table}` VALUES\n";
                $insert_rows = array();
                foreach ( $rows as $row ) {
                    $values = array();
                    foreach ( $row as $value ) {
                        if ( is_null( $value ) ) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . esc_sql( $value ) . "'";
                        }
                    }
                    $insert_rows[] = '(' . implode( ',', $values ) . ')';
                }
                $sql .= implode( ",\n", $insert_rows ) . ";\n\n";
            }
        }
        $sql .= "SET foreign_key_checks = 1;\n";
        file_put_contents( $dumpfile, $sql );
    }
}
?>
