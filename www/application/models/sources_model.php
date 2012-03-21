<?php
class Sources_model extends CI_Model {
	
	public function __construct()	{
		$this->load->database();
		parent::__construct();
		$this->load->model('puzzles_model');
		$this->load->helper('json');
		$this->load->library('xml');
	}
	
	public function getSources() {
		$query = $this->db->get('sources');
		$result = $query->result_array();
		return $result;
	}
	
	public function getConfig($sourceName) {
		$query = $this->db->get_where('sources', array('name' => $sourceName));
		$result = $query->result_array();
		
		return $result;
	}
	
	public function downloadPuzzle($sourceConfig) {
	
		$filename = $this->parseFilename($sourceConfig['filename']);
		
		if (!is_dir('puzzles/'.$sourceConfig['name'])) {
			mkdir('puzzles/'.$sourceConfig['name'], 0700);
		}
		
		if (isset($sourceConfig['header']) && !empty($sourceConfig['header'])) {
			$header = json_decode($sourceConfig['header']);
		}
		
		if (!file_exists('puzzles/'.$sourceConfig['name'].'/'.date('ymd').'-'.$filename.'.'.$sourceConfig['filetype'])) {
		
			$fp = fopen('puzzles/'.$sourceConfig['name'].'/'.date('ymd').'-'.$filename.'.'.$sourceConfig['filetype'], 'w');
	
			$curl_handle=curl_init();
			curl_setopt($curl_handle,CURLOPT_URL,$sourceConfig['url'].$filename.'.'.$sourceConfig['filetype']);			
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			
			
			if (isset($header['referer']) && !empty($header['referer'])) {
				curl_setopt($curl_handle, CURLOPT_REFERER, $header['referer']);
			}
			if (isset($header['useragent']) && !empty($header['useragent'])) {
				curl_setopt($curl_handle, CURLOPT_USERAGENT, $header['useragent']);
			}
						
			$content = curl_exec($curl_handle);
			
			curl_close($curl_handle);
			
			if ($sourceConfig['filetype'] == 'jpz') {
				$content = $this->convertJPZ($content);
			}
			
			fwrite($fp, $content);

			fclose($fp);
			
			echo $filename.' downloaded! <br>';
			
			if ($sourceConfig['filetype'] == 'jpz') {
				$content = $this->getJPZData($content);
			} else {
				$content = $this->getPuzData($content);
			}
			
			$data = $this->parsePuzzle($content);
			
			$data = array('source_id' => $sourceConfig['id']) + array('slug' => $sourceConfig['name'].'-'.date('ymd')) + $data;
			
			return $this->db->insert('puzzles', $data);
			
		}
	}
	
	public function parseFilename($filename) {
		
		preg_match('/\[(.*?)\]/', $filename, $matches);
		
		if ($matches) {
		
			$dateValue = date($matches[1]);
			$filename = preg_replace('/\[[^\]]*\]/', $dateValue, $filename);
							
		}
		
		return $filename;
		
	}
	
	public function parsePuzzle($puzzle) {
				
		$width = $puzzle['meta']['width'];
		$height = $puzzle['meta']['height'];
		$bwstring = $puzzle['bwstring'];
		$answerstring = $puzzle['answerstring'];
		
		if (isset($puzzle['cluestring'])) {
			$cluestring = $puzzle['cluestring'];
		}
		
		$puzzleGrids = $this->puzzles_model->puzzle_grids($puzzle);
		
		$bwgrid = $puzzleGrids['bwgrid'];
		$numgrid = $puzzleGrids['numgrid'];

		if ($puzzle['filetype'] == 'jpz') {
			$across = $puzzle['across'];
			$down = $puzzle['down']; 
		} 
		
		else if ($puzzle['filetype'] == 'puz') {
		
			$newclues = $puzzle['newclues'];
 
			$across = array();
			$down = array();
			$cluenumber = 0;
			
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
	 
			        if ($bwgrid[$i][$j-1] == -1){ 
			            array_push($across, array('cluenumber' => $cluenumber, 'cluetext' => array_shift($newclues)));
			        }
			         
			        if ($bwgrid[$i-1][$j] == -1){ 
			            array_push($down, array('cluenumber' => $cluenumber, 'cluetext' => array_shift($newclues))); 
			        }
			         
			    }
			 
			}
		
		}
					
		$meta = array(
			'title' => $puzzle['header']['title'],
			'author' => $puzzle['header']['author'],
			'copyright' => htmlentities($puzzle['header']['copyright']),
			'width' => $width,
			'height' => $height
		);
		
		$meta['copyright'] = str_ireplace("&Acirc;", "", $meta['copyright']);
				
		$data = array(
			'bwstring' => $bwstring,
			'answerstring' => $answerstring,
			'across' => json_encode($across),
			'down' => json_encode($down),
			'meta' => json_encode($meta),
			'date' => date('Y-m-d')
		);
				
		return $data;
		
	}
	
	public function getPuzData($puzzledata) {
		$puzzle = array();
	
		$dimdata = unpack("c2dim", substr($puzzledata, 0x2C, 2));
		
		$puzzle['meta']['width'] = $dimdata['dim1'];
		$puzzle['meta']['height'] = $dimdata['dim2'];
		
		$width = $puzzle['meta']['width'];
		$height = $puzzle['meta']['height'];
		
		$puzzle['answerstring'] = substr($puzzledata, 0x34, $width*$height); //Find the answers string
		$puzzle['bwstring'] = substr($puzzledata, 0x34+$width*$height, $width*$height); //Find the crossword structure
		$puzzle['cluestring'] = substr($puzzledata, 0x34+($width*$height+$width*$height)); //Find the clue string
		
		$isUTF = mb_detect_encoding($puzzle['cluestring'], 'UTF-8', true); // Check if cluestring is UTF-8
		
		if (!$isUTF) {
		
			$puzzle['cluestring'] = utf8_encode($puzzle['cluestring']);
		
		}
				
		$puzzle['newclues'] = preg_split('/\0/', $puzzle['cluestring']);

		$puzzle['header'] = array("title" => array_shift($puzzle['newclues']), "author" => array_shift($puzzle['newclues']), "copyright" => array_shift($puzzle['newclues']));
		
		$puzzle['filetype'] = 'puz';
		
		return $puzzle;
	}
	
	public function convertJPZ($content) {
	
		if (!is_dir('puzzles/tmp')) {
			mkdir('puzzles/tmp', 0700);
		}
	
		$fp = fopen('puzzles/tmp/temp.jpz', 'w');
		fwrite($fp, $content);
		fclose($fp);
		$this->unzip('puzzles/tmp/temp.jpz');
		$content = file_get_contents('puzzles/tmp/temp.jpz');
		unlink('puzzles/tmp/temp.jpz');
		rmdir('puzzles/tmp');
	
		$content = str_replace('&nbsp;', ' ', $content);
		$content = str_replace('%', '%25', $content);
		$content = str_replace('\\+', '%2B', $content);
		$content = str_replace('Ò', '"', $content);
		$content = str_replace('Ó', '"', $content);
		
		utf8_encode($content);
		
		return $content;
		
	}
	
	public function getJPZData($puzzledata) {
	
		$jpzObject = $this->xmlToObject($puzzledata);
	
		$puzzledata = $jpzObject->{"rectangular-puzzle"};
			
		$puzzle = array();
		
		$puzzle['meta']['width'] = (string) $puzzledata->crossword->grid->attributes()->width;
		$puzzle['meta']['height'] = (string) $puzzledata->crossword->grid->attributes()->height;
		
		$puzzle['header']['title'] = (string) $puzzledata->metadata->title;
		$puzzle['header']['author'] = (string) $puzzledata->metadata->creator;
		$puzzle['header']['copyright'] = (string) $puzzledata->metadata->copyright;
		
		$puzzle['header']['copyright'] = substr($puzzle['header']['copyright'], 1);
		
		//Time to make some strings!
		
		$puzzledataJson = json_encode($puzzledata);
		$puzzledataArray = json_decode($puzzledataJson, TRUE);
		
		$bwstring = "";
		$answerstring = "";
				
		for ($i = 1; $i <= $puzzle['meta']['height']; $i++) { // y value
		
			for ($j = 1; $j <= $puzzle['meta']['width']; $j++) { // x value
			
				foreach ($puzzledataArray['crossword']['grid']['cell'] as $cell) {
				
					if ($cell['@attributes']['x'] == $j && $cell['@attributes']['y'] == $i) {
						
						if (isset($cell['@attributes']['solution'])) {
							
							$bwstring .= '-';
							$answerstring .= $cell['@attributes']['solution'];							
						} elseif (isset($cell['@attributes']['type']) && $cell['@attributes']['type'] == 'block') {
						
							$bwstring .= '.';
							$answerstring .= '.';	
						
						}
						
					}
				
				}	
			
			}
		
		}
		
		$across = array();
		$down = array();
		
		$i = 0;
		
		foreach ($puzzledataArray['crossword']['clues'][0]['clue'] as $acrossclue) {
		
			$cluenumber = (string) $puzzledata->crossword->clues[0]->clue[$i]->attributes()->number;
						
			array_push($across, array('cluenumber' => $cluenumber, 'cluetext' => $acrossclue));
			
			$i++;
		
		}
		
		$i = 0;
		
		foreach ($puzzledataArray['crossword']['clues'][1]['clue'] as $acrossclue) {
		
			$cluenumber = (string) $puzzledata->crossword->clues[1]->clue[$i]->attributes()->number;
						
			array_push($down, array('cluenumber' => $cluenumber, 'cluetext' => $acrossclue));
			
			$i++;
		
		}
		
		$puzzle['across'] = $across;
		$puzzle['down'] = $down;
				
		$puzzle['bwstring'] = $bwstring;
		$puzzle['answerstring'] = $answerstring;
		
		$puzzle['filetype'] = 'jpz';
		
		return $puzzle;
	}
	
	public function xmlToObject($xml) {
		$xmlObject = simplexml_load_string($xml);
		
		return $xmlObject;
	}
	
	public function unzip($file){ 
	    $zip = zip_open($file); 
	    if(is_resource($zip)){ 
	        $tree = ""; 
	        while(($zip_entry = zip_read($zip)) !== false){ 
	            echo "Unpacking ".zip_entry_name($zip_entry)."\n"; 
	            if(strpos(zip_entry_name($zip_entry), DIRECTORY_SEPARATOR) !== false){ 
	                $last = strrpos(zip_entry_name($zip_entry), DIRECTORY_SEPARATOR); 
	                $dir = substr(zip_entry_name($zip_entry), 0, $last); 
	                $file = substr(zip_entry_name($zip_entry), strrpos(zip_entry_name($zip_entry), DIRECTORY_SEPARATOR)+1); 
	                if(!is_dir($dir)){ 
	                    @mkdir($dir, 0755, true) or die("Unable to create $dir\n"); 
	                } 
	                if(strlen(trim($file)) > 0){ 
	                    $return = @file_put_contents($dir."/".$file, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry))); 
	                    if($return === false){ 
	                        die("Unable to write file $dir/$file\n"); 
	                    } 
	                } 
	            }else{ 
	                file_put_contents($file, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
	            } 
	        } 
	    }else{ 
	        echo "Unable to open zip file\n"; 
	    } 
	} 
	
}