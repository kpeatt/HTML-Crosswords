	<div class="navbar navbar-fixed-top">
	
		<div class="navbar-inner">
		    <div class="container">

				<a class="brand" href="#">
					WordMist
				</a>
				
		    </div>
	  </div>
	
	</div>
	
	<div class="row-fluid">
	
		<div class="span12">
		
			<h1><?php echo $puzzle['meta']['title']; ?></h1>
			<p><?php if(isset($puzzle['meta']['author']) && !empty($puzzle['meta']['author'])) { echo $puzzle['meta']['author']; } if (isset($puzzle['meta']['author']) && !empty($puzzle['meta']['copyright'])) { echo ', '.$puzzle['meta']['copyright']; } ?></p>
			
		</div>

	</div>
	
	<div class="row-fluid">
		
		<div id="puzzle" class="span6">
			<?php echo $html; ?>
		</div>
	
		<div id="clues" class="span6">
		
			<div class="row-fluid">
		
				<div class="across span6">
				
				    <h2>Across:</h2>
				    
				    <ol>
				        <?php 
				         
				            $i = 0; foreach($puzzle['across'] as $clue) {
				             
				             	if(isset($clue['cluetext']) || isset($clue['cluenumber'])) {
				             
				                    echo "\n\t\t\t<li value='".$clue['cluenumber']."'>".$clue['cluetext']."</li>";
				                    
				                }
				                     
				            $i++;   }
				             
				        ?>
				     
				    </ol>
			        
				</div>
				
				<div class="down span6">
			
				    <h2>Down:</h2>
				    
				    <ol>
				        <?php 
				         
				            $i = 0; foreach($puzzle['down'] as $clue) {
				             
				             	if(isset($clue['cluetext']) || isset($clue['cluenumber'])) {
				             
				                    echo "\n\t\t\t<li value='".$clue['cluenumber']."'>".$clue['cluetext']."</li>";
				                    
				                }
				                     
				            $i++;   }
				             
				        ?>
				     
				    </ol>
			    
			    </div>
		    
		    </div>
		        
		</div>
	</div>

