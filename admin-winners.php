<?php
	$pagetitle = "Manage Team Placements";

	// include these now in case we need to redirect the user
	$root = $_SERVER['DOCUMENT_ROOT'];
  	$worlds_bracket_home = $root . '/worldsbracket/';

	require_once($worlds_bracket_home . "library/include.php");

	if(!isset($CURRENT_USER) || $CURRENT_USER['is_staff'] == 0)
	{
		header("Location: index.php");
	}


	if(isset($_GET['id']))
	{
		$divId = $_GET['id'];
		$div = $BRACKET_DATA_BO->getDivision($divId);
	}


	// get the current round id from the query string, otherwise default to 0
	$rounds = $BRACKET_DATA_BO->getAllRounds();
	$roundid = 0;
	if(isset($_GET['roundid']))
	{
		$roundid = $_GET['roundid'];
	}
	else
	{
		$roundid = $rounds[0]["CompetitionRoundId"];
	}

	// some constants
	$season = $config['compYear'];
	$textInputFormat = "div_{0}_pos_{1}_text";
	$idInputFormat = "div_{0}_pos_{1}_id";
	$numEntries = 11;

	// on post back save the teams
	if((isset($_POST["btnSubmit"]) || isset($_POST["btnSubmitNext"]))  && isset($div) && isset($CURRENT_USER))
	{
		$entries = [];
		for($i = 1; $i < $numEntries; $i++)
		{
			$textInputName = str_replace('{1}', $i, str_replace('{0}', $divId, $textInputFormat));
			$hiddenInputName = str_replace('{1}', $i, str_replace('{0}', $divId, $idInputFormat));

			if(isset($_POST[$hiddenInputName]) && strlen($_POST[$hiddenInputName]) > 0)
			{
				$entry = [];
				$entry['Season'] = $season;
				$entry['RoundId'] = $roundid;
				$entry['DivisionId'] = $divId;
				$entry['Position'] = $i;
				$entry['UserId'] = $CURRENT_USER["user_id"];
				$entry['TeamId'] = $_POST[$hiddenInputName];
				$entry['TeamName'] = $_POST[$textInputName];

				$entries[$i] = $entry;
			}
		}

		$BRACKET_DATA_BO->addWorldsPlacements($entries, $roundid, $divId, $season);

		$isSaved = True;

		// redirect to next division
		if (isset($_POST["btnSubmitNext"]))
		{
			$nextId = $BRACKET_DATA_BO->getAllDivisions()[0]['DivisionId'];
			$next = $BRACKET_DATA_BO->getNextDivision($divId);
			if(isset($next) && isset($next['DivisionId'])) {
				$nextId = $next['DivisionId'];
			}

			$location = "admin-winners.php?id=" . $nextId . "&roundid=" . $roundid ;
			header("Location: " . $location);
		}
	}


	include $worlds_bracket_home . 'header.php';

	// get the standings
	$placements = $BRACKET_DATA_BO->getWorldsPlacementsDict($season, $roundid, $divId);

?>

<div id="wizard">
<div id="content">
	<div class="menubar">
		<div class="sidebar-toggler visible-xs">
			<i class="ion-navicon"></i>
		</div>
		<div class="page-title">
			<?php echo $pagetitle ?>
		</div>
	</div>

	<div class="content-wrapper">
		<div class="header" id="wizard-header">
			<div class="steps clearfix">
				<div>
					<?php
					// active class
					foreach($rounds as $round)
					{
						$href = 'admin-winners.php?id=' . $divId . '&roundid=' . $round['CompetitionRoundId'];

						$isactive = '';
						if($roundid >= $round['CompetitionRoundId'])
						{
							$isactive = ' active ';
						}

						// for some reason this chunk is blowing up, so echo out the html instead
						echo '<div class="step' . $isactive . '">';
						echo 	'<a href="' . $href . '">';
						echo 		$round['CompetitionRoundName'];
						echo 	'</a>';
						echo 	'<span></span>';
						echo "</div>";
						/*
						?>
							<div class="step <?php echo $isactive ?>">
								<a href="<?php echo $href ?>">
									<?php echo $round['CompetitionRoundName'] ?>
								</a>
								<span></span>
							</div>
						<?
						*/

					}
					?>
				</div>
			</div>
		</div>
		<div style="margin-bottom:120px;">
		</div>

		<?php
		if(isset($isSaved) && $isSaved == True)
		{
		?>
		<div class="alert alert-success" role="alert">
			Bracket saved!
	    </div>
		<?php
		}
		?>
		<div class="alert alert-danger" style="display:none;" id="errorBox" role="alert">
		</div>

		<form class="form-horizontal" method="post" name="bracket-form" id="bracket-form" enctype="multipart/form-data">

			<div class="chart" data-division="<?php echo $divId ?>">
				<h3><?php echo $div['DivisionName']?></h3>

				<?php
				for($i = 1; $i < $numEntries; $i++)
				{
					$textInputName = str_replace('{1}', $i, str_replace('{0}', $divId, $textInputFormat));
					$hiddenInputName = str_replace('{1}', $i, str_replace('{0}', $divId, $idInputFormat));

					$textInputVal = "";
					$hiddenInputVal = "";


					if(isset($placements[$i]))
					{
						$textInputVal = $placements[$i]['TeamName'];
						$hiddenInputVal = $placements[$i]['TeamId'];
					}
				?>
				<div class="form-group">
				    <label class="col-sm-1 col-md-1 control-label">
						<?php echo $i ?>.
					</label>
				    <div class="col-sm-11 col-md-9">
						<div class="input-wrapper">
				      <input type="text" class="form-control auto-complete" name="<?php echo $textInputName ?>" value="<?php echo $textInputVal ?>" data-division="<?php echo $divId?>" data-placement="<?php echo $i?>" />
					   <input type="hidden" class="hidden-ac-val" name="<?php echo $hiddenInputName ?>" value="<?php echo $hiddenInputVal ?>" data-division="<?php echo $divId ?>" data-placement="<?php echo $i?>" />
				   	</div>
				    </div>
			  	</div>
				<?php
				}
				?>
			</div>
			<?php
			if($CURRENT_USER['is_staff'] == 1) {
			?>
				<input type="submit" value="Save" class="btn btn-primary" name="btnSubmit" id="btnSubmit">

				<input type="submit" value="Save and Next" class="btn btn-primary pull-right" name="btnSubmitNext" id="btnSubmitNext">
			<?php
			}
			?>
		</form>
	</div>
</div>

</div>



<script>

	function SortByName(a, b){
		var aName = a.label.toLowerCase();
		var bName = b.label.toLowerCase();
		return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
	}

	$(function() {
		var dict = {};

		$('#bracket-form .chart').each(function(i, el) {
			 // load the list of teams from RTW
			 var divId = $(el).data('division');
 	         $.ajax({
				 url: "<?php echo $config['rtwServiceUrl'] ?>GetBidWinners?divisionid=" + divId,
				 dataType: "jsonp",
				 divisionId: divId,
				 divWrapper: $(el),
				 success: function( data ) {
					 // store the data
					 dict[this.divisionId] = data;

					 // iterate over the controls and store info in the data attr
					 this.divWrapper.find('.input-wrapper').each(function(i, el) {
						 var textInput = $(el).find('.auto-complete').first();
						 var hiddenInput = $(el).find('.hidden-ac-val').first();

						 // default the team-name data attr to blank
						 textInput.data('team-name','');

						 if($.trim(hiddenInput.val()) == '' || hiddenInput.val() == '0') {
							 // if the hidden input is blank or 0, clear the text
							 hiddenInput.val('');
							 textInput.val('');
						 }
						 else {
	 						// store the team name as a data attribute for validation later
	 					 	var match = $.grep(data, function(n, i) {
	 					 					  return n.TeamId == hiddenInput.val();
	 					 				});
	 						if (match.length > 0) {
	 							textInput.data('team-name', match[0].FullTeamName);
	 							textInput.val(match[0].FullTeamName); // update the text box val if the data changed at RTW
	 						}
						 }
					 });
				 }
			 });
		});

	    $( ".auto-complete" ).each(function(i, el) {
			// the input element
			var el = $(el);

 		 	// the hidden input
 		 	var elVal = el.siblings('.hidden-ac-val').first();

			// validate on blur
			el.blur(function(){
				if($(this).val() != $(this).data('team-name')) {
					$(this).closest('.form-group').addClass('has-error');
				}
				else {
					$(this).closest('.form-group').removeClass('has-error');
				}

				if($('.has-error').length > 0) {
					$('#errorBox').show();
					$('#errorBox').text('There is a problem with your team selection. Please fix the rows highlighted in red before you save.');
					$("#btnSubmit").prop('disabled', true);
				}
				else {
					$('#errorBox').hide();
					$("#btnSubmit").prop('disabled', false);
				}

			});


			// set up autocomplete
			el.autocomplete({
				 select: function(e, ui) {
					// set the hidden field value to the be the teamid and the label to be the text
					e.preventDefault()
				 	elVal.val(ui.item.value);
					$(this).data('team-name', ui.item.label)
					$(this).val(ui.item.label);
				 },
				 focus: function( e, ui ) {
 					e.preventDefault()
 				 	elVal.val(ui.item.value);
					$(this).data('team-name', ui.item.label);
					$(this).val(ui.item.label);

				 },
				 source: function(req, response) {
					 // get the division/data
					 var id = el.data('division');
					 data = dict[id];

					 // filter the data
					 var newData = $.grep(data, function(n, i){
					   return n.FullTeamName.toLowerCase().indexOf(req.term.toLowerCase()) > -1;
					 });

					 // now create a new array of data
					 var selectData = [];
					 for(var i = 0; i < newData.length; i++)
					 {
					 	selectData.push({label: newData[i].FullTeamName, value: newData[i].TeamId, bidtype:newData[i].BidType});
					 }

					 // send the select data to the response
					 response( selectData.sort(SortByName));
				 },
				 create: function () {
		             $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
						 var bidType = '';
						 if(item.bidtype == 'FullPaid')
							 bidType = 'Full Paid';
						 else if (item.bidtype == 'PartialPaid')
							 bidType = 'Partial Paid';
						 else if (item.bidtype == "AtLarge")
							 bidType = 'At Large';

		                 return $('<li>')
		                     .append('<a>' + item.label + '<span class="pull-right">(' + bidType + ')</span></a>')
		                     .appendTo(ul);
		             };
		         }
			 })
		 });
	 });

</script>


<?php include $root .  "/worldsbracket/" . 'footer.php'; ?>
