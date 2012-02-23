<?php

class Cron extends CI_Controller {
	
	var $sources = "";
	
	public function __construct() {
		parent::__construct();
		$this->load->model('sources_model');

	}
	
	public function index()	{

	}
	
	public function update() {
		$this->sources = $this->sources_model->getSources();
		
		foreach($this->sources as $source) {
			$source_config = $this->sources_model->getConfig($source['name']);
			$today = strtolower(date('l'));
			$this->load->database();
			
			if (($today == $source_config[0]['day'] || $source_config[0]['frequency'] == 'daily') && ($source_config[0]['lastchecked'] != date('Y-m-d')) && ($source_config[0]['active'])) {
				$this->sources_model->downloadPuzzle($source_config[0]);
			}

		}
	}

}