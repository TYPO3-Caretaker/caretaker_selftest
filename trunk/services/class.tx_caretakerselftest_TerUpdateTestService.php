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
class tx_caretakerselftest_TerUpdateTestService extends tx_caretaker_TestServiceBase {

	public function runTest() {

		if ( t3lib_div::int_from_ver( TYPO3_version ) < t3lib_div::int_from_ver( '4.5.0' ) ){
			return $this->runTest400();
		} else {
			return $this->runTest450();
		}
		
	}

	private function runTest400(){
		
		$config = $this->getConfiguration();

		$mTime = filemtime(PATH_site . 'typo3temp/extensions.xml.gz');
		$age   = time() - $mTime;
		
		if ( $age > $config['maxAge'] ){
			return tx_caretaker_TestResult::create( tx_caretaker_Constants::state_error, $age, 'TER-Update is due. Last update was on '.strftime ( '%x %X' , $mTime)  );
		} else {
			return tx_caretaker_TestResult::create( tx_caretaker_Constants::state_ok, $age, 'TER is up to Date. Last update was on '.strftime ( '%x %X' , $mTime) );
		}
		
	}

	private function runTest450(){
		$config = $this->getConfiguration();
		$minTimstamp = time() - $config['maxAge'];
		
		$repositories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows ( '*', 'sys_ter', '1' );
		
		$errors = array();
		$ok = array();

		foreach( $repositories as $repository ){
			if ( $repository['lastUpdated'] < $minTimstamp ){
				$errors[] = new tx_caretaker_ResultMessage( 'ERROR: Extension-repository ' . $repository['title'] . ' was not up to date. Last update was on ' . strftime ( '%x %X' ,  $repository['lastUpdated'] ) );
			} else {
				$ok = new tx_caretaker_ResultMessage( 'OK: Extension-repository ' . $repository['title'] . ' is up to date. Last update was on ' . strftime ( '%x %X' ,  $repository['lastUpdated'] ) );
			}
		}
		
		if ( count($errors) > 0 ){
			return tx_caretaker_TestResult::create( tx_caretaker_Constants::state_error, $age, count($errors) . ' extension repositories have to be updated. ' . count($ok)  . ' extension repositories are ok. ' );
		} else {
			return tx_caretaker_TestResult::create( tx_caretaker_Constants::state_ok, $age, count($ok)  . ' extension repositories are ok. ' );
		}

	}

	private function getConfiguration(){
		return array(
			'maxAge' => intval( $this->getConfigValue('maxAge') )
		);
	}
}
?>
