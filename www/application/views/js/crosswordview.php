<script type="text/javascript">
    $(function(){
    
    	$('#puzzle td').height($('#puzzle td').width());
    	
        var answerKey = "<?php echo $puzzle['answerstring']; ?>";
         
        $('input.answer').blur(function() { // Highlight correct/incorrect answers
             
            var currentCell = $(this).attr('id').substr(5);
            var response = $(this).val().toLowerCase();
            var answer = answerKey.charAt(currentCell-1).toLowerCase();
            
            if (response === answer) {
                $(this).removeClass('wrong').addClass('right');
            } else if (response === '') {
                $(this).removeClass('wrong').removeClass('right');
            } else {
                $(this).removeClass('right').addClass('wrong');
            }
             
        });
        
        var direction = 'across';
        
        $('input.answer').dblclick(function() { // Change puzzle entry direction on double click
        
        	if (direction == 'across') {
        		direction = 'down';
        	} else {
        		direction = 'across';
        	}
        	
        	alert(direction);
        
        });
        
		$('input.answer').focus(function() {  // Highlight clue spaces
		      	        	        		
		    $('#puzzle td').removeClass('hilite');
		    
		    if (direction == 'across') {
			    $(this).closest('td').addClass('hilite');
				$(this).closest('td').nextUntil('td.black').addClass('hilite');
				$(this).closest('td').prevUntil('td.black').addClass('hilite');
			} else { 
			
				var cellY = parseInt($(this).attr('celly'));
				var cellX = parseInt($(this).attr('cellx'));
								
				var hiliteCellUp = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
				var hiliteCellDown = hiliteCellUp;
				
				while (hiliteCellUp.length > 0) {
					cellY = cellY - 1;
					hiliteCellUp = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
					hiliteCellUp.closest('td').addClass('hilite');
					
					if (cellY <= 0) {
						break;
					}
				}
				
				while (hiliteCellDown.length > 0) {
					cellY = cellY + 1;
					hiliteCellDown = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
					hiliteCellDown.closest('td').addClass('hilite');
					
					if (cellY > <?php echo $puzzle['meta']['height']; ?>) {
						break;
					}
				}
			
			}
			
		});
		
		$('input.answer').blur(function() { // Remove highlight
			
			$('#puzzle td').removeClass('hilite');
			
		});

		$('input.answer').keydown(function(e) { //Keyboard Navigation
		
			var activeCell = $(this);
			var cellX = parseInt($(this).attr('cellx'));
			var cellY = parseInt($(this).attr('celly'));
				 
			switch(e.keyCode) {
				case 37:
					// Left
					
					cellX = cellX - 1;
					
					activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
					
					if (activeCell.length == 0) {
						
						$(this).closest('td').prevUntil('td.space').each(function(i) {
							cellX = cellX - 1;
						});
						
						activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
						
					}
					
					if (cellX <= 0) {
						activeCell = $(this).closest('tr').prev('tr').find('td.space:last input.answer');
					}
					
			    	break;
		    	
		    	case 38:
			        // Up
			        
			        cellY = cellY - 1;
			        
			        activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
			       	
			       	while (activeCell.length == 0) {
						cellY = cellY - 1;
						
						if (cellY <= 0) {
							cellY = 1;
							break;
						}
						
						activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');

					}
								       	
					break;
					
				case 39:
					// Right
					
					cellX = cellX + 1;
					
					activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
					
					if (activeCell.length == 0) {
					
						$(this).closest('td').nextUntil('td.space').each(function(i) {
							cellX = cellX + 1;
						});
						
						activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
					}
					
					if (cellX > <?php echo $puzzle['meta']['width']; ?>) {
						activeCell = $(this).closest('tr').next('tr').find('td.space:first input.answer');
					}
					
			    	break;
			    	
			    case 40:
			        // Down
			        
			        cellY = cellY + 1;
			       	
			       	activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
			       	
			       	while (activeCell.length == 0) {
						cellY = cellY + 1;
						
						if (cellY > <?php echo $puzzle['meta']['width']; ?>) {
							cellY = <?php echo $puzzle['meta']['width']; ?>;
							break;
						}
						
						activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
					}
			       	
					break;
					
				case 8:
					// Backspace
					
					if ($(this).val() != '') {
						$(this).val('');
					}
					
					else {
						cellX = cellX - 1;
						activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
						
						if (activeCell.length == 0) {
						
							$(this).closest('td').prevUntil('td.space').each(function(i) {
								cellX = cellX - 1;
							});
							
							activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
							
						}
						
						if (cellX <= 0) {
							activeCell = $(this).closest('tr').prev('tr').find('td.space:last input.answer');
						}
					}
					
					break;
			    
		        		        	
	        		        
		    }
		    
		    activeCell.focus();
	    
	    });
            
    });
    
    $(window).resize(function() {
    	$('#puzzle td').height($('#puzzle td').width());
    });
    
</script>