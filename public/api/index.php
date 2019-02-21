<?php

require("../../vendor/autoload.php");

/** Корневая папка API */
$_SERVER['SCRIPT_NAME'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__);


Luracast\Restler\Defaults::$useUrlBasedVersioning = true;
Luracast\Restler\Defaults::$crossOriginResourceSharing = true;
Luracast\Restler\Defaults::$composeClass = '\Fias\Api\Compose';
Luracast\Restler\Defaults::$cacheDirectory = $_SERVER['DOCUMENT_ROOT'] . '/../cache';

$r = new Luracast\Restler\Restler(getenv('APP_ENV') === 'production');
$r->setAPIVersion(1);
$r->addAuthenticationClass('Fias\Api\AccessControl');
$r->addAPIClass('Luracast\Restler\Resources');
$r->addAPIClass('Fias\Api\v1\Fias');


/** Удаляем хэдеры */
$r->on(
	[
		'respond' => function () {
				header_remove("X-Powered-CMS");
				header_remove("P3P");
				header_remove("X-Powered-By");
			}
	]
);

$r->handle();
