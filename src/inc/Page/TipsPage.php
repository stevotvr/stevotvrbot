<?php

namespace StevoTVRBot\Page;

use StevoTVRBot\Model\TipsModel;

class TipsPage extends Page
{
    public function run()
    {
        header('Access-Control-Allow-Origin: *');
        echo json_encode(TipsModel::getAll() ?? []);
    }
}
