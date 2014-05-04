<?php // license BSD-2, ie. do whatever you want

// this file is meant to be included by other files, since contest config
// is contest specific it needs to be passed to this file via the $conf var,
// just make sure it's declared before doing include 'path/to/this/script'

$get_vb_userinfo = function () use ($conf) {


	include_once $conf['vbulletin']['path.to.global.php'];

	$usergroups = explode(',', $vbulletin->userinfo['membergroupids']);

	$authorized_to_vote = false;
	foreach ($usergroups as $usergroup) {
		if (in_array($usergroup, $conf['vbulletin']['can.vote'])) {
			$authorized_to_vote = true;
			break;
		}
	}

	$authorized_to_viewresults = false;
	foreach ($usergroups as $usergroup) {
		if (in_array($usergroup, $conf['vbulletin']['can.view.results'])) {
			$authorized_to_viewresults = true;
			break;
		}
	}

	// we need this for vote duplication checks
	$userid = $vbulletin->userinfo['userid'];

	return array
		(
			'id' => $vbulletin->userinfo['userid'],
			'can.view.results' => $authorized_to_viewresults,
			'can.vote' => $authorized_to_vote
		);
};

$userinfo = $get_vb_userinfo();
unset($get_vb_userinfo);

return $userinfo;