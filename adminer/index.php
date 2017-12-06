<?php
function adminer_object() {
    // required to run any plugin
    include_once "./plugins/plugin.php";

    // autoloader
    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }

    $plugins = array(
        // specify enabled plugins here
        new AdminerLoginServersEnhanced(
          array(
            new AdminerLoginServerEnhanced(getenv(strtoupper(str_replace('-', '_', getenv('DATABASE_SERVICE_NAME'))).'_SERVICE_HOST'), 'DATABASE_SERVICE_HOST', 'server')
          )
        ),
    );

    /* It is possible to combine customization and plugins:
    class AdminerCustomization extends AdminerPlugin {
    }
    return new AdminerCustomization($plugins);
    */

    return new AdminerPlugin($plugins);
}

// include original Adminer or Adminer Editor
include "./adminer.php";
