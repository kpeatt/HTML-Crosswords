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
				
		$width = $puzzle['meta']['width'];
		$height = $puzzle['meta']['height'];
		
		$answerstring = $puzzle['answerstring'];
 		$answergrid = array();

 
		for ($i = 0; $i <= $height-1; $i++) { // Make a 2d array of answers
		    $answergrid[$i] = str_split(substr($answerstring, $i*$width, $width));
		}
		
		$bwstring = $puzzle['bwstring'];
		
		$bwgrid = array();
		$numgrid = array();
		
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
		         
		    }
		 
		}
		
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
                    $html .= "\n\t\t<td class='space'><div class='wrapper'><div class='number'>".$numgrid[$i][$j]."</div><input type='text' class='answer' maxlength='1' rel='[".$i."][".$j."]' id='cell_".$k."'></div></td>";
                    $k++;
                } else { // It's a blank square!
                    $html .= "\n\t\t<td class='space'><div class='wrapper'><input type='text' class='answer' maxlength='1' rel='[".$i."][".$j."]' id='cell_".$k."'></div></td>";
                    $k++;
                }
            }
             
            $html .= "\n\t</tr>";
        }
         
        $html .= "\n</table>";
                
        return $html;
		
	}

}