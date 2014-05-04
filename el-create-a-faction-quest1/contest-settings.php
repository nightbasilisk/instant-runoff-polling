<?php // license BSD-2, ie. do whatever you want

$rootpath = realpath(__DIR__.'/..');

return array
	(
		// [!!] settings values in this file are placeholders

		'pagename' => 'Instant-runoff Poll',
		'forumlink' => '#add-your-forum-link-here',
		'pollid' => crc32('endless-ledgend-quest-1-faction'),

		// how many rounds to run when tallying; just a fail-safe
		'maxrounds' => 1000,

		// order will be used as initial ranking
		'polloptions' => array
			(
				// you can add html if you need to; ideally in the case of this
				// contest use userid's as the id if it's not too much work

				// the following are placeholers for testing:
				1 => 'Cultists of the Eternal End: Religious, one holy city, minor faction-indoctrination, holy warfare - Nosferatiel',
				2 => 'The Sowers: Cybernetic, teamplay, terraform - godman85',
				3 => 'The Church of the Creators: Religious, dust-baptism, terrain-bonuses - Teslaflux',
				4 => 'Azi Dahaka: Dragonoid, high population disapproval, borough approval bonus - Shoreyo',
				5 => 'Viridiplantae: Plantfolk, only food/no industry, free boroughs, living cities, minor faction bonus - Wredniak2003',
				6 => 'Aveos: Birdpeople, defensive, counterattack - Hunter93',
				7 => 'The Maskari: Shapeshifters, guilds, espionage, camouflage - Rudest',
				8 => 'Wetkin: amphibian goblins, waterbound settling, unit looting, fodder/swarming - Polymathin',
				9 => 'The Forgotten Ones: Humanoid, religious, scientific, magic - odeboy1',
				10 => 'The Shadows of the Revenant: Spiritistic/shamanistic, necromancy, shrines - Xaeltos',
				11 => 'The Azuku: Humanoid, minor faction recruitment, diplomatic - chaos_rick',
				12 => 'The Peacebringers: Telepaths, slavery, converting villages to cities, no disapproval - Verdian',
				13 => 'Ubnud Muezzi: Elementals, minor faction bonus, high expansion disapproval, defensive, static - _theclamps_',
				14 => 'The Drifters: Elvish, teleportation, winter-immunity, roguish, no city districts - Mordius',
				15 => 'The Federation: Multicultural, minor faction bonus, diplomatic - RariShyZealot',
				16 => 'Auriga Fakäd: Elementals, no cities/mobile extractors, special techtree, winter-worsening - VieuxChat',
				17 => 'Londinium: Humanoid(?), big cities, one capital, special city placement - nightbasilisk',
			),

		'vbulletin' => array
			(
				'path.to.global.php' => "$rootpath/path/to/forum/global.php",
				// ideally only admins should view, since perf on results may be bad
				'can.view.results' => ['1', '2'], // <- these are just an example
				// elligible voters group
				'can.vote' => ['1', '2', '3', '4'],  // <- these are just an example
			),

		// contest database
		'database' => array
			(
				// db should be competely isolated from anything else
				'dbhost'   => 'mysql:host=localhost;port=3306;dbname=instantrunoffpolls',
				// db user should only have access to instantrunoffpolls
				'username' => 'root', // replace with a instantrunoffpolls_user
				'password' => 'root',
			),
	);