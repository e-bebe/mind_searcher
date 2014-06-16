<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Top extends CI_Controller {

    public function index()
    {
        $data = array();

        $this->load->view('top/index');
    }

}
