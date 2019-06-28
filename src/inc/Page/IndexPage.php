<?php

namespace StevoTVRBot\Page;

use StevoTVRBot\Model\TipsModel;

class IndexPage extends Page
{
    public function run(array $params)
    {
        $this->showTemplate('index');
    }
}
