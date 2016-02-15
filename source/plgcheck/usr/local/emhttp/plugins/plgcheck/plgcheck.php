#!/usr/bin/php
<?
require_once '/usr/local/emhttp/webGui/include/Wrappers.php';

function download_url($url, $path = "", $bg = false){
  exec("curl --max-time 60 --silent --insecure --location --fail ".($path ? " -o '$path' " : "")." $url ".($bg ? ">/dev/null 2>&1 &" : "2>/dev/null"), $out, $exit_code );
  return ($exit_code === 0 ) ? implode("\n", $out) : false;
}

$var    = parse_ini_file("/var/local/emhttp/var.ini");
$server = strtoupper($var['NAME']);
$unraid = parse_plugin_cfg("dynamix",true);
$output = $unraid['notify']['plugin'];

if ( is_file("/boot/config/plugins/plgcheck/versions") ){
  $versions = json_decode(file_get_contents("/boot/config/plugins/plgcheck/versions"),true);
}

$pluginInstalled = array_diff(scandir("/var/log/plugins"),array(".",".."));

$flag = false;
foreach ($pluginInstalled as $plugin) {
  if ( pathinfo($plugin, PATHINFO_EXTENSION) != "plg" ) {
    continue;
  }
  $installedVersion = exec("/usr/local/emhttp/plugins/dynamix.plugin.manager/scripts/plugin version /var/log/plugins/$plugin");
  $pluginURL = exec("/usr/local/emhttp/plugins/dynamix.plugin.manager/scripts/plugin pluginURL /var/log/plugins/$plugin");
  $pluginName = exec("/usr/local/emhttp/plugins/dynamix.plugin.manager/scripts/plugin name /var/log/plugins/$plugin");
  download_url($pluginURL,"/tmp/pluginchecker.plg");

  $newVersion = exec("/usr/local/emhttp/plugins/dynamix.plugin.manager/scripts/plugin version /tmp/pluginchecker.plg");

  if ( strcmp($newVersion,$installedVersion) > 0) {
#    echo "found update for $pluginName\n";
    if ( strcmp($newVersion,$versions[$pluginName]) == 0) {
#      echo "already notified!\n"; 
    }else {
      $versions[$pluginName] = $newVersion;
      $flag = true;
      exec("/usr/local/emhttp/webGui/scripts/notify -e 'Plugin $server - $pluginName [$newVersion]' -s 'Notice [$server] - Version update $newVersion' -d 'A new version of $pluginName is available' -i 'normal $output'");
    }
  }

}

if ( $flag ) {
  exec("mkdir -p /boot/config/plugins/plgcheck");
  file_put_contents("/boot/config/plugins/plgcheck/versions",json_encode($versions, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}


?>

