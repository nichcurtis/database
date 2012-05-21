<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Database Auth driver
 *
 * [!!] this Auth driver does not support roles
 *
 * @package    	Kohana/Auth
 * @author		Nicholas Curtis		<nich.curtis@gmail.com>
 */
class Kohana_Auth_Database extends Auth
{
	/**
	 * Constructor loads the user list into the class.
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Logs a user in.
	 *
	 * @param 	string	 	username
	 * @param 	string   	password
	 * @param 	boolean	enable autologin (not supported)
	 * @return	boolean
	 */
	protected function _login($username, $password, $remember)
	{
		if (is_string($password))
		{
			// Create a hashed password
			$password = $this->hash($password, $username);
		}

		$query = DB::select()
				->from($this->_config['table_name'])
				->where('username', '=', $username)
				->where('password', '=', $password);

		$result = $query->execute($this->_config['db_instance']);

		if ($result->count() === 1)
		{
			$user = $result->current();

			if ($user['password'] === $password)
			{
				// remove password from session
				unset($user['password']);

				return $this->complete_login($user);
			}
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param 	mixed		username
	 * @return	boolean
	 */
	public function force_login($username)
	{
		$query = DB::select()
				->from($this->_config['table_name'])
				->where('username', '=', $username);

		$result = $query->execute($this->_config['db_instance']);

		if ($result->count() === 1)
		{
			return $this->complete_login($result->current());
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Perform a hmac hash, using the configured method.
	 *
	 * @param  	string		string to hash
	 * @param  	string		hash key to use
	 * @return 	string
	 */
	public function hash($str, $hash_key=null)
	{
		if ($hash_key === null)
		{
			if ( ! $this->_config['hash_key'])
				throw new Kohana_Exception('A valid hash key must be set in your auth config.');
			$hash_key = $this->_config['hash_key'];
		}

		return hash_hmac($this->_config['hash_method'], $str, $hash_key);
	}

	/**
	 * Compare password with original (plain text). Works for current (logged in) user
	 *
	 * @param	string		$password
	 * @return 	boolean
	 */
	public function check_password($password)
	{
		$user = $this->get_user();

		if ( ! array_key_exists('username', $user) OR $user['username'] === FALSE)
		{
			return FALSE;
		}

		return ($password === $this->hash($user['password'], $user['username']));
	}

} // End Auth Database
