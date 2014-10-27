<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

    // related search
    public function related()
    {
        $keyword = $this->input->post('keyword');
        $data = [];

        if ($keyword === false) return 1;

        // replace space to +
        $keyword = preg_replace("/\s/", "+", $keyword);
        // search in google
        $g_url = 'https://www.google.co.jp/search?q='.$keyword;

        // get html source
        $html = file_get_contents($g_url);
        mb_language("Japanese");
        $html = mb_convert_encoding($html, 'UTF-8', 'auto');

        // parse dom
        $doc = new DOMDocument();
        // hide warning error.
        @$doc->loadHTML($html);

        $elements = $doc->getElementsByTagName('p');

        if (!$elements) return 1;

        foreach ($elements as $e) {
            // search option
            if (!is_null($e->previousSibling)) {
               continue; 
            }
            $data[] = $e->textContent;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));

    }

    // morphological analysis search
    public function happy()
    {
        $keyword = $this->input->post('keyword');
        $data = [];

        if ($keyword === false) return 1;

        // replace space to +
        $keyword = preg_replace("/\s/", "+", $keyword);
        // search in google
        $g_url = 'https://www.google.co.jp/search?q='.$keyword;

        // get html source
        $html = file_get_contents($g_url);
        mb_language("Japanese");
        $html = mb_convert_encoding($html, 'UTF-8', 'auto');

        // parse dom
        $doc = new DOMDocument();
        // hide warning error.
        @$doc->loadHTML($html);

        // $elements = $doc->getElementById('topstuff');
        $elements = $doc->getElementsByTagName('h3');

        $first = '';
        foreach ($elements as $e) {
            // search option
            if (!is_null($e->previousSibling)) {
               continue; 
            }
            // get first score
            $first = $e->textContent;
            break;
        }
        if (!$first) return 1; 

        // mecab
        /*
        foreach (array_unique(mecab_split($first)) as $value) {
            $data[] = $value;
        }
         */
        $mecab = new Mecab_Tagger();
        for($node=$mecab->parseToNode($first); $node; $node=$node->getNext()){
            if($node->getStat() != 2 && $node->getStat() != 3){
                // if (preg_match('/名詞/', $node->getFeature()) || preg_match('/感動詞/', $node->getFeature())) {
                if (preg_match('/名詞/', $node->getFeature())) {
                    $data[] = $node->getSurface();
                }
            }
        } 
        if (!$data) return 1;

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
