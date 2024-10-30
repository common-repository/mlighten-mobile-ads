<?php

    add_action( 'admin_menu', 'mlighten_admin_menu' );

    function mlighten_plugin_action_links( $links, $file ) {
        if ( $file == plugin_basename( dirname(__FILE__).'/mlighten-mobile-ads.php' ) ) {
            if ( class_exists( 'Jetpack' ) ) {
                $links[] = '<a href="' . admin_url( 'admin.php?page=mlighten-config' ) . '">'.__( 'Settings' ).'</a>';
            }
            else {
                $links[] = '<a href="' . admin_url( 'plugins.php?page=mlighten-config' ) . '">'.__( 'Settings' ).'</a>';
            }
        }

        return $links;
    }

    add_filter( 'plugin_action_links', 'mlighten_plugin_action_links', 10, 2 );

    function mlighten_conf()
    {
        global $mlighten_options;

        $request = curl_init();
        $url = "http://api.mlighten.com/register.php?v=1.3&email=" . urlencode( wp_get_current_user()->user_email) . "&partner={$mlighten_options['adpartnerid']}";
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_TIMEOUT, 5);
        curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($request, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Connection: Close"));

        curl_exec($request);
        curl_close( $request );

        echo '<div class="wrap">';
        echo '<h1>mLighten Mobile Ads</h1>';

        echo '<form action="options.php" method="post">';
        settings_fields('mlighten_settings_group');
        
        // Displays Ad Activated Option
        echo '<p>Here you can turn on/off mLighten Mobile Ads on your WordPress Site.  To enable, just check the "Display Ads" check box and save.  Mobile ads will begin to display immediately on your site when being browsed from a mobile device.  You\'ll earn money when when your visitors view ads, "click" on ads, or download the advertised offers, giving you a wide variety of ways to make money from your traffic.  Check "Display Ads" and start making money today!</p>';
        echo '<p><label class="description" for="adactive" style="float: left; width: 80px; font-weight: bold;">Display Ads </label>';
        echo '<input type="checkbox" name="adactive" id="adactive" value="1"';
        checked(1, $mlighten_options['adactive']);
        echo ' /></p>';
        
        // Displays Partner ID input
        echo '<p>Make sure that you have a Partner ID entered into the "Partner ID" box.  By default, the Partner ID is generated from your email address.  If you already have a Partner ID from another site feel free to enter that.  We DO NOT recommend changing the default value as it may interfere with your ability to generate revenue from your ads.  For questions or assistance, please contact mLighten Customer Support  at: <a href="mailto:support@mlighten.com">support@mlighten.com</a></p>';
        echo '<label class="description" for="adpartnerid" style="float: left; width: 80px; font-weight: bold;">Partner ID: </label>';
        if( empty( $mlighten_options['adpartnerid'] ) ) {
          $mlighten_options['adpartnerid'] = md5(wp_get_current_user()->user_email);
        }
        echo '<input type="text" name="adpartnerid" id="adpartnerid" value="'. $mlighten_options['adpartnerid'] .'"/><br />';
        
        submit_button();
        echo '</form>';
        echo '</div>';

        if( $mlighten_options['adactive'] )
        {
          echo '<h2>Performance (Month to Date)</h2>';
          echo 'Below you will see your ad performance statistics for the current month.  Earnings are displayed as "Pending" until they can be validated by the advertiser.  Once validated, your earnings will appear in the "Earnings" column and be available for payment once the total reaches at least $50.  Any revenue not paid out will carry over to the next month.  For questions or to set up your payment details, please contact mLighten Customer Support at <a href="mailto:support@mlighten.com">support@mlighten.com</a></p>'; 
          $url = "http://api.mlighten.com/stats.php?v=1.3&partnerid={$mlighten_options['adpartnerid']}&format=html";
          $request = curl_init();
          curl_setopt($request, CURLOPT_URL, $url);
          curl_setopt($request, CURLOPT_HEADER, 0);
          curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($request, CURLOPT_TIMEOUT, 5);
          curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 5);
          curl_setopt($request, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Connection: Close"));

          $stats = curl_exec($request);
          curl_close( $request );

          echo $stats;
        }
    }
    
    function mlighten_register_settings()
    {
        register_setting( 'mlighten_settings_group', 'adactive' );
        register_setting( 'mlighten_settings_group', 'adpartnerid' );
    }

    add_action( 'admin_init', 'mlighten_register_settings' );

    function mlighten_admin_menu()
    {
        if ( class_exists( 'Jetpack' ) ) {
                add_action( 'jetpack_admin_menu', 'mlighten_load_menu' );
        } else {
                mlighten_load_menu();
        }
    }

    function mlighten_load_menu()
    {
        if ( class_exists( 'Jetpack' ) ) {
                add_submenu_page( 'jetpack', __( 'mLighten Mobile Ads' ), __( 'mLighten Mobile Ads' ), 'manage_options', 'mlighten-config', 'mlighten_conf' );
        }
        else {
                add_submenu_page('plugins.php', __('mLighten Mobile Ads'), __('mLighten Mobile Ads'), 'manage_options', 'mlighten-config', 'mlighten_conf');
        }
    }
