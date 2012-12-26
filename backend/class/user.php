<?php
	//get db configuration
	require_once(dirname(__FILE__) . "/config/db.php");
	
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
			"banned_until"
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
			if(isset($_SESSION['session_data']))
			{
				//session_data is set
				//explode session data to get little bits
				$session_data = explode(":", $_SESSION['session_data']);
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
					$check = $this->db->prepare("SELECT COUNT(username) AS user_cnt, auto_logout FROM users WHERE username = :username AND password = :password AND email = :email AND access = :access AND banned_until = :banned_until");
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
							session_unset();
							session_destroy();
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
	}
?>