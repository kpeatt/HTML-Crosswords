<?php

class Src_Jonesin_model extends Sources_model {

	var $sourceName = "jonesin";
	var $config = "";

	function getConfig() {
		$this->config = parent::getConfig($this->sourceName);
		
		return $this->config;
	}
	
	function downloadPuzzle() {
	
	}
	
}
?>