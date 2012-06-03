<?php
class Puzzles_model extends CI_Model {

	public function __construct()	{
		$this->load->database();
	}
	
	public function get_puzzle($slug = FALSE) {
	
		if ($slug === FALSE) {
			$query = $this->db->get('puzzles');
			$puzzles = $query->result_array();
			
			$i = 0;
			foreach ($puzzles as $puzzle) {
				$puzzles[$i]['meta'] = json_decode($puzzle['meta'], true);
				$i++;
			}
			
			return $puzzles;
		}
		
		$query = $this->db->get_where('puzzles', array('slug' => $slug));
		$puzzle = $query->result_array();
		
		if(empty($puzzle)) {
			return $puzzle;
		}
		
		$puzzle = $puzzle[0];
		$puzzle['meta'] = json_decode($puzzle['meta'], true);
		
		return $puzzle;
	
	}
	
	public function render_puzzle($puzzle) {
		
		$puzzleGrids = $this->puzzle_grids($puzzle);
		
		$width = $puzzle['meta']['width'];
		$height = $puzzle['meta']['height'];
		
		$bwgrid = $puzzleGrids['bwgrid'];
		$numgrid = $puzzleGrids['numgrid'];
		$cluenumgrid = $puzzleGrids['cluenumgrid'];
		$usergrid = $puzzleGrids['usergrid'];
				
		///Time to render the HTML!
		
		$html = "<table>";
         
        $k = 1; //For the cell count
         
        for ($i = 1; $i <= $height; $i++) {
 
            $html .= "\n\t<tr>";
             
            for ($j = 1; $j <= $width; $j++) {
             
                if ($numgrid[$i][$j] == -1){ //It's a black square!
                    $html .= "\n\t\t<td class='black'></td>";
                    $k++;
                } else if ($numgrid[$i][$j] > 0) { // It's a clue!
                    $html .= "\n\t\t<td class='space'>\n\t\t\t<div class='wrapper'>\n\t\t\t\t";
                    $html .= "<div class='number'>".$numgrid[$i][$j]."</div>\n\t\t\t\t";
                    $html .= "<input type='text' class='answer' maxlength='1' cellx='".$j."' celly='".$i."' id='cell_".$k."' name='cell_".$k."' ";
                    $html .= "across='".$cluenumgrid['across'][$i][$j]."' down='".$cluenumgrid['down'][$i][$j]."' ";
                    if (isset($usergrid[$i][$j])) {
                    	$html .= "value='".$usergrid[$i][$j]."'";
                    }
                    $html .= ">\n\t\t\t</div>\n\t\t</td>";
                    $k++;
                } else { // It's a blank square!
                    $html .= "\n\t\t<td class='space'>\n\t\t\t<div class='wrapper'>\n\t\t\t\t";
                    $html .= "<input type='text' class='answer' maxlength='1' cellx='".$j."' celly='".$i."' id='cell_".$k."' name='cell_".$k."' ";
                    $html .= "across='".$cluenumgrid['across'][$i][$j]."' down='".$cluenumgrid['down'][$i][$j]."' ";
                    if (isset($usergrid[$i][$j])) {
                    	$html .= "value='".$usergrid[$i][$j]."'";
                    }
                    $html .= ">\n\t\t\t</div>\n\t\t</td>";
                    $k++;
                }
            }
             
            $html .= "\n\t</tr>";
        }
         
        $html .= "\n</table>";
                
        return $html;
		
	}
	
	public function puzzle_grids($puzzle) {
			
		$width = $puzzle['meta']['width'];
		$height = $puzzle['meta']['height'];
		$answerstring = $puzzle['answerstring'];
		$bwstring = $puzzle['bwstring'];
		
		// If a user has saved data, let's use that to fill out the puzzle
		
		$usergrid = array();
		$userid = $this->tank_auth->get_user_id();
		if (isset($userid)) {
			$where = array('user_id'=>$userid, 'puzzle_id'=>$puzzle['id']);
			$query = $this->db->get_where('user_puzzles', $where);
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					$userstring = $row->answers;
				}
			}
								
			if (isset($userstring) && $userstring != ''){
				for ($i = 0; $i <= $height-1; $i++) { // Make a 2d array of answers
				    $usergrid[$i] = str_split(substr($userstring, $i*$width, $width));
				}
			
				array_unshift($usergrid, array()); // Add row of -1s to top and left of usergrid
				for ($i = 0; $i < $width; $i++) {
				    array_push($usergrid[0], -1);
				}
				for ($i = 0; $i <= $width; $i++) {
				    array_unshift($usergrid[$i], -1);
				}
								
				// Remove '-' from usergrid for rendering
				for ($i = 1; $i <= $height; $i++) {
				    for ($j = 1; $j <= $width; $j++) { 
				        if (isset($usergrid[$i][$j]) && $usergrid[$i][$j] == '-') { 
				        	$usergrid[$i][$j] = '';
				        }
				    }
				}
			}
		
		}

		$answergrid = array();

		for ($i = 0; $i <= $height-1; $i++) { // Make a 2d array of answers
		    $answergrid[$i] = str_split(substr($answerstring, $i*$width, $width));
		}
		
		$bwgrid = array();
		$numgrid = array();
		$cluenumgrid = array();
		
		for ($i = 0; $i < $height; $i++) { // Make a 2d array of structure
		    $bwgrid[$i] = str_split(substr($bwstring, $i*$width, $width));
		}
		 
		for ($i = 0; $i < count($bwgrid); $i++) { // 2d Array of Clue Numbers
		    for ($j = 0; $j < count($bwgrid[$i]); $j++) {
		        $numgrid[$i][$j] = $bwgrid[$i][$j];
		    }
		}
		
		array_unshift($bwgrid, array()); // Add row of -1s to top and left of BWGrid
		for ($i = 0; $i < $width; $i++) {
		    array_push($bwgrid[0], -1);
		}
		for ($i = 0; $i <= $width; $i++) {
		    array_unshift($bwgrid[$i], -1);
		}
		 
		array_unshift($numgrid, array()); // Do the same thing to NumGrid
		for ($i = 0; $i < $width; $i++) {
		    array_push($numgrid[0], -1);
		}
		for ($i = 0; $i <= $width; $i++) {
		    array_unshift($numgrid[$i], -1);
		}
		 
		array_unshift($answergrid, array()); // Do the same thing to AnswerGrid
		for ($i = 0; $i < $width; $i++) {
		    array_push($answergrid[0], -1);
		}
		for ($i = 0; $i <= $width; $i++) {
		    array_unshift($answergrid[$i], -1);
		}
		
		$cluenumber = 0; //Now to do some clue numbering!
		$acrosscluenumber = 0;
		$downcluenumber = 0;
		$acrossspace = 0;
		$downspace = 0;
 
		for ($i = 1; $i <= $height; $i++) {
		     
		    for ($j = 1; $j <= $width; $j++) {
		         
		        if ($bwgrid[$i][$j] == '.'){ 
		          $bwgrid[$i][$j] = -1 ;
		          $numgrid[$i][$j] = -1;
		        }
		         
		        if ($bwgrid[$i][$j] == -1) {
		            continue;
		        }
		         
		        if ($bwgrid[$i][$j-1] == -1 or $bwgrid[$i-1][$j] == -1){ // If a square has -1 to it's left or top, it's a clue. So give it a number!
		          $cluenumber++;
		          $numgrid[$i][$j] = $cluenumber; 
		        }
		        
		        if ($bwgrid[$i][$j-1] == -1) { // This is an across clue
		        	
		        	$acrosscluenumber++;
		        	$acrosscluenumber = $acrossspace + $acrosscluenumber;
		        	
		        	$cluenumgrid['across'][$i][$j] = $acrosscluenumber;
		        	
		        	$acrossspace = 0;
		        }
		        
		        if ($bwgrid[$i-1][$j] == -1 && $bwgrid[$i][$j-1] != -1) { // This is a down clue and not an across clue
		        	$acrossspace++;
		        }
		        
		        if ($bwgrid[$i][$j] == '-' && $bwgrid[$i][$j-1] != -1) { //Check if it's a space and not a clue and increment
		        	$cluenumgrid['across'][$i][$j] = $acrosscluenumber;
	        	}
	        	
	        	if ($bwgrid[$i-1][$j] == -1) { // This is a down clue
		        	
		        	$downcluenumber++;
		        	$downcluenumber = $downspace + $downcluenumber;
		        	
		        	$cluenumgrid['down'][$i][$j] = $downcluenumber;
		        	
		        	$downspace = 0;
		        	
		        }
		        
		        if ($bwgrid[$i][$j-1] == -1 && $bwgrid[$i-1][$j] != -1) { // This is an across clue and not a down clue
		        	$downspace++;
		        }
		        
		        if ($bwgrid[$i][$j] == '-' && $bwgrid[$i-1][$j] != -1) { //Check if it's a space and not a clue and increment
		        	$cluenumgrid['down'][$i][$j] = $cluenumgrid['down'][$i-1][$j];
	        	}
		         
		    }
		 
		}
		
		$puzzleGrids = array('bwgrid' => $bwgrid, 'numgrid' => $numgrid, 'answergrid' => $answergrid, 'cluenumgrid' => $cluenumgrid, 'usergrid' => $usergrid);
				
		return $puzzleGrids;
		
	}

}