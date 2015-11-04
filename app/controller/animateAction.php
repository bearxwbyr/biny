<?php
/**
 * html5
 */
class animateAction extends baseAction
{
    public function execute()
    {
        return $this->display('animate/main');
    }
}