<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

# hogehage
class Top extends CI_Controller {

    public function index()
    {
        $data = array();

        $this->load->view('top/index');
    }

    public function d3_test()
    {
        $data = array();

        $this->load->view('top/d3_test');
    }

    public function d3_test2()
    {
        $data = array();

        $this->load->view('top/d3_test2');
    }
    
    public function d3_test3()
    {
        $data = array();

        $this->load->view('top/d3_test3');
    }

    public function d3_test4()
    {
        $data = array();

        $this->load->view('top/d3_test4');
    }
    
    public function d3_test5()
    {
        $data = array();

        $this->load->view('top/d3_test5');
    }

    public function ajax_test()
    {
        $data = array();

        $this->load->view('top/ajax_test');
    }
}
