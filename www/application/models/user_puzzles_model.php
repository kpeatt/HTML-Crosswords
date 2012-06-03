<?php

class user_puzzles_model extends CI_Model 
{

	public function __construct()	{
		$this->load->database();
	}
	
	// Convert Inputs to Answer String
	function answers_to_key($postdata, $bwstring) {
		$answerstring = '';
		
		$bwstring = str_split($bwstring);
		
		$i = 1;
		foreach($bwstring as $key) {
			
			if ($key == '-' && $postdata["cell_$i"] != '') {
				$answerstring .= $postdata["cell_$i"];
			} else if ($key == '-' && $postdata["cell_$i"] == '') {
				$answerstring .= '-';
			} else {
				$answerstring .= '.';
			}
			
			$i++;
						
		}
		
		return $answerstring;
	}
	
	// Compare Answer String to Answer Key
	function percent_complete($answerstring, $answerkey) {
		
		$answerkey = str_replace('.', '', $answerkey);
		$answerstring = str_replace('.', '', $answerstring);
		
		$answerarray = str_split($answerstring);
		$keyarray = str_split($answerkey);
				
		$i = 0;
		$size = sizeof($keyarray);
		$totalright = 0;
		foreach ($answerarray as $answer) {
			
			if ($keyarray[$i] == $answer) {
				$totalright++;
			}
			
			$i++;
		}
		
		$complete = ($totalright/$size)*100;
		
		$complete = round($complete, 2);
				
		return $complete;
		
	}
	
	// Save puzzle to User ID
	function save_puzzle($puzzledata) {
	
		$userid = $this->tank_auth->get_user_id();
		$puzzleid = $puzzledata['id'];
		$puzzlekey = $puzzledata['bwstring'];
		$answerkey = $puzzledata['answerstring'];
		$answers = $this->input->post();
		$answerstring = $this->answers_to_key($answers, $puzzlekey);
		
		 $data = array(
			'user_id' => $userid,
			'puzzle_id' => $puzzleid,
			'answers' => $answerstring,
			'progress' => $this->percent_complete($answerstring, $answerkey)
		);
		
		$saved = false;
				
		$where = array('user_id'=>$userid, 'puzzle_id'=>$puzzleid);
		$this->db->from('user_puzzles')->where($where);
	    if ($this->db->count_all_results() == 0) { 
			// A record does not exist, insert one.
	      
			$data['created'] = date('Y-m-d H:i:s');
			
			$this->db->insert('user_puzzles', $data);
			
			$saved = true;
				      	      	      
	    } else {
			// A record does exist, update it.
			
			$this->db->where($where);
			$this->db->update('user_puzzles', $data);
			
			$saved = true;
	    }
	    
	    return $saved;
	    	    		
	}
	
}
?>