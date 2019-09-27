<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Model;

use StevoTVRBot\Config;

/**
 * Model representing the users database.
 */
class UsersModel extends Model
{
	/**
	 * Cache of user data.
	 *
	 * @var array[]
	 */
	private static $userCache = [];

	/**
	 * Try to get the current authenticated user.
	 *
	 * @return array|null Array of user data for the currently authenticated
	 *                    user, or null if the user is not authenticated
	 */
	public static function getCurrentUser()
	{
		if (!session_id())
		{
			session_start();
		}

		if (isset($_SESSION['user_name']))
		{
			return self::getUser($_SESSION['user_name']);
		}

		$accessToken = filter_input(INPUT_COOKIE, 'access_token');
		if ($accessToken)
		{
			$user = self::validateToken($accessToken);
			if ($user)
			{
				$_SESSION['user_name'] = $user;
				return self::getUser($user);
			}
		}

		return null;
	}

	/**
	 * Get the data for a user from the database.
	 *
	 * @param string $name The name of the users
	 *
	 * @return array|null Array of user data, or null if the user does not
	 *                    exist
	 */
	public static function getUser(string $name)
	{
		if (isset(self::$userNames[$name]))
		{
			return self::$userCache[self::$userNames[$name]];
		}

		if ($stmt = self::db()->prepare('SELECT id, accessToken, refreshToken, isAdmin FROM users WHERE name = ?;'))
		{
			$stmt->bind_param('s', $name);
			$stmt->execute();
			$stmt->bind_result($id, $accessToken, $refreshToken, $isAdmin);
			$stmt->fetch();
			$stmt->close();

			if (isset($id))
			{
				$data = compact('id', 'name', 'accessToken', 'refreshToken', 'isAdmin');
				self::$userCache[$name] = $data;
				return $data;
			}
		}

		return null;
	}

	/**
	 * Initiate the OAuth2 authorization flow with Twitch.
	 *
	 * @param string $returnPath The URI path to which to return the user after
	 *                           authorization
	 */
	public static function initAuthFlow(string $returnPath = '')
	{
		session_start();

		$_SESSION['return_url'] = Config::BASE_URL . $returnPath;
		$_SESSION['state'] = md5(mt_rand());

		$query = http_build_query([
			'client_id'		=> Config::TWITCH_APP_ID,
			'redirect_uri'	=> Config::BASE_URL . 'auth',
			'response_type'	=> 'code',
			'scope'			=> '',
			'state'			=> $_SESSION['state'],
		]);
		header('Location: https://id.twitch.tv/oauth2/authorize?' . $query);
	}

	/**
	 * Set the access and refresh tokens for a user.
	 *
	 * @param string $access  The access token
	 * @param string $refresh The refresh token
	 *
	 * @return boolean Whether the tokens were valid
	 */
	public static function setUserTokens(string $access, string $refresh)
	{
		$name = self::validateToken($access);
		if ($name === false)
		{
			return false;
		}

		if ($stmt = self::db()->prepare('UPDATE users SET accessToken = ?, refreshToken = ? WHERE name = ?;'))
		{
			$stmt->bind_param('sss', $access, $refresh, $name);
			$stmt->execute();
			$stmt->close();
		}

		return true;
	}

	/**
	 * Validate an access token with the Twitch server.
	 *
	 * @param string $token The access token
	 *
	 * @return string|boolean The login for the user, or false if validation
	 *                        failed
	 */
	public static function validateToken(string $token)
	{
		$ch = curl_init('https://id.twitch.tv/oauth2/validate');
		if ($ch === false)
		{
			return false;
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: OAuth ' . $token,
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($ch), true);

		curl_close($ch);

		return (is_array($response) && isset($response['login'])) ? $response['login'] : false;
	}
}
