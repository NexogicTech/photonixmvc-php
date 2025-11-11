<?php
namespace App\Home\Controller;

use App\baseController;
use PhotonixCore\Photonix;
use function PhotonixCore\html;

class Index extends baseController
{
    public function index(): object
    {
       return html("<iframe src='https://www.nexogic.org'></iframe>");
    }

    public function hello(string $name = "Photonix MVC"): object
    {
        return html(Photonix::version()." Hello, $name!");
    }
}