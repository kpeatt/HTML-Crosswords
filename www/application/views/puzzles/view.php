	<h1><?php echo $puzzle['meta']['title']; ?></h1>
	<p><?php if(isset($puzzle['meta']['author']) && !empty($puzzle['meta']['author'])) { echo $puzzle['meta']['author']; } if (isset($puzzle['meta']['author']) && !empty($puzzle['meta']['copyright'])) { echo ', '.$puzzle['meta']['copyright']; } ?></p>

	<div id="puzzle">
		<?php echo $html; ?>
	</div>
	
	<div id="clues">
	
		<div class="across">
		
		    <h2>Across:</h2>
		    
		    <ol>
		        <?php 
		         
		            $i = 0; foreach($puzzle['across'] as $clue) {
		             
		             	if(isset($clue['cluetext']) && !empty($clue['cluetext'])) {
		             
		                    echo "\n\t\t\t<li value='".$clue['cluenumber']."'>".$clue['cluetext']."</li>";
		                    
		                }
		                     
		            $i++;   }
		             
		        ?>
		     
		    </ol>
	        
		</div>
		
		<div class="down">
	
		    <h2>Down:</h2>
		    
		    <ol>
		        <?php 
		         
		            $i = 0; foreach($puzzle['down'] as $clue) {
		             
		             	if(isset($clue['cluetext']) && !empty($clue['cluetext'])) {
		             
		                    echo "\n\t\t\t<li value='".$clue['cluenumber']."'>".$clue['cluetext']."</li>";
		                    
		                }
		                     
		            $i++;   }
		             
		        ?>
		     
		    </ol>
	    
	    </div>
	        
	</div>

