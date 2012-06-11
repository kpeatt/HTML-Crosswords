	<div class="navbar subnav subnav-fixed">
		<div class="container-fluid">
			<h3 class="navbar-text pull-left"><?php echo $puzzle['meta']['title']; ?></h3>
			<div class="btn-toolbar pull-right">
    			<div class="btn-group">
    			  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                   <i class="icon-check"></i> Check
                   <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li>
                        <a href="">Check Square</a>
                    </li>
                    <li>
                        <a href="">Check Word</a>
                    </li>
                    <li>
                        <a href="">Check Filled Squares</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="">Check Puzzle</a>
                    </li>
                  </ul>
                </div><!--btn-group-->
                <div class="btn-group">
    			  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                   <i class="icon-eye-open"></i> Reveal
                    <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li>
                        <a href="">Reveal Square</a>
                    </li>
                    <li>
                        <a href="">Reveal Word</a>
                    </li>
                    <li>
                        <a href="">Reveal Wrong Squares</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="">Reveal Puzzle</a>
                    </li>
                  </ul>
                </div><!--btn-group-->
                <div class="btn-group">
                    <a class="btn btn-success" id="ajax-save">Save Puzzle</a> 
                </div>
			</div><!--btn-toolbar-->
		</div><!--btn-container-fluid-->
	</div><!-- /subnav -->
	
	<div class="row-fluid">
		
		<div id="puzzle" class="span9">
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
	
		<div id="clues" class="span3">
		
			<div class="row-fluid">
		
				<div class="across">
				
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
				
				<div class="down">
			
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

