<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

# ajax
class Ajax extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // keywordを受け取る
        $keyword = $this->input->post('keyword');

// @todo debug
$keyword = 'IT';
    
        // keywordで検索


        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($keyword));
    }


}
