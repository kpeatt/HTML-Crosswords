<script type="text/javascript">

    	    
    var resizePuzzle = function($puzzle) {
       	       
       var navbarHeight = $('.navbar').height() + $('.subnav').height();
       
       $('#puzzle').height($(window).height() - $('#puzzle .well').height() - 40);
    
       var height = (($('#puzzle').height() - navbarHeight) * 0.95);
	   $puzzle.width(height).height(height);
	    
	   var $cell = $puzzle.find('td');
	    
	   $cell.height($cell.width());
    }
    
    $(function(){
        	
        var answerKey = "<?php echo $puzzle['answerstring']; ?>";
        
        var checkAnswer = function(item) {
	        
	        var currentCell = item.attr('id').substr(5);
	        var response = item.val().toLowerCase();
            var answer = answerKey.charAt(currentCell-1).toLowerCase();
	        
	        if (response === answer) {
                item.removeClass('wrong').addClass('right');
            } else if (response === '') {
                item.removeClass('wrong').removeClass('right');
            } else {
                item.removeClass('right').addClass('wrong');
            }
            
            $('#puzzle td').removeClass('hilite'); // Remove highlights
			$('#clues li').removeClass('active');
	        
        }
        
    	$('input.answer').each(function() { // Check answers on page load
    		checkAnswer($(this));
    	});
         
        $('input.answer').blur(function() { // Highlight correct/incorrect answers
            checkAnswer($(this));
        });
        
        var direction = 'across'; // People type Across by default
        
        $('input.answer').dblclick(function() { // Change puzzle entry direction on double click
        
        	if (direction == 'across') {
        		direction = 'down';
        	} else {
        		direction = 'across';
        	}
        	        	
        	updateCurrentCluefromCell($(this));
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
					
					if (cellY > $('#puzzle tr').length) {
						break;
					}
				}
				
				$('#clues .down').find('li[value="'+item.attr('down')+'"]').addClass('active');
			
			}
	    
	    }
        
		$('input.answer').focus(function() {  // Highlight clue spaces
		    
		    var $this = $(this);
		          	        		
    		hiliteClue($this);
    		updateCurrentCluefromCell($this);
		    			
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
				activeCell = item.closest('tr').prev('tr').children('td.space').last().find('input.answer');
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
			
			if (cellX > $('#puzzle tr').length) {
				activeCell = item.closest('tr').next('tr').children('td.space').first().find('input.answer');
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
				
				if (cellY > $('#puzzle tr').length) {
					cellY = $('#puzzle tr').length;
					break;
				}
				
				activeCell = $('input.answer[celly=' + cellY + '][cellx=' + cellX + ']');
			}
			
			return activeCell;
			
		}

		$('input.answer').keydown(function(e) { //Keyboard Navigation
		
			var $this = $(this);
		
			var activeCell = $this;
			var cellX = parseInt($this.attr('cellx'));
			var cellY = parseInt($this.attr('celly'));
			
			if (e.metaKey) {
				return false;
			}
				 
			switch(e.keyCode) {
				case 37: // Left
										
					activeCell = leftCell($this);
					
			    	break;
		    	
		    	case 38: // Up
			        
			        activeCell = upCell($this);
								       	
					break;
					
				case 39: // Right
					
					activeCell = rightCell($this);
					
			    	break;
			    	
			    case 40:
			        // Down
			        
			   		activeCell = downCell($this);
			       	
					break;
					
				case 8:
					// Backspace
					
					if ($this.val() != '') {
						$this.val('');
					} else {
						if (direction == 'across') {
				    		activeCell = leftCell($this);
				    		$(activeCell).val('');
				    	} else if (direction == 'down') {
				    		activeCell = upCell($this);
				    		$(activeCell).val('');
				    	}
					}
					
					break;
			    
			    case 9:
					// Tab
					
					if (!event.shiftKey) {
						
						if (direction == 'down') {
				    		activeCell = downCell($this);
				    		
				    		if(e.preventDefault) {
				                e.preventDefault();
				            }
				    	} else {
				    		activeCell = rightCell($this);
				    		if(e.preventDefault) {
				                e.preventDefault();
				            }
				    	}
						
					} else {
					
						if (direction == 'down') {
				    		activeCell = upCell($this);
				    		
				    		if(e.preventDefault) {
				                e.preventDefault();
				            }
				    	} else {
				    		activeCell = leftCell($this);
				    		if(e.preventDefault) {
				                e.preventDefault();
				            }
				    	}
					
					}
			    				
					break;	        		        	
	        		        
		    }
		    
		    if (($this.val().length == 0) && (e.keyCode >= 65 && e.keyCode <= 90)) {
		    
		    	$this.val(String.fromCharCode(e.keyCode));
		    	
		    	if (direction == 'across') {
		    		activeCell = rightCell($this);
		    	} else if (direction == 'down') {
		    		activeCell = downCell($this);
		    	}
		    	
		    	if(e.preventDefault) {
	                e.preventDefault();
	            }
		    }
		    
		    if (($this.val().length >= 1) && (e.keyCode >= 65 && e.keyCode <= 90)) {
		    	
		    	$this.val(String.fromCharCode(e.keyCode));
		    	
		    	if (direction == 'across') {
		    		activeCell = rightCell($this);
		    		
		    	} else if (direction == 'down') {
		    		activeCell = downCell($this);
		    	}
		    	
		    	if(e.preventDefault) {
	                e.preventDefault();
	            }
		    	
		    }
		    
		    if (!(e.keyCode >= 65 && e.keyCode <= 90)) {
		    	if(e.preventDefault) {
	                e.preventDefault();
	            }
		    }
		    
		    activeCell.focus();	    
		    hiliteClue(activeCell);
		    updateCurrentCluefromCell(activeCell);

	    });
	    
	    var findCellfromClue = function(item) {
	    	
	    	direction = item.closest('div').attr('class').split(' ')[0];
	    	var clueNumber = item.attr('value');
	    	
	    	activeCell = $('#puzzle').find('input['+direction+'='+clueNumber+']').first();
	    		    	
	    	return direction;
	    	return activeCell;
	    }
	    
	    var findCluefromCell = function(item) {
	    	activeClue = $('#clues .'+direction).find('li[value="'+item.attr(direction)+'"]');
	    	
	    	return activeClue;
	    }
	    
	    var updateCurrentCluefromList = function(item) {
	    	
	    	direction = item.closest('div').attr('class').split(' ')[0];
	    	var clueNumber = item.attr('value');
	    	var clueText = item.html();
	    	
	    	$('.current-clue span.number').html(clueNumber);
	    	$('.current-clue span.text').html(clueText);
	    	
	    	if (direction == 'across') {
	    		$('.current-clue span.direction i').attr('class', 'icon-arrow-right');
	    	} else {
	    		$('.current-clue span.direction i').attr('class', 'icon-arrow-down');
	    	}
	    	
	    }
	    
	    var updateCurrentCluefromCell = function(item) {
	    
			item = $('#clues .'+direction).find('li[value="'+item.attr(direction)+'"]');
	    	
	    	var clueNumber = item.attr('value');
	    	var clueText = item.html();
	    	
	    	$('.current-clue span.number').html(clueNumber);
	    	$('.current-clue span.text').html(clueText);
	    	
	    	if (direction == 'across') {
	    		$('.current-clue span.direction i').attr('class', 'icon-arrow-right');
	    	} else {
	    		$('.current-clue span.direction i').attr('class', 'icon-arrow-down');
	    	}
	    	
	    }
	    
	    $('#clues li').click(function() {
	    	findCellfromClue($(this));
	    	updateCurrentCluefromList($(this));
	    	
	    	activeCell.focus();	    
		    hiliteClue(activeCell);
	    });
	    	    
	    /* Ajax Save !TODO: Make this actually ajax... D: */
	    
	    $('#ajax-save').click(function() { 
	    
	    	document.forms["save-puzzle"].submit();
	    	return false;
	    
	    });
	    
    	resizePuzzle($('#puzzle table'));
	    
            
    });
    
    $(window).resize(function() {
    	resizePuzzle($('#puzzle table'));
    });
    
</script>