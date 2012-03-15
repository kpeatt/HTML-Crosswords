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
        	
        	hiliteClue($(this));
        	$(this).focus();
        	        
        });
        
        var hiliteClue = function(item) {
		    
	    	$('#puzzle td').removeClass('hilite');
	    	$('#clues li').removeClass('active');
	    
	    	if (direction == 'across') {
			    item.closest('td').addClass('hilite');
				item.closest('td').nextUntil('td.black').addClass('hilite');
				item.closest('td').prevUntil('td.black').addClass('hilite');
				
				$('#clues .across').find('li[value="'+item.attr('across')+'"]').addClass('active');
				
			} else { 
			
				var cellY = parseInt(item.attr('celly'));
				var cellX = parseInt(item.attr('cellx'));
								
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
				
				$('#clues .down').find('li[value="'+item.attr('down')+'"]').addClass('active');
			
			}
	    
	    }
        
		$('input.answer').focus(function() {  // Highlight clue spaces
		      	        	        		
    		hiliteClue($(this));
		    			
		});
		
		$('input.answer').blur(function() { // Remove highlight
			
			$('#puzzle td').removeClass('hilite');
			
		});
		
		var leftCell = function(item) { // Move cell to the left
		
			var activeCell = item;
		
			var cellX = parseInt(item.attr('cellx'));
			var cellY = parseInt(item.attr('celly'));
			
			cellX = cellX - 1;
					
			activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
						
			if (activeCell.length == 0) {
				
				item.closest('td').prevUntil('td.space').each(function(i) {
					cellX = cellX - 1;
				});
				
				activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
								
			}
			
			if (cellX <= 0) {
				activeCell = item.closest('tr').prev('tr').find('td.space:last input.answer');
			}
			
			return activeCell;
			
		}
		
		var rightCell = function(item) { // Move cell right
		
			var activeCell = item;
		
			var cellX = parseInt(item.attr('cellx'));
			var cellY = parseInt(item.attr('celly'));
		
			cellX = cellX + 1;
					
			activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
			
			if (activeCell.length == 0) {
			
				item.closest('td').nextUntil('td.space').each(function(i) {
					cellX = cellX + 1;
				});
				
				activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
			}
			
			if (cellX > <?php echo $puzzle['meta']['width']; ?>) {
				activeCell = item.closest('tr').next('tr').find('td.space:first input.answer');
			}
			
			return activeCell;
		
		}
		
		var upCell = function(item) { // Move cell up
		
			var activeCell = item;
		
			var cellX = parseInt(item.attr('cellx'));
			var cellY = parseInt(item.attr('celly'));
			
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
			
			return activeCell;
			
		}
		
		var downCell = function(item) { // Move cell down
		
			var activeCell = item;
		
			var cellX = parseInt(item.attr('cellx'));
			var cellY = parseInt(item.attr('celly'));
			
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
			
			return activeCell;
			
		}

		$('input.answer').keydown(function(e) { //Keyboard Navigation
		
			var activeCell = $(this);
			var cellX = parseInt($(this).attr('cellx'));
			var cellY = parseInt($(this).attr('celly'));
			
			if (e.metaKey) {
				exit;
			}
				 
			switch(e.keyCode) {
				case 37: // Left
					
					activeCell = leftCell($(this));
					
			    	break;
		    	
		    	case 38: // Up
			        
			        activeCell = upCell($(this));
								       	
					break;
					
				case 39: // Right
					
					activeCell = rightCell($(this));
					
			    	break;
			    	
			    case 40:
			        // Down
			        
			   		activeCell = downCell($(this));
			       	
					break;
					
				case 8:
					// Backspace
					
					if ($(this).val() != '') {
						$(this).val('');
					} else {
						if (direction == 'across') {
				    		activeCell = leftCell($(this));
				    	} else if (direction == 'down') {
				    		activeCell = upCell($(this));
				    	}
					}
					
					break;
			    
			    case 9:
					// Tab
					
					if (!event.shiftKey) {
						
						if (direction == 'down') {
				    		activeCell = downCell($(this));
				    		
				    		if(e.preventDefault) {
				                e.preventDefault();
				            }
				    	}
						
					} else {
					
						if (direction == 'down') {
				    		activeCell = upCell($(this));
				    		
				    		if(e.preventDefault) {
				                e.preventDefault();
				            }
				    	}
					
					}
			    				
					break;	        		        	
	        		        
		    }
		    
		    if (($(this).val().length == 0) && (e.keyCode >= 65 && e.keyCode <= 90)) {
		    
		    	$(this).val(String.fromCharCode(e.keyCode));
		    	
		    	if (direction == 'across') {
		    		activeCell = rightCell($(this));
		    	} else if (direction == 'down') {
		    		activeCell = downCell($(this));
		    	}
		    	
		    	if(e.preventDefault) {
	                e.preventDefault();
	            }
		    }
		    
		    if (($(this).val().length >= 1) && (e.keyCode >= 65 && e.keyCode <= 90)) {
		    	
		    	$(this).val(String.fromCharCode(e.keyCode));
		    	
		    	if (direction == 'across') {
		    		activeCell = rightCell($(this));
		    		
		    	} else if (direction == 'down') {
		    		activeCell = downCell($(this));
		    	}
		    	
		    }
		    
		    activeCell.focus();	    
		    hiliteClue(activeCell);

	    });
	    
	    
            
    });
    
    $(window).resize(function() {
    	$('#puzzle td').height($('#puzzle td').width());
    });
    
</script>