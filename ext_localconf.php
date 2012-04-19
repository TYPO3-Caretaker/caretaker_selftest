<?php

	// load Service Helper
include_once(t3lib_extMgm::extPath('caretaker').'classes/helpers/class.tx_caretaker_ServiceHelper.php');

	// register Tests
tx_caretaker_ServiceHelper::registerCaretakerService ($_EXTKEY , 'services' , 'tx_caretakerselftest_Instancelist'   ,'Selftest -> Instancelist', 'Test that Monitoring Setups for the given List of Instance-Urls exist and are enabled.' );
tx_caretaker_ServiceHelper::registerCaretakerService ($_EXTKEY , 'services' , 'tx_caretakerselftest_TerUpdate'      ,'Selftest -> TER Udpate', 'Test that TER is up to date' );

?>
