<?php

class user_puzzles_model extends CI_Model 
{

	public function __construct()	{
		$this->load->database();
	}
	
	// Save puzzle to User ID
	function save_puzzle($puzzleid) {
	
		$userid = $this->tank_auth->get_user_id();
		
		echo $userid;
		
		if (!$userid) {
			
			return false;
			
		}
		
		// Convert inputs to Answer string
		function answers_to_string($postdata) {
			$answerstring = '';
			foreach($postdata as $answer) {
				
				if ($answer != '') {
					$answerstring .= $answer;
				} else {
					$answerstring .= '-';
				}
			}
			return $answerstring;
		}
		
		 $data = array(
			'user_id' => $userid,
			'puzzle_id' => $puzzleid,
			'answers' => answers_to_string($this->input->post())
		);
		
		$this->db->from('user_puzzles')->where(array('user_id'=>$userid, 'puzzle_id'=>$puzzleid));
	    if ($this->db->count_all_results() == 0) { 
	      // A record does not exist, insert one.
	      
		  $data['created'] = date('Y-m-d H:i:s');
		
	      $this->db->insert('user_puzzles', $data);
	      
	      echo 'inserted';
	      
	    } else {
	      // A record does exist, update it.
	      
	      $this->db->update('user_puzzles', $data);
	      
	      echo 'updated';
	    }
		
	}
	
}
?>