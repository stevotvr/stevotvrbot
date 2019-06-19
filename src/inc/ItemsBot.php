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
		}
	}

	private function find()
	{
		if (!$this->authorize())
		{
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
        	return;
        }

		$user = filter_input(INPUT_GET, 'user') ?? '';
        $description = $modDesc . ' ' . $itemName;
        $value = ($modValue / 100) * $itemValue;

        if ($stmt = $this->db()->prepare("INSERT INTO inventory (user, modifier, item, value, description) VALUES (?, ?, ?, ?, ?);"))
        {
            $stmt->bind_param('siiis', $user, $modId, $itemId, $value, $description);
            $stmt->execute();
            $stmt->close();

	        printf('Found %s worth $%d', $description, $value);
        }
 	}
}
