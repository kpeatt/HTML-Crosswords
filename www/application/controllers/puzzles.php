<?php

class Puzzles extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('puzzles_model');
		$this->load->helper('json'); 
	}
	
	public function index()	{
		$data['puzzles'] = $this->puzzles_model->get_puzzle();
		$data['title'] = 'Puzzles Collection';
					
		$this->load->view('templates/header', $data);
		$this->load->view('puzzles/index', $data);
		$this->load->view('templates/footer');
	}
	
	public function view($slug) {
		$data['puzzle'] = $this->puzzles_model->get_puzzle($slug);

		if (empty($data['puzzle']))
		{
			show_404();
		}
		
		$data['title'] = $data['puzzle']['meta']['title'];
		$data['html'] = $this->puzzles_model->render_puzzle($data['puzzle']);	
										
		$data['head'] = array('specificjs');								
		
		$this->load->view('templates/header', $data);
		$this->load->view('puzzles/view', $data);
		$this->load->view('templates/footer');
		
	}
	
	public function download() {
		$this->load->model("src_jonesin_model");
		$results = $this->src_jonesin_model->getConfig();
		print_r($results);
	}

}