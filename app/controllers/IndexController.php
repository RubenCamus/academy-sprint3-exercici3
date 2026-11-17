<?php

class IndexController extends Controller{

    public function indexAction() {
        $this->view->message = "This is the root of the page";
    }

}
