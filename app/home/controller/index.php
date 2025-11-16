<?php
namespace App\Home\Controller;

use App\baseController;
use PhotonixCore\Photonix;
use PhotonixCore\Request;
use PhotonixCore\View\View;

class Index extends baseController
{
    public function index(Request $request): string
    {
       return '<!DOCTYPE html><html><head><meta charset="UTF-8"><link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"><title>Photonix</title><style>body,html{margin:0;padding:0;height:100%;overflow:hidden}iframe{width:100vw;height:100vh;border:none}</style></head><body><iframe src="https://www.nexogic.org" allowfullscreen></iframe></body></html>';
    }

    /**
     * @param string $name
     * @return string
     */
    public function hello(Request $request, string $name = "Photonix MVC"): string
    {
        $all = $request->get();
        $first = is_array($all) ? (array_values($all)[0] ?? null) : null;
        $actual = $request->get('name', $request->get('vd', $first ?? $name));
        return Photonix::version()." Hello, $actual!";
    }

    public function version(Request $request): string
    {
        return View::display("home");
    }
}