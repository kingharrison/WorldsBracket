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
	if(isset($_POST["submittedButton"]) && isset($bracket) && isset($CURRENT_USER))
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
		<div class="alert alert-info" role="alert">Please enter your selections for Top 5 in each division. If a team is missing, please message <a href="<?php echo $config['fierceboardUrl'] ?>conversations/add?to=Ashley" target="_blank">Ashley</a>.
		<br/><br/>
		To select a team, start typing the team name in the textbox. A dropdown will appear with matching options. There may be a slight delay before the list of teams appears.
		</div>
		
		
		<form class="form-horizontal" method="post" name="bracket-form" enctype="multipart/form-data">
		<?php
		foreach($divisions as $div){
		?>
			<div class="chart">
				<?php
				$divId = $div['DivisionId'];
				$divName = $div['DivisionName'];
				
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
						$hiddenInputVal = $entries[$i]['TeamName'];
					}
				?>		
				<div class="form-group">
				    <label class="col-sm-1 col-md-1 control-label">
						<?php echo $i ?>.
					</label>
				    <div class="col-sm-11 col-md-9">
				      <input type="text" class="form-control auto-complete" name="<?php echo $textInputName ?>" value="<?php echo $textInputVal ?>" data-division="<?php echo $divId?>" data-placement="<?php echo $i?>">
					   <input type="hidden" class="hidden-ac-val" name="<?php echo $hiddenInputName ?>" value="<?php echo $hiddenInputVal ?>" data-division="<?php echo $divId ?>" data-placement="<?php echo $i?>">
				    </div>
			  	</div>
						<?php
					}
					echo '</div>';	
		}
		?>
		
		<input type="submit" text="save" class="btn btn-primary" name="submittedButton">

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
		
	    $( ".auto-complete" ).each(function(i, el) {
			 // the input element
			 var el = $(el);
			 
			 // load the list of teams from RTW if it's not already stored
			 var divId = el.data('division');
			 if(!dict[divId])
			 {
	  	         $.ajax({
					 url: "<?php echo $config['rtwServiceUrl'] ?>GetBidWinners?divisionid=" + divId,
					 dataType: "jsonp",
					 divisionId: divId,
					 success: function( data ) {
						 dict[this.divisionId] = data;
					 }
				 });
		 	}
			
		 	// selected value
		 	var elVal = el.siblings('.hidden-ac-val').first();
		 
			// set up autocomplete
			el.autocomplete({
				 select: function(e, ui) {
					// set the hidden field value to the be the teamid and the label to be the text
					e.preventDefault()
				 	elVal.val(ui.item.value);
					$(this).val(ui.item.label);
				 },
				 focus: function( e, ui ) {
 					e.preventDefault()
 				 	elVal.val(ui.item.value);
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
	
