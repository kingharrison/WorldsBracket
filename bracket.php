<?php 
	$pagetitle = "Fierceboard Bracket Contest";
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	include $root . '/worldsbracket/header.php';
	
	$name = NULL;
	$enddate = NULL;
	// get the bracket members
	$divisions = [];
	
	if(isset($_GET['id']))
	{
		$bracketid = $_GET['id'];
	
		// get the bracket name
		if($stmt = $mysqli->prepare("SELECT MatchName, EndDate FROM BracketMatch WHERE matchid = ?")) {
			$stmt->bind_param('i', $bracketid);
			$res =$stmt->execute();
	   
		    $stmt->bind_result($name, $enddate);
		
		    if($stmt->fetch()) {
		           $pagetitle = $name . ' Bracket';
		    }	
		
			$stmt->close();
		}
		
		// get the bracket divisions
		if($stmt = $mysqli->prepare("SELECT DivisionId, DivisionName FROM VW_Matches WHERE matchid =  ?")) {
			$stmt->bind_param('i', $bracketid);
			$res =$stmt->execute();
	
		    $stmt->bind_result($divisionid, $divisionname);

		    while($stmt->fetch()) {
				array_push($divisions, array("DivisionId"=> $divisionid, "DivisionName" => $divisionname));
           
		    }
			$stmt->close();
		}
	}
	
	
	if(isset($_POST["submittedButton"]))
	{
		
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
		<div class="alert alert-info" role="alert">Please enter your selections for Top 5 in each division. If a team is missing, please message <a href="http://board.fierce-brands.com/conversations/add?to=Ashley" target="_blank">Ashley</a>.</div>
		
		
		<form class="form-horizontal" method="post" name="bracket-form" enctype="multipart/form-data">
		<?php
		foreach($divisions as $div){
		?>
			<div class="chart">
				<h3><?php echo $div["DivisionName"] ?></h3>
	
				<?php

					for($i = 1; $i < 6; $i++)
					{
				?>		
				<div class="form-group">
				    <label class="col-sm-1 col-md-1 control-label">
				<?php echo $i ?>.
					</label>
				    <div class="col-sm-11 col-md-9">
				      <input type="text" id="textdiv_<?php echo $div['DivisionId'] ?>_pos_<?php echo $i?>" class="form-control auto-complete" data-division="<?php echo $div['DivisionId'] ?>" data-placement="<?php echo $i?>">
					   <input type="hidden" id="hiddendiv_<?php echo $div['DivisionId'] ?>_pos_<?php echo $i?>" class="hidden-ac-val" data-division="<?php echo $div['DivisionId'] ?>" data-placement="<?php echo $i?>">
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
	     $( ".auto-complete" ).each(function(i, el) {
			 el = $(el);
			 elVal = el.siblings('.hidden-ac-val').first();
			 el.autocomplete({
				 select: function(e, ui) {
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
					 var id = el.data('division');
		  	         $.ajax({
						 url: "http://theroadtoworlds.com/service/rtwservice.asmx/GetBidWinners?divisionid=" + id,
						 dataType: "jsonp",
						 searchToken: req.term,
						 success: function( data ) {
							 searchToken = this.searchToken;
							 newData = $.grep(data, function(n, i){ // just use arr
							   return n.FullTeamName.toLowerCase().indexOf(searchToken) > -1;
							 });
							 selectData = [];
							 for(var i = 0; i < newData.length; i++)
							 {
							 	selectData.push({label: newData[i].FullTeamName, value: newData[i].TeamId});
							 }
							 response( selectData.sort(SortByName));
						 }
					 });
				 }
			 })
		 });
	 });
</script>
		
	
<?php include $root . '/worldsbracket/footer.php'; ?>	
	
