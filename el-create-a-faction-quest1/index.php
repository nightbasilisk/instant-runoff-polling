<!DOCTYPE html>
<meta charset="UTF-8"/>
<title>Instant-runoff Voting</title>
<link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="../jquery.html5sortable.js"></script>
<style type="text/css">
	.container { max-width: 750px; padding-bottom: 100px; }
	#contest-options { list-style-type: none; padding: 0; margin: 0; }
	.contest-vote { padding-bottom: 10px; }
	.contest-vote--input { width: 60px; display: inline-block;}
	.contest-vote--entry { padding-left: 10px; }
	.contest-vote, .sortable-placeholder { height: 44px; margin-bottom: 5px;}
	.sortable-placeholder { border: 1px dashed #666; }
</style>

<body>

<?php PHP_VERSION_ID > 50400 or die('Incompatible PHP version.') ?>
<?php $conf = include __DIR__.'/contest-settings.php' ?>

<div class="container">

	<div class="header">
		<ul class="nav nav-pills pull-right">
			<li><a href="<?php echo $conf['forumlink'] ?>">Back to Forum</a></li>
		</ul>
		<h1 class="text-muted"><?php echo $conf['pagename'] ?></h1>
	</div>

	<?php $user = include realpath(__DIR__.'/..').'/vbulletin-userinfo.php' ?>

	<div class="jumbotron">
		<h1>Cast your Vote!</h1>

		<p class="lead"><strong>Once submitted the vote is final.</strong> If you are confused on the poll format please read
			<a href="http://en.wikipedia.org/wiki/Instant-runoff_voting">Wikipedia: Instan-runoff voting</a> or watch the
			<a href="https://www.youtube.com/watch?v=3Y3jE3B8HsE">video explanation of how it works</a>.</p>
	</div>

	<?php if ( ! $user['can.vote']): ?>
		<p>Hi! please login before trying to vote in the poll. <3</p>
	<?php else: // authorized to vote ?>

		<?php
			// establish a database connection
			$dbc = $conf['database'];
			$db = new PDO($dbc['dbhost'], $dbc['username'], $dbc['password']);
			// check if user voted in poll
			$uservotes = $db->prepare('SELECT * FROM votes WHERE pollid = :pollid AND userid = :userid');
			$uservotes->execute([':pollid' => $conf['pollid'], ':userid' => $user['id']]);
			$result = $uservotes->fetchAll();
		?>

		<?php if (empty($result)): ?>

			<?php
				$values = [];
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					// read in ranks
					$rank = [];
					$ranks_used = [];
					foreach ($conf['polloptions'] as $oid => $entry) {
						if (isset($_POST["opt$oid"])) {
							$rankvalue = intval($_POST["opt$oid"]);
							if (in_array($rankvalue, $ranks_used)) {
								die('Sorry but you cant use the same rank for multiple entries');
							}
							else { // valid entry
								$ranks_used[] = $rankvalue;
								$rank[$oid] = $rankvalue;
							}
						}
						else { // the option is not set
							die('Sorry but your vote is invalid. Please hit back and try again.');
						}
					}

					// we now have the ranks in $rank and everything is okey
					// now we just add the vote along with rank

					$addrank = $db->prepare
						(
							'
								INSERT INTO votes (pollid, userid, oid, rank)
								VALUES (:pollid, :userid, :oid, :rank)
							'
						);

					$addrank->bindParam(':pollid', $conf['pollid'], PDO::PARAM_INT);
					$addrank->bindParam(':userid', $user['id'], PDO::PARAM_INT);

					foreach ($rank as $oid => $rankvalue) {
						$addrank->bindParam(':oid', $oid, PDO::PARAM_INT);
						$addrank->bindParam(':rank', $rankvalue, PDO::PARAM_INT);
						$addrank->execute();
					}

					?><p>Thank you for voting!</p><?php

					exit;
				}
				else { // $_SERVER['REQUEST_METHOD'] === 'GET'
					$rank = 1;
					foreach (array_keys($conf['polloptions']) as $oid) {
						$values[$oid] = $rank++;
					}
				}
			?>

			<!-- blank action redirects back to this script -->
			<form action='' method="POST">
				<p>Rank your choices in order of favorites where 1 is your favorite, 2 is your second favorite, etc.</p>
				<p><u>You can drag and drop</u> to order the choices.</p>
				<p><b>Tip</b>. To quaikly sort go 1 by 1 down drag things you like to the top, drag things you don't like to the bottom. Sort as you drag with others you've already dragged to the top or bottom. You'll only need one pass.</p>
				<hr/>
				<ul id='contest-options'>
					<?php foreach ($conf['polloptions'] as $oid => $entry): ?>
						<li class="contest-vote">
							<table><tr><td>
							<input type="number" class="contest-vote--input form-control"
							       name="opt<?php echo $oid ?>"
							       value="<?php echo $values[$oid] ?>">
							</td><td class="contest-vote--entry">
							<?php echo $entry ?>
							</td></tr></table>
						</li>
					<?php endforeach; ?>
				</ul>
				<hr/>
				<button type="submit" class="btn btn-primary">Vote!</button>
			</form>

			<script>
				$('#contest-options').sortable().bind('sortupdate', function() {
					// re-rank everything
					var rank = 1;
					$('.contest-vote--input').each(function () {
						var $entry = $(this);
						$entry.val(rank++);
					});
				});
			</script>

		<?php else: ?>
			<p>You've already voted. Sorry but we don't support vote updates at this time.</p>
			<hr/>
			<p><em>Looking for your votes? Here they are:</em></p>
			<?php
				$ranks = [];
				foreach ($result as $row) {
					$ranks[$row['rank']] = $row['oid'];
				}
				ksort($ranks);

				echo "<ol>";
				foreach ($ranks as $oid) {
					echo "<li>{$conf['polloptions'][$oid]}</li>";
				}
				echo "</ol>";

				echo "<p>Here's a bbcode version for sharing on the forum.</p>";
				echo "<pre>I voted,\n";
				echo "[list=1]\n";
				foreach ($ranks as $oid) {
					echo " [*] {$conf['polloptions'][$oid]}\n";
				}
				echo "[/list]";
				echo "</pre>";
			?>
		<?php endif; ?>

	<?php endif; ?>

</div>
