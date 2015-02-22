<?php 
	$pagetitle = "Fierceboard Bracket Contest";
	
	$root = $_SERVER['DOCUMENT_ROOT'] . '/worldsbracket/';
	include $root . 'header.php';
	include $root . '/worldsbracket/' . 'library/BracketEntry.php';
	
	if(isset($_GET['id']))
	{
		$bracketid = $_GET['id'];
		
		// get the bracket info/divisions
		$bracket = $BRACKET_DATA_BO->getBracket($bracketid);
		$divisions = $BRACKET_DATA_BO->getBracketDivisions($bracketid);
		
		$pagetitle = $bracket['MatchName'] . ' Bracket';
	}
	
	$textInputFormat = "div_{0}_pos_{1}_text";
	$idInputFormat = "div_{0}_pos_{1}_id";
	
	// on post back save the teams
	if(isset($_POST["btnSubmit"]) && isset($bracket) && isset($CURRENT_USER))
	{	
		foreach($divisions as $div) 
		{
			$divId = $div['DivisionId'];
			
			$entries = [];
			for($i = 1; $i < 6; $i++) 
			{
				$textInputName = str_replace('{1}', $i, str_replace('{0}', $divId, $textInputFormat));
				$hiddenInputName = str_replace('{1}', $i, str_replace('{0}', $divId, $idInputFormat));
				
				if(isset($_POST[$hiddenInputName]))
				{
					$entry = new BracketEntry();
					$entry->setUserId($CURRENT_USER["user_id"]);
					$entry->setBracketId($bracketid);
					$entry->setDivisionId($divId);
					$entry->setPosition($i);
					$entry->setTeamId($_POST[$hiddenInputName]);
					$entry->setTeamName($_POST[$textInputName]);
				
					$entries[$i] = $entry;
				}
			}
			
			$BRACKET_DATA_BO->addBracketEntries($entries, $bracketid, $divId, $CURRENT_USER["user_id"]);
			
			$isSaved = True;
			
		}
	}
	
	
	
?>

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
		<?php 
		if(isset($isSaved) && $isSaved == True)
		{
		?>
		<div class="alert alert-success" role="alert">
			Bracket saved!
	    </div>
		<?php
		}
		
		if (!isset($CURRENT_USER)) {
			echo '<div class="alert alert-warning" role="alert">Please <a href="' . $config['fierceboardUrl'] . 'login/">first log into fierceboard</a> to take part in the bracket contest</div>';
		}
				
		?>
		
		<div class="alert alert-info" role="alert">
			Please enter your selections for Top 5 in each division. If a team is missing, please message <a href="<?php echo $config['fierceboardUrl'] ?>conversations/add?to=Ashley" target="_blank">Ashley</a>.
			<br/><br/>
			To select a team, start typing the team name in the textbox. A dropdown will appear with matching options. There may be a slight delay before the list of teams appears.
		</div>
		
		<div class="alert alert-danger" style="display:none;" id="errorBox" role="alert">
			
		</div>
		
		
		<form class="form-horizontal" method="post" name="bracket-form" id="bracket-form" enctype="multipart/form-data">
		<?php
		foreach($divisions as $div) {
			$divId = $div['DivisionId'];
			$divName = $div['DivisionName'];
			
			echo '<div class="chart" data-division="' . $divId . '">'
		?>
			
				<?php
				
				
				$entries = $BRACKET_DATA_BO->getBracketEntriesDict($bracketid, $divId, $CURRENT_USER["user_id"]);
				
				echo "<h3>" . $divName . "</h3>";
				
				for($i = 1; $i < 6; $i++)
				{
					$textInputName = str_replace('{1}', $i, str_replace('{0}', $divId, $textInputFormat));
					$hiddenInputName = str_replace('{1}', $i, str_replace('{0}', $divId, $idInputFormat));
					
					$textInputVal = "";
					$hiddenInputVal = "";
					
					if(isset($entries[$i]))
					{
						$textInputVal = $entries[$i]['TeamName'];
						$hiddenInputVal = $entries[$i]['TeamId'];
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
					echo '</div>';	
		}
		
		if (isset($CURRENT_USER)) 
		{
		
		?>
		<input type="submit" text="save" class="btn btn-primary" name="btnSubmit" id="btnSubmit">
		
		<?php
		}
			
		?>

		</form>
	</div>
</div>
	
<style>
	#dashboard .chart {
		margin-bottom:20px !important;
	}
	.ui-autocomplete {
		max-height: 150px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
		font-size:.8em;
	}
	/* IE 6 doesn't support max-height
	* we use height instead, but this forces the menu to always be this tall
	*/
	* html .ui-autocomplete {
		height: 150px;
	}
</style>
		
<script>
	function SortByName(a, b){
		var aName = a.label.toLowerCase();
		var bName = b.label.toLowerCase(); 
		return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
	}
	
	$(function() {
		var dict = {};
		
		/*
		$('#bracket-form').submit(function() {
		});
		*/
		
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
					 	selectData.push({label: newData[i].FullTeamName, value: newData[i].TeamId});
					 }
					 
					 // send the select data to the response
					 response( selectData.sort(SortByName));
				 }
			 })
		 });
	 });
</script>
		
	
<?php include $root .  "/worldsbracket/" . 'footer.php'; ?>	
	
