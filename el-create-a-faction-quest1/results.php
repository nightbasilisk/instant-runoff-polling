<!DOCTYPE html>
<meta charset="UTF-8"/>
<title>Results</title>
<link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">

<style type="text/css">
	.container { max-width: 750px; padding-bottom: 100px; }
	hr { border-style: dashed;}
</style>

<body>

<?php PHP_VERSION_ID > 50400 or die('Incompatible PHP version.') ?>
<?php $conf = include __DIR__.'/contest-settings.php' ?>

<div class="container">

	<h1>The Results</h1>

	<p><a href="#finalresult">Jump to Final Result</a></p>

	<p><b>Note 1</b>: Entry with no primary votes will not show at all!</p>
	<p><b>Note 2</b>: Last place tie-breaker rule used is "eliminate all."</p>

<?php

	$user = include realpath(__DIR__.'/..').'/vbulletin-userinfo.php';

	if ( ! $user['can.vote']) {
		echo "<p>Hi! you don't have enough access to view the poll results.</p>";
	}
	else { // authorized to view resuls

		// establish a database connection
		$dbc = $conf['database'];
		$db = new PDO($dbc['dbhost'], $dbc['username'], $dbc['password']);

		// get all the ranks
		$selectranks = $db->prepare('SELECT * FROM votes WHERE pollid = :pollid');
		$selectranks->execute([':pollid' => $conf['pollid']]);

		$allvotes = [];
		foreach ($selectranks as $row) {
			isset($allvotes[$row['userid']]) or $allvotes[$row['userid']] = [];
			$allvotes[$row['userid']][$row['rank']] = $row['oid'];
		}

		// sort values
		foreach ($allvotes as & $entry) {
			ksort($entry);
		}

		$totalvoters = count($allvotes);
		$winning_votes = intval(floor($totalvoters / 2)) + 1; // 50% + 1

		echo "<p><em>$totalvoters voters participated. 50%+1 votes are required to win (ie. $winning_votes).</em></p>";

		$round = 0;
		$eliminated = [];
		$maxrounds = $conf['maxrounds'];
		do { // loop until we're done
			$round += 1;
			echo "<hr/>";
			echo "<h2>Round $round</h2>";
			// tally votes
			$tally = [];
			foreach ($allvotes as & $votes) {
				$rank_keys = array_keys($votes);
				foreach ($rank_keys as $rank) {
					if (in_array($votes[$rank], $eliminated)) {
						// entry was already eliminated, cleanup
						unset($votes[$rank]);
						ksort($votes);
					}
					else { // not eliminated, got what we need
						isset($tally[$votes[$rank]]) or $tally[$votes[$rank]] = 0;
						$tally[$votes[$rank]] += 1;
						break;
					}
				}
			}

			$winner = [ 'votes' => -1, 'oid' => null ];
			$loser  = [ 'votes' => 99999999, 'oid' => null ];
			echo '<table class="table">';
			foreach ($tally as $oid => $votecount) {
				if ($winner['votes'] < $votecount) {
					$winner['oid'] = $oid;
					$winner['votes'] = $votecount;
				}
				if ($loser['votes'] > $votecount) {
					$loser['oid'] = $oid;
					$loser['votes'] = $votecount;
				}
				echo "<tr><td>{$conf['polloptions'][$oid]}</td><td>{$votecount}</td></tr>";
			}
			echo "</table>";
			echo "<br/>";

			if ($winner['oid'] == $loser['oid']) {
				echo '<span id="finalresult">Tie. Poll is undecisive.</span>';
				exit;
			}

			echo "<p><strong>Round Winner:</strong> {$conf['polloptions'][$winner['oid']]}</p>";

			foreach ($tally as $oid => $votecount) {
				if ($votecount == $loser['votes']) {
					echo "<p><strong>Eliminated:</strong> {$conf['polloptions'][$oid]}</p>";
					$eliminated[] = $oid;
				}
			}

			if ($winner['votes'] >= $winning_votes) {
				exit;
			}
		}
		while ($round < $maxrounds && count($eliminated) <= count($conf['polloptions']) - 1);
		echo '<span id="finalresult">&nbsp;</span>';
	}
