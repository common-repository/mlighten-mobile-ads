<?php

/*
    Plugin Name: mLighten Mobile Ads
    Plugin URI: http://www.mlighten.com
    Description: The mLighten Mobiles Ads plugin enables non-invasive mobile banners to be displayed on your site without the need for custom code or modification to your template files.  mLighten ads will "stick" at the bottom of your page and allow the user to close or dismiss them at any time.  Banners are optimized for display based on the capabilities of the device and will only show on your mobile traffic.  No longer let your mobile traffic go un-monetized!
    Version: 1.3
    Author: mLighten
    Author URI: http://www.mlighten.com
    License: GPL2
*/

if( is_admin() ) {
  include( "mlighten-admin.php" );
}

define( 'MLIGHTEN__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Global Variables
//
$mlighten_options = array(
    "adactive"    => get_option("adactive"),
    "adpartnerid" => get_option("adpartnerid") );

// Check if mLighten ads are active and add to footer
//
if( $mlighten_options['adactive'] == 1 ) {
    add_action('wp_footer', 'add_mlighten_script');
}

// mLighten sticky ad script
//
function add_mlighten_script()
{
    global $mlighten_options;
    $partnerid = $mlighten_options['adpartnerid'];
    $mlighten__plugin_url = rtrim(MLIGHTEN__PLUGIN_URL, '/');
$adcode = <<<EOD
<script id="mlightenStickyJs" type="text/javascript" src="{$mlighten__plugin_url}/js/mlighten.sticky.js"></script>
<div id="mlightenBanner">
   <script id="mlightenCoreJs" type="text/javascript" src="{$mlighten__plugin_url}/js/mlighten.js"></script>
   <script> _mlsticky = { partner: "{$partnerid}" } </script>
</div>
EOD;
    echo $adcode;
}
