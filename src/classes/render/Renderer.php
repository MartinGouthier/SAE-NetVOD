<?php

namespace iutnc\netvod\render;
interface Renderer {
    const COMPACT = 1;
    const LONG = 2;
    const SERIEPREF = 3;
    const SERIERECENTE = 4;
    const COMMENTAIRES = 5;
    function render(int $selecteur) : string;
}
