	<div class="row-fluid">
	
		<div class="span12">
		
			<h1><?php echo $puzzle['meta']['title']; ?></h1>
			<p><?php if(isset($puzzle['meta']['author']) && !empty($puzzle['meta']['author'])) { echo $puzzle['meta']['author']; } if (isset($puzzle['meta']['author']) && !empty($puzzle['meta']['copyright'])) { echo ', '.$puzzle['meta']['copyright']; } ?></p>
			
		</div>

	</div>
	
	<div class="row-fluid">
		
		<div id="puzzle" class="span6">
			<div class="well current-clue">
				<h2>
					<span class="direction"><i class="icon-arrow-right"></i> </span>
					<span class="number"><?php echo $puzzle['across'][0]['cluenumber']; ?></span>. <span class="text"><?php echo $puzzle['across'][0]['cluetext']; ?></span>
				</h2>
			</div>
		
			<?php echo form_open('/puzzles/' . $puzzle['slug'], array('id' => 'save-puzzle')) ?>
				<?php echo $html; ?>
				
				<button type="submit" class="btn btn-primary">Save Puzzle</button>
			</form>
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

