<?php

namespace steroids\widgets;

use steroids\base\Widget;

class ModalWrapper extends Widget
{
    public function run()
    {
        return $this->renderReact([], false);
    }
}