<?php
/**
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 *
 * @author Christopher Schäpers <kondou@ts.unde.re>
 * @author Frank Karlitschek <frank@karlitschek.de>
 * @author Georg Ehrke <oc.list@georgehrke.com>
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Robin Appelman <robin@icewind.nl>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */
\OC_JSON::checkAdminUser();
\OC_JSON::callCheck();

if (!array_key_exists('appid', $_POST)) {
	\OC_JSON::error(array(
		'message' => 'No AppId given!'
	));
	return;
}

$appId = (string)$_POST['appid'];
$appId = OC_App::cleanAppId($appId);

$config = \OC::$server->getConfig();
$config->setSystemValue('maintenance', true);
try {
	$installer = \OC::$server->query(\OC\Installer::class);
	$result = $installer->updateAppstoreApp($appId);
	$config->setSystemValue('maintenance', false);
} catch(Exception $ex) {
	$config->setSystemValue('maintenance', false);
	OC_JSON::error(array('data' => array( 'message' => $ex->getMessage() )));
	return;
}

if($result !== false) {
	OC_JSON::success(array('data' => array('appid' => $appId)));
} else {
	$l = \OC::$server->getL10N('settings');
	OC_JSON::error(array('data' => array( 'message' => $l->t("Couldn't update app.") )));
}
