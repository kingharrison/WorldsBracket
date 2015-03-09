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
						 var bidType = 'At Large';
						 if(item.bidtype == 'FullPaid')
							 bidType = 'Full Paid';
						 else if (item.bidType == 'PartialPaid')
							 bidType = 'Partial Paid';
						 
		                 return $('<li>')
		                     .append('<a>' + item.label + '<span class="pull-right">(' + bidType + ')</span></a>')
		                     .appendTo(ul);
		             };
		         }
			 })
		 });
	 });