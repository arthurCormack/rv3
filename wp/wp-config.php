<?php
        define('DB_NAME', 'wp_ez2');
        define('DB_USER', 'zweb_dev');
        define('DB_PASSWORD', 'macasx!@#');
        define('DB_HOST', 'zoomercluster.cluster-ccqkhjv27i8h.us-east-1.rds.amazonaws.com');
        define('DB_CHARSET', 'utf8mb4');
        define('DB_COLLATE', '');
        define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/');
        define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST'] . '/');
        define('AUTH_KEY',         'n1mmqt`;i{eJEjrq5Pz<uq1:}!6}iQ$R jD1|V UMU9;b?-2yn<-#sct-I)GG9<z');
        define('SECURE_AUTH_KEY',  'th-u-bCOz4O0F{9X2|6)P&f#cJ:n@hZDNtDA0#+7aASk?NJ7E_00>4</5-Vo7d+(');
        define('LOGGED_IN_KEY',    'BE#;QrhlI|H;CkdSQg)mK=0qA2~;*_>8qFEpv<y47)Z|!Aw8NMdH78Kd<9q/(5FD');
        define('NONCE_KEY',        ':i82aANur]VpX5,Kl2V1yKT#wcdn-qNkoez?{o&tG[5V1)1%/<>GbDf9uw%8@o&l');
        define('AUTH_SALT',        'm&]EZ-Ue+K*+k|$y*Gdxu2xbqm}5;-*`bcESHe-[pdLvA>bo<MN!|X&jk6N-X.4J');
        define('SECURE_AUTH_SALT', '%*48+YJeez Y%6I1>t|^]w[cD.D?oE[^+Lkb>.1=qCSRXBm8/2u=yN,#6T><1z:)');
        define('LOGGED_IN_SALT',   '#vVGHAef2u>|tcOH#| 4,(<ui31o9PC8fI[a&:)|Mr)=@~+A<RAdh_C9b`K!`*>c');
        define('NONCE_SALT',       'H!PwKvY8:<kqr#h#sdku%T^S[}%>jg>>eN-O;dxSWgd}Ph4Hc!d4dUE=9_S5up~V');
        define('WP_MEMORY_LIMIT', '768M' ); 
        define('WP_MAX_MEMORY_LIMIT', '768M' );
        define('WP_AUTO_UPDATE_CORE', false );
        define('AUTOMATIC_UPDATER_DISABLED', true );
        
        $table_prefix  = 'wp_';
        define('WPLANG', '');
        define('WP_DEBUG', false);
          if ( !defined('ABSPATH') )
            define('ABSPATH', dirname(__FILE__) . '/');
        require_once(ABSPATH . 'wp-settings.php');
        if (strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) $_SERVER['HTTPS']='on';
        if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['REMOTE_ADDR'] = trim($ips[0]);
        } elseif ( isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']) ) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
        } elseif ( isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) ) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CLIENT_IP'];
        }