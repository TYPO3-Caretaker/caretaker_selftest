<?php

/***************************************************************
* Copyright notice
*
* (c) 2009 by n@work GmbH and networkteam GmbH
*
* All rights reserved
*
* This script is part of the Caretaker project. The Caretaker project
* is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id: class.tx_caretakerdummyTestService.php 28941 2010-01-18 09:48:04Z martoro $
 */

/**
 * Instancelist Test - Test that Monitoring Setups for the given List of Instance-Urls exist and are enabled
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker_selftest
 */
class tx_caretakerselftest_InstancelistTestService extends tx_caretaker_TestServiceBase {

	public function runTest() {

		$config = $this->getConfiguration();

			// get list of instances
		switch ($config['mode']){
			case 'plain' : 
				$instance_urls = explode ( chr(10), $config['instancelist_plain'] );
				break;
			case 'url'   :
				$url = $config['instancelist_url'];
				if ( strpos( '://' , $url) === false ) {
					$url = $this->getInstanceUrl() . $url;
				}
				$httpResult = $this->executeHttpRequest( $config['instancelist_url'] );
				if ( $httpResult['response'] && $httpResult['info']['http_code'] == 200 ){
					$instance_urls = explode ( chr(10), $httpResult['response'] );
				} else {
					return 	tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error, 0, 'Instancelist Url could not be fetched ' . $config['instancelist_url']  );
				}
				break;
			default:
				return 	tx_caretaker_TestResult::create(tx_caretaker_Constants::state_error, 0, 'Plase select how to get the Instancelist' );
			
		}

		$submessages = array();
		$state    = tx_caretaker_Constants::state_ok;

		foreach ($instance_urls as $instance_url){
			
			$instance_url = trim($instance_url);

				// ignore empty and commentlines
			if (strpos('#' , $instance_url) === 0 || $instance_url == ''  ) {
				break;
			}
			
				// find instances even if they use http-authentication 
			$instance_url_auth = str_replace ( '://' , '://%:%@' , $instance_url );

				// find instances even if the instance record uses https url
			$instance_url_https = str_replace ( 'http://' , 'https://' , $instance_url );

				// get Instance Record and check that the Instance is enabled
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', 'tx_caretaker_instance',
					'deleted=0 AND ( ' .
						' url = '. $GLOBALS['TYPO3_DB']->fullQuoteStr($instance_url, 'tx_caretaker_instance') .
						' OR ' .
						' url LIKE ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($instance_url_auth, 'tx_caretaker_instance') .
						' OR ' .
						' url = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($instance_url_https, 'tx_caretaker_instance') .
						' )'
					);

			if ( $instance = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
				if ($instance['hidden'] != 0){
					$submessages[] = new tx_caretaker_ResultMessage('Instance Record with URL ###VALUE_URL### was hidden.' , array( 'url' => $instance_url ) );
					if ($state < tx_caretaker_Constants::state_error ) $state = tx_caretaker_Constants::state_error;
				}
			} else {
				$submessages[] = new tx_caretaker_ResultMessage('No Instance Record with URL ###VALUE_URL### found.' , array( 'url' => $instance_url ) );
				if ($state < tx_caretaker_Constants::state_error ) $state = tx_caretaker_Constants::state_error;
			}

		}


		return 	tx_caretaker_TestResult::create( $state, 0, 'Instancelist Selftest returned ###STATE###' , $submessages );
		
	}

	public function getConfiguration(){
		return array(
			'mode' => $this->getConfigValue('mode'),
			'instancelist_url' => $this->getConfigValue('instancelist_url'),
			'instancelist_plain' => $this->getConfigValue('instancelist_plain')
		);
	}
	


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/services/class.tx_caretaker_typo3_extensions.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/services/class.tx_caretaker_typo3_extensions.php']);
}
?>