<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

# ajax
class Ajax extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = $this->input->post('number');

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }


}
