<?php

class Puzzles extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('puzzles_model');
		$this->load->helper('json'); 
		$this->load->library('typography');
	}
	
	public function index()	{
	
		if (!$this->tank_auth->is_logged_in()) {
			redirect('/auth/login/');
		}
	
		$data['puzzles'] = $this->puzzles_model->get_puzzle();
		$data['title'] = 'Puzzles Collection';
		
		$i = 0; //Convert Regular quotes to Smart Quotes
		foreach ($data['puzzles'] as $puzzle) {
			$data['puzzles'][$i]['meta']['title'] = $this->typography->format_characters($puzzle['meta']['title']);
			$i++;
		}
		
		$data['puzzle'] = '';
		
		$data['js'] = array('jquery', 'bootstrap');	
		$data['css'] = array('bootstrap', 'common', 'bootstrap_responsive');
									
		$this->load->view('templates/header', $data);
		$this->load->view('puzzles/index', $data);
		$this->load->view('templates/footer', $data);
	}
	
	public function view($slug) {
	
		$this->load->helper('form');
	
		$data['puzzle'] = $this->puzzles_model->get_puzzle($slug);
				
		$data['puzzle']['slug'] = $slug;

		if (empty($data['puzzle']))
		{
			show_404();
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
										
		$data['js'] = array('jquery', 'bootstrap', 'crosswordview');	
		$data['css'] = array('bootstrap', 'common', 'bootstrap_responsive');
		
		$this->load->view('templates/header', $data);
		$this->load->view('puzzles/view', $data);
		$this->load->view('templates/footer', $data);
	}
	
	public function save($slug) {
	
		$this->load->model('user_puzzles_model');
		
		$data['puzzle'] = $this->puzzles_model->get_puzzle($slug);
		
		$this->user_puzzles_model->save_puzzle($data['puzzle']['id']);
		echo 'Puzzle saved!';
				
	}

}