<?php

namespace StevoTVRBot;

class ItemsBot extends Bot
{
	public function exec()
	{
		switch (filter_input(INPUT_GET, 'action'))
		{
			case 'find':
			    $this->find();
				break;
			case 'sell':
			    $this->sell();
				break;
		}
	}

	private function find()
	{
		if (!$this->authorize())
		{
			return;
		}

		$user = filter_input(INPUT_GET, 'user');

 		if (empty($user))
 		{
	        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
	        echo '400 Bad Request';
 			return;
 		}

		$itemId = $itemName = $itemValue = $modId = $modDesc = $modValue= null;

        if ($stmt = $this->db()->prepare("SELECT id, item, value FROM items ORDER BY -LOG(RAND()) / weight LIMIT 1;"))
        {
            $stmt->execute();
            $stmt->bind_result($itemId, $itemName, $itemValue);
            $stmt->fetch();
            $stmt->close();
        }

        if ($stmt = $this->db()->prepare("SELECT id, description, value FROM modifiers ORDER BY -LOG(RAND()) / weight LIMIT 1;"))
        {
            $stmt->execute();
            $stmt->bind_result($modId, $modDesc, $modValue);
            $stmt->fetch();
            $stmt->close();
        }

        if (!$itemId || !$modId)
        {
	        echo 'No items could be found.';
        	return;
        }

        $description = $modDesc . ' ' . $itemName;
        $value = ($modValue / 100) * $itemValue;

        if ($stmt = $this->db()->prepare("INSERT INTO inventory (user, modifier, item, value, description) VALUES (?, ?, ?, ?, ?);"))
        {
            $stmt->bind_param('siiis', $user, $modId, $itemId, $value, $description);
            $stmt->execute();
            $stmt->close();

	        printf('%s found %s worth $%d', $user, $description, $value);
        }
 	}

 	private function sell()
 	{
		if (!$this->authorize())
		{
			return;
		}

 		$user = filter_input(INPUT_GET, 'user');
 		$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT, ['min_range' => 0]);

 		if (empty($user) || !$itemId)
 		{
	        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
	        echo '400 Bad Request';
 			return;
 		}

        if ($stmt = $this->db()->prepare("SELECT value, description FROM inventory WHERE id = ? AND user = ?;"))
        {
            $stmt->bind_param('is', $itemId, $user);
            $stmt->execute();
            $stmt->bind_result($value, $description);
            $valid = $stmt->fetch();
            $stmt->close();

            if (!$value)
            {
            	printf('%s, that item could not be found in your inventory.', $user);
            	return;
            }

	        if ($stmt = $this->db()->prepare("DELETE FROM inventory WHERE id = ?;"))
	        {
	            $stmt->bind_param('i', $itemId);
	            $stmt->execute();
	            $stmt->close();

		        printf('!addpoints %s %d', $user, $value);
	        }
        }
 	}
}
