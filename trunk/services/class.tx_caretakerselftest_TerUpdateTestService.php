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

		$config = $this->getConfiguration();
		
		$mTime = filemtime(PATH_site . 'typo3temp/extensions.xml.gz');
		$age   = time() - $mTime;
		
		if ( $age > $config['maxAge'] ){
			return tx_caretaker_TestResult::create( tx_caretaker_Constants::state_error, $age, 'TER-Update is due. Last update was  on '.strftime ( '%x %X' , $mTime)  );
		} else {
			return tx_caretaker_TestResult::create( tx_caretaker_Constants::state_ok, $age, 'TER is up to Date. Last update was  on '.strftime ( '%x %X' , $mTime) );
		}
		
	}

	private function getConfiguration(){
		return array(
			'maxAge' => intval( $this->getConfigValue('maxAge') )
		);
	}
}
?>
