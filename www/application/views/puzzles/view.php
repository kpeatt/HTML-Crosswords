	<div class="navbar subnav subnav-fixed">
		<div class="container-fluid">
			<h3 class="navbar-text pull-left"><?php echo $puzzle['meta']['title']; ?></h3>
			<a href="" class="btn btn-success pull-right" id="ajax-save">Save Puzzle</a>
		</div>
	</div><!-- /subnav -->
	
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

