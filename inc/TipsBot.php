<?php

namespace StevoTVRBot;

class TipsBot extends Bot
{
	public function exec()
	{
		switch (filter_input(INPUT_GET, 'action'))
		{
			case 'add':
			    $this->addTip();
				break;
			case 'getall':
			    $this->getAllTips();
				break;
			default:
			    $this->getTip();
				break;
		}
	}

    private function addTip()
    {
        if (!$this->authorize())
        {
            return;
        }

        $user = filter_input(INPUT_GET, 'user') or '';
        $tip = trim(filter_input(INPUT_GET, 'tip'));
        $len = strlen($tip);

        if ($len < 2)
        {
            echo $user . ' Your tip message is too short (2 characters min, yours was ' . $len . ')';
        }
        else if ($len > 80)
        {
            echo $user . ' Your tip message is too long (80 characters max, yours was ' . $len . ')';
        }
        else
        {
            if ($stmt = $this->db()->prepare("INSERT INTO tips (user, message) VALUES (?, ?);"))
            {
                $stmt->bind_param('ss', $user, $tip);
                $stmt->execute();
                echo 'Tip #' . $this->db()->insert_id . ' has been added to the list';
                $stmt->close();
            }
        }
    }

    private function getTip()
    {
        if ($stmt = $this->db()->prepare("SELECT message FROM tips WHERE status = 'APPROVED' ORDER BY RAND() LIMIT 1;"))
        {
            $stmt->execute();
            $stmt->bind_result($message);
            $stmt->fetch();
            echo $message;
            $stmt->close();
        }
    }

    private function getAllTips()
    {
        header('Access-Control-Allow-Origin: *');

        $list = [];

        if ($stmt = $this->db()->prepare("SELECT message FROM tips WHERE status = 'APPROVED' ORDER BY RAND();"))
        {
            $stmt->execute();
            $stmt->bind_result($message);

            while ($stmt->fetch())
            {
                $list[] = $message;
            }

            $stmt->close();
        }

        echo json_encode($list);
    }
}
