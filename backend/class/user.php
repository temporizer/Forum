<?php
	//get db configuration
	require_once(dirname(__FILE__) . "/../config/db.php");
	
	class User extends PDO
	{
		public $user = null;
		protected $db = null;
		protected $session_elements = array(
			"id",
			"username",
			"password",
			"email",
			"access",
			"banned_until",
			"post_count",
			"solved_count",
			"avatar",
			"description"
		);
		public $logged_in = false;
		
		function __construct()
		{
			$db = parent::__construct(
				'mysql:host=' . DB_HOST . ';dbname=' . DB_SCHEMA,
				DB_USER,
				DB_PASS
			);
			
			if($this->logged_in())
			{
				$this->logged_in = true;
			}
		}
		
		private function logged_in()
		{
			// changed to $_SESSION because of the user class
			if(isset($_SESSION))

			//if(isset($_SESSION['session_data']))
			{
				//session_data is set
				//explode session data to get little bits

				// changed to $_SESSION because of the user class
				$session_data = $_SESSION;
				//$session_data = explode(":", $_SESSION['session_data']);
				
				//session data should have as many elements as $this->session_elements.
				if(count($session_data) === count($this->session_elements))
				{
					//seems to be in check.
					//put session data in a good format for us
					$i = 0;
					foreach($session_data AS $val)
					{
						$this->user[$this->session_elements[$i]] = $val;
						$i++;
					}
					
					//does the actual user exist though?
					$check = $this->prepare(
						"SELECT
							COUNT(username)
								AS user_cnt,
							auto_logout
						FROM users
						WHERE
							username = :username AND
							password = :password AND
							email = :email AND
							access = :access AND
							banned_until = :banned_until"
					);
					$check->bindParam(":username", $this->user["username"], PDO::PARAM_STR);
					$check->bindParam(":password", $this->user["password"], PDO::PARAM_STR);
					$check->bindParam(":email", $this->user["email"], PDO::PARAM_STR);
					$check->bindParam(":access", $this->user["access"], PDO::PARAM_INT);
					$check->bindParam(":banned_until", $this->user["banned_until"], PDO::PARAM_INT);
					$check->execute();
					$data = $check->fetch(PDO::FETCH_ASSOC);
					
					if($data["user_cnt"] > 0)
					{
						if($data["auto_logout"] == "yes")
						{
							//automatically destroy their sessions and return false.
							$this->logout();
							return false;
						}
							else
						{
							//logged in!
							return true;
						}
					}
						else
					{
						//couldn't find that user at all, meaning invalid sessions or we deleted the user.
						return false;
					}
				}
					else
				{
					//didn't have the right amount of data in the session - must mean custom made or we changed the amount of elements.
					return false;
				}
			}
				else
			{
				//sessions aren't even set.
				return false;
			}
		}
		
		function logout()
		{
			session_unset();
			session_destroy();
		}
		
		function hash($value)
		{
			return hash('sha512', $value);
		}
		
		function redirect($location)
		{
			header('Location: ' . $location);
		}
		
		function create_session_data()
		{
			//get args
			$args = func_get_args();
			
			//If there's only one argument, and it's an array
			if(count($args) == 1 && is_array($args[0]))
			{
				// changed so $_SESSION = array as opposed to $_SESSION['session_data']
				return $args[0];

				//return implode(":", $args[0]);
			}
				else
			{
				return implode(":", $args);
			}
		}
		
		function categoryName($id)
		{
			$cat = $this->prepare("SELECT name FROM categories WHERE id = :id");
			$cat->bindParam(":id", $id, PDO::PARAM_INT);
			$cat->execute();
			$cat_name = $cat->fetch(PDO::FETCH_ASSOC);
			return (!empty($cat_name)) ? $cat_name['name'] : false;
		}
		
		function current_page()
		{
			if(!empty($_SERVER['HTTPS']))
			{
				$url = "https://" .
					$_SERVER['SERVER_NAME'] .
					(($_SERVER["SERVER_PORT"] != "80") ? ':' . $_SERVER["SERVER_PORT"] : '') .
					$_SERVER['REQUEST_URI'];
			}
				else
			{
				$url = "http://" .
					$_SERVER['SERVER_NAME'] .
					(($_SERVER["SERVER_PORT"] != "80") ? ':' . $_SERVER["SERVER_PORT"] : '') .
					$_SERVER['REQUEST_URI'];
			}
			return $url;
		}
		
		function parseTime($time, $type = "")
		{
			//if they feel they need an 'ago' on the time, the suffix adds it.
			$suffix = ($type == "before") ? 'ago' : (($type == "after") ? 'more' : '');
			
			//try to get days first
			if(($days = round(abs($time/86400), 0)) >= 1)
			{
				//if days is more than 6 days (one week).
				if($days > 6)
				{
					//get weeks
					$weeks = round(abs($days/7), 0);
					
					//if weeks is more than 3 weeks, should be a month.
					if($weeks > 3)
					{
						//return the full date
						return date("F d Y h:i A", $time);
					}
						else
					{
						//just return weeks
						return $weeks . ' week' . (($weeks == 1) ? '' : 's') . ' ' . $suffix;
					}
				}
					else
				{
					//return days
					return $days . ' day' . (($days == 1) ? '' : 's') . ' ' . $suffix;
				}
			}
				else
			//get hours if days doesn't work out
			if(($hours = round(abs($time/3600), 0)) >= 1)
			{
				//return hours
				return $hours . ' hour' . (($hours == 1) ? '' : 's') . ' ' . $suffix;
			}
				else
			//get minutes if hours doesn't work out
			if(($minutes = round(abs($time/60), 0)) >= 1)
			{
				//return minutes
				return $minutes . ' minute' . (($minutes == 1) ? '' : 's') . ' ' . $suffix;
			}
				else
			{
				//return seconds if all above fails.
				$seconds = round(abs($time), 0);
				return $seconds . ' second' . (($seconds == 1) ? '' : 's') . ' ' . $suffix;
			}
		}
	}
?>