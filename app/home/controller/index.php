<?php
namespace App\Home\Controller;

use App\baseController;
use PhotonixCore\Photonix;

class Index extends baseController
{
    public function index(): string
    {
       return '<!DOCTYPE html><html><head><meta charset="UTF-8"><link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"><title>Photonix</title><style>body,html{margin:0;padding:0;height:100%;overflow:hidden}iframe{width:100vw;height:100vh;border:none}</style></head><body><iframe src="https://www.nexogic.org" allowfullscreen></iframe></body></html>';
    }

    /**
     * @param string $name
     * @return string
     */
    public function hello(string $name = "Photonix MVC"): string
    {
        return Photonix::version()." Hello, $name!";
    }
    //两种方式渲染HTML
}