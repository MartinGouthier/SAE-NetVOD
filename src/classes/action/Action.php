<?php

namespace iutnc\netvod\action;


abstract class Action {

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    public function __construct(){

        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    abstract public function GET() : string;

    abstract public function POST() : string;

    public function execute() : string{
        $html = null;
        if ($this->http_method === "POST")
            $html = $this->POST();
        elseif ($this->http_method === "GET")
            $html = $this->GET();
        return $html;
    }

}