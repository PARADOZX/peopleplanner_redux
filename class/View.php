<?php

class View
{
    private $template;
    
    public function __construct($file)
    {
        if (!file_exists('views/' . $file . '.php')) throw new Exception('view does not exist');
        
        $this->template = $file;
    }
    
    public function render(Array $data)
    {
        // extract($data);  //unnecessary as we loop through $data

        ob_start();
        include('views' . DIRECTORY_SEPARATOR . $this->template . '.php');
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }
}