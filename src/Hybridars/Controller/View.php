<?php

namespace Hybridars\Controller;

class View
{
    protected $template_dir = 'templates/';
    protected $vars = array();

    public function __construct($template_dir = null) {
        if ($template_dir !== null) {
            $this->template_dir = $template_dir;
        }
    }
    
    public function render($template_file) {
        if (file_exists($this->template_dir.$template_file)) {
            //Starts output buffering
            ob_start();

            //Includes contents
            include $this->template_dir.$template_file;
            $buffer = ob_get_contents();
            ob_end_clean();

            //Returns output buffer
            return $buffer;
        } else {
            throw new \Exception('Template file ' . $template_file . ' not found in directory ' . $this->template_dir);
        }
    }
    
    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }
    
    public function __get($name) {
        return $this->vars[$name];
    }
}
