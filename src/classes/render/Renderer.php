<?php

namespace iutnc\netvod\render;
interface Renderer {
    const COMPACT = 1;
    const LONG = 2;
    const SERIEPREF = 3;
    function render(int $selecteur) : string;
}
