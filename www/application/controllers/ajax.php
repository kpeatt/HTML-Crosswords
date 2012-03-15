<?php

class Ajax extends Controller {

  public function Ajax() {
  
    parent::Controller(); 
  }

  public function username_taken()
  {
    $this->load->model('MUser', '', TRUE);
    $username = trim($_POST['username']);
    // if the username exists return a 1 indicating true
    if ($this->MUser->username_exists($username)) {
      echo '1';
    }
  }

}

/* End of file ajax.php */
/* Location: ./application/controllers/ajax.php */