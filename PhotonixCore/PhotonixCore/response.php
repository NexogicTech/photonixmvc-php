<?php

namespace PhotonixCore;

class HtmlResponse {
    public string $content;

    public function __construct(string $content) {
        $this->content = $content;
    }
}

function html(string $content): HtmlResponse {
    return new HtmlResponse($content);
}