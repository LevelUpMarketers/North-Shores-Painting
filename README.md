# North Shores Painting


## DB Dump Plugin

This repository includes a custom WordPress plugin located at `wp-content/plugins/db-dump-plugin`.
The plugin adds an admin page under **Tools → DB Dump** with a **Pull Database** button.
Pressing the button generates an SQL dump of the entire WordPress database and stores it as
`database-export.sql` in the plugin directory. The dump can then be downloaded or committed
for review.
If the `mysqldump` command is unavailable, the plugin now falls back to a PHP-based
export so it works on most hosting environments.
