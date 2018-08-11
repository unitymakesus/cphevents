<?php
/**
 * woo Upgrade class
 *
 * Performas upgrade dokan latest version
 *
 * @since 2.1
 *
 * @package Dokan
 */
class Wlr_Upgrade {

    /** @var array DB updates that need to be run */
    private static $updates = [
        '2.0'    => 'upgrades/wlr-upgrade-2.0.php',
    ];

    /**
     * Constructor loader function
     *
     * Load autometically when class instantiate.
     *
     * @since 1.0
     */
    function __construct() {
        add_action( 'admin_notices', array( $this, 'show_update_notice' ) );
        add_action( 'admin_init', array( $this, 'do_updates' ) );
    }

    /**
     * Check if need any update
     *
     * @since 1.0
     *
     * @return boolean
     */
    public function is_needs_update() {
        $installed_version = get_option( 'wlr_plugin_version' );

        if ( version_compare( $installed_version, WLR_PLUGIN_VERSION, '<' ) ) {
            return true;
        }

        return false;
    }

    /**
     * Show update notice
     *
     * @since 1.0
     *
     * @return void
     */
    public function show_update_notice() {
        if ( ! current_user_can( 'update_plugins' ) || ! $this->is_needs_update() ) {
            return;
        }

        $installed_version = get_option( 'wlr_plugin_version' );
        $updates_versions  = array_keys( self::$updates );

        if ( ! is_null( $installed_version ) && version_compare( $installed_version, end( $updates_versions ), '<' ) ) {
            ?>
                <div id="message" class="updated">
                    <p><?php _e( '<strong>Data Update Required</strong> &#8211; We need to update your install to the latest version', 'wlr' ); ?></p>
                    <p class="submit"><a href="<?php echo add_query_arg( [ 'wlr_do_update' => true ], $_SERVER['REQUEST_URI'] ); ?>" class="wlr-update-btn button-primary"><?php _e( 'Run the updater', 'wlr' ); ?></a></p>
                </div>

                <script type="text/javascript">
                    jQuery('.wlr-update-btn').click('click', function(){
                        return confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'wlr' ); ?>' );
                    });
                </script>
            <?php
        } else {
            update_option( 'wlr_plugin_version', WLR_PLUGIN_VERSION );
        }
    }


    /**
     * Do all updates when Run updater btn click
     *
     * @since 1.0
     *
     * @return void
     */
    public function do_updates() {
        if ( isset( $_GET['wlr_do_update'] ) && $_GET['wlr_do_update'] ) {
            $this->perform_updates();
        }
    }


    /**
     * Perform all updates
     *
     * @since 1.0
     *
     * @return void
     */
    public function perform_updates() {
        if ( ! $this->is_needs_update() ) {
            return;
        }

        $installed_version = get_option( 'wlr_plugin_version' );

        foreach ( self::$updates as $version => $path ) {
            if ( version_compare( $installed_version, $version, '<' ) ) {
                include WLR_INC . '/' . $path;
                update_option( 'wlr_plugin_version', $version );
            }
        }

        update_option( 'wlr_plugin_version', WLR_PLUGIN_VERSION );

        $location = remove_query_arg( ['wlr_do_update'], $_SERVER['REQUEST_URI'] );
        wp_redirect( $location );
        exit();
    }

}
