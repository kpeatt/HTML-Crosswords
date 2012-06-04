<?php

class Puzzles extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('puzzles_model');
		$this->load->helper('json'); 
		$this->load->library('typography');
		$this->load->library('gravatar');
	}
	
	public function index()	{
	
		// Check if user is logged in and get userdata		
		if (!$this->tank_auth->is_logged_in()) {
			redirect('/auth/login/');
		} else {
			$data['user']['id'] = $this->tank_auth->get_user_id();
			$data['user']['username'] = $this->tank_auth->get_username();
			$data['user']['email'] = $this->tank_auth->get_email();
			$data['user']['avatar'] = $this->gravatar->get_gravatar($data['user']['email'], 'pg', '20', 'mm');
		}
	
		$data['puzzles'] = $this->puzzles_model->get_puzzle();
		$data['title'] = 'Puzzles Collection';
		
		$i = 0; //Convert Regular quotes to Smart Quotes
		foreach ($data['puzzles'] as $puzzle) {
			$data['puzzles'][$i]['meta']['title'] = $this->typography->format_characters($puzzle['meta']['title']);
			$i++;
		}
		
		$data['puzzle'] = '';
		
		$data['template']['name'] = 'puzzle-list';
		
		$data['js'] = array('jquery', 'bootstrap');	
		$data['css'] = array('bootstrap', 'common', 'bootstrap_responsive');
									
		$this->load->view('templates/header', $data);
		$this->load->view('puzzles/index', $data);
		$this->load->view('templates/footer', $data);
	}
	
	public function view($slug) {
	
		$this->load->helper('form');
		$this->load->model('user_puzzles_model');
	
		// Check if user is logged in and get userdata		
		if (!$this->tank_auth->is_logged_in()) {
			redirect('/auth/login/');
		} else {
			$data['user']['id'] = $this->tank_auth->get_user_id();
			$data['user']['username'] = $this->tank_auth->get_username();
			$data['user']['email'] = $this->tank_auth->get_email();
			$data['user']['avatar'] = $this->gravatar->get_gravatar($data['user']['email'], 'pg', '20', 'mm');
		}
				
		$data['puzzle'] = $this->puzzles_model->get_puzzle($slug);
				
		if (empty($data['puzzle']))
		{
			show_404();
		}
				
		// Save functionality, post to self
		if ($this->input->post()) {
			$saved = $this->user_puzzles_model->save_puzzle($data['puzzle']);
		}
		
		// Give user messages if successful/unsuccessful
		if (isset($saved) && $saved) {
			$this->session->set_flashdata('success', 'Puzzle has been saved');	
		} else if (isset($saved)) {
			$this->session->set_flashdata('error', 'Puzzle could not be saved. Please try again.');	
		}
		
		$data['html'] = $this->puzzles_model->render_puzzle($data['puzzle']);
		
		$data['puzzle']['across'] = json_decode($data['puzzle']['across'], true);
		$data['puzzle']['down'] = json_decode($data['puzzle']['down'], true);
				
		$i = 0; //Convert Regular quotes to Smart Quotes
		foreach ($data['puzzle']['across'] as $across) {
			$data['puzzle']['across'][$i]['cluetext'] = $this->typography->format_characters($across['cluetext']);
			$i++;
		}
		$i = 0;
		foreach ($data['puzzle']['down'] as $down) {
			$data['puzzle']['down'][$i]['cluetext'] = $this->typography->format_characters($down['cluetext']);
			$i++;
		}
		
		$data['puzzle']['meta']['title'] = $this->typography->format_characters($data['puzzle']['meta']['title']);
		$data['puzzle']['meta']['copyright'] = $this->typography->format_characters($data['puzzle']['meta']['copyright']);
		$data['puzzle']['meta']['author'] = $this->typography->format_characters($data['puzzle']['meta']['author']);
		
		$data['title'] = $this->typography->format_characters($data['puzzle']['meta']['title']);
		
		$data['template']['name'] = 'puzzle-view';
								
		$data['js'] = array('jquery', 'bootstrap', 'crosswordview');	
		$data['css'] = array('bootstrap', 'common', 'bootstrap_responsive');
		
		$this->load->view('templates/header', $data);
		$this->load->view('puzzles/view', $data);
		$this->load->view('templates/footer', $data);
	}
	
	public function save($slug) {
	
		$this->load->model('user_puzzles_model');
		
		$data['puzzle'] = $this->puzzles_model->get_puzzle($slug);
		
		$this->user_puzzles_model->save_puzzle($data['puzzle']);
						
	}

}