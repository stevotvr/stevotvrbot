<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Page;

use StevoTVRBot\Config;
use StevoTVRBot\Model\UsersModel;

/**
 * Handler for the authorization page.
 */
class AuthPage extends Page
{
	/**
	 * @inheritDoc
	 */
	public function run(array $params)
	{
		if (!filter_has_var(INPUT_GET, 'code'))
		{
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
			echo '400 Bad Request';
			return;
		}

		if (!session_id())
		{
			session_start();
		}

		if (filter_input(INPUT_GET, 'state') !== $_SESSION['state'])
		{
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
			echo '400 Bad Request';
			return;
		}

		$code = filter_input(INPUT_GET, 'code');

		$ch = curl_init('https://id.twitch.tv/oauth2/token');
		if ($ch === false)
		{
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
			echo '500 Internal Server Error';
			return;
		}

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, [
			'client_id'		=> Config::TWITCH_APP_ID,
			'client_secret'	=> Config::TWITCH_APP_SECRET,
			'code'			=> $code,
			'grant_type'	=> 'authorization_code',
			'redirect_uri'	=> Config::BASE_URL . 'auth',
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

		curl_close($ch);

		if ($responseCode === 200)
		{
			$response = json_decode($response, true);
			$user = UsersModel::setUserTokens($response['access_token'], $response['refresh_token']);
			if ($user)
			{
				setcookie('access_token', $response['access_token'], time() + 2592000);
				header('Location: ' . $_SESSION['return_url']);
				return;
			}
		}
	}
}
