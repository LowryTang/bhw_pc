<?php return array (
  'logs' => 
  array (
    'path' => 'logs/log',
    'type' => 'file',
  ),
  'DB' => 
  array (
    'type' => 'mysqli',
    'tablePre' => 'b_',
    'read' => 
    array (
      0 => 
      array (
        'host' => 'qdm-025.hichina.com',
        'user' => 'qdm0250016',
        'passwd' => 'yndxbxq916112',
        'name' => 'qdm0250016_db',
      ),
    ),
    'write' => 
    array (
      'host' => 'qdm-025.hichina.com',
      'user' => 'qdm0250016',
      'passwd' => 'yndxbxq916112',
      'name' => 'qdm0250016_db',
    ),
  ),
  'interceptor' => 
  array (
    0 => 'themeroute@onCreateController',
    1 => 'layoutroute@onCreateView',
  ),
  'langPath' => 'language',
  'viewPath' => 'views',
  'skinPath' => 'skin',
  'classes' => 'classes.*',
  'rewriteRule' => 'url',
  'theme' => 
  array (
    'pc' => 'ixiyo',
    'mobile' => 'ixiyo',
  ),
  'skin' => 
  array (
    'pc' => 'default',
    'mobile' => 'default',
  ),
  'timezone' => 'Etc/GMT-8',
  'upload' => 'upload',
  'dbbackup' => 'backup/database',
  'safe' => 'cookie',
  'safeLevel' => 'none',
  'lang' => 'zh_sc',
  'debug' => false,
  'configExt' => 
  array (
    'site_config' => 'config/site_config.php',
  ),
  'encryptKey' => 'fa0808f3af952f288885220b3cb33209',
)?>