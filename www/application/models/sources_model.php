<?php
class Sources_model extends CI_Model {
	
	public function __construct()	{
		$this->load->database();
		parent::__construct();
		$this->load->model('puzzles_model');
		$this->load->helper('json');
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
		
		if (!file_exists('puzzles/'.$sourceConfig['name'].'/'.date('ymd').'-'.$filename.'.puz')) {
		
			$fp = fopen('puzzles/'.$sourceConfig['name'].'/'.date('ymd').'-'.$filename.'.puz', 'w');
	
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
	
	public function parsePuzzle($puzzledata) {
	
		$puzzle = array();
		
		$dimdata = unpack("c2dim", substr($puzzledata, 0x2C, 2));
		
		$width = $dimdata['dim1'];
		$height = $dimdata['dim2'];
		
		$answerstring = substr($puzzledata, 0x34, $width*$height); //Find the answers string
		$bwstring = substr($puzzledata, 0x34+$width*$height, $width*$height); //Find the crossword structure
		
		$puzzle['meta']['width'] = $width;
		$puzzle['meta']['height'] = $height;
		$puzzle['bwstring'] = $bwstring;
		$puzzle['answerstring'] = $answerstring;
		
		$puzzleGrids = $this->puzzles_model->puzzle_grids($puzzle);
		
		$bwgrid = $puzzleGrids['bwgrid'];
		$numgrid = $puzzleGrids['numgrid'];

		$cluestring = substr($puzzledata, 0x34+($width*$height+$width*$height));
		
		$newclues = preg_split('/\0/', $cluestring);

		$header = array("title" => array_shift($newclues), "author" => array_shift($newclues), "copyright" => array_shift($newclues));

		// Now we need to do some clue numbering!
 
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
					
		$meta = array(
			'title' => $header['title'],
			'author' => $header['author'],
			'copyright' => htmlentities($header['copyright']),
			'width' => $width,
			'height' => $height
		);
				
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