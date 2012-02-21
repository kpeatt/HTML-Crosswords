<?php
class Sources_model extends CI_Model {
	
	public function __construct()	{
		$this->load->database();
		parent::__construct();
	}
	
	function getSources() {
		$query = $this->db->get('sources');
		$result = $query->result_array();
		return $result;
	}
	
	function getConfig($sourceName) {
		$query = $this->db->get_where('sources', array('name' => $sourceName));
		$result = $query->result_array();
		
		return $result;
	}
	
	function downloadPuzzle($sourceConfig) {
	
		$filename = $this->parseFilename($sourceConfig['filename']);
		
		if (!is_dir('puzzles/'.$sourceConfig['name'])) {
			mkdir('puzzles/'.$sourceConfig['name'], 0700);
		}
		
		if (!file_exists('puzzles/'.$sourceConfig['name'].'/'.date('ymd').'-'.$filename.'.puz')) {
		
			$fp = fopen('puzzles/'.$sourceConfig['name'].'/'.date('ymd').'-'.$filename.'.puz', 'w');
	
			$curl_handle=curl_init();
			curl_setopt($curl_handle,CURLOPT_URL,$sourceConfig['url'].$filename.'.'.$sourceConfig['filetype']);			
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, $fp);			
			$content = curl_exec($curl_handle);
			curl_close($curl_handle);
			
			if ($sourceConfig['filetype'] == 'jpz') {
				$content = $this->convertJPZ($content);
			}
			
			fwrite($fp, $content);

			fclose($fp);
			
			echo $filename.' downloaded!';

		}
	}
	
	function parseFilename($filename) {
		
		preg_match('/\[(.*?)\]/', $filename, $matches);
		
		if ($matches) {
		
			$dateValue = date($matches[1]);
			$filename = preg_replace('/\[[^\]]*\]/', $dateValue, $filename);
							
		}
		
		return $filename;
		
	}
	
	function convertJPZ($content) {
	
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
	
	function parsePuzzle($filename) {
		
		
		
	}
	
	function unzip($file){ 
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

/**
 * Jonesin' Crosswords Downloader
 * URL: http://herbach.dnsalias.com/Jonesin/jzYYMMDD.puz
 * Date = Thursdays
 
public class JonesinDownloader extends AbstractDownloader {
    private static final String NAME = "Jonesin' Crosswords";
    NumberFormat nf = NumberFormat.getInstance();

    public JonesinDownloader() {
        super("http://herbach.dnsalias.com/Jonesin/", DOWNLOAD_DIR, NAME);
        nf.setMinimumIntegerDigits(2);
        nf.setMaximumFractionDigits(0);
    }

    public int[] getDownloadDates() {
        return DATE_THURSDAY;
    }

    public String getName() {
        return NAME;
    }

    public File download(Date date) {
        return super.download(date, this.createUrlSuffix(date));
    }

    @Override
    protected String createUrlSuffix(Date date) {
        return "jz" + (date.getYear() - 100) + nf.format(date.getMonth() + 1) + nf.format(date.getDate()) + ".puz";
    }
}*/