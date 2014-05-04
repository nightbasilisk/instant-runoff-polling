<?php

	// This is just a mock for testing; hopefully I got the vbulletin object
	// model right from my research

	class vbulletin {
		public $userinfo = array
			(
				'userid' => '1',
				'membergroupids' => '1,7,3'
			);
	}

	$vbulletin = new vbulletin;
