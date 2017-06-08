<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Jsmin Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Java Script
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/jsmin
 */
class CI_Jsmin {
	
	const ORD_LF			= 10;
	const ORD_SPACE			= 32;
	const ACTION_KEEP_A		= 1;
	const ACTION_DELETE_A	= 2;
	const ACTION_DELETE_A_B = 3;

	protected $a			= '';
	protected $b			= '';
	protected $input		= '';
	protected $inputIndex	= 0;
	protected $inputLength 	= 0;
	protected $lookAhead	= null;
	protected $output		= '';

	public function __construct($input = NULL)
	{
		if( !empty($input) )
		{
				$this->input = str_replace("\r\n", "\n", $input);
				$this->inputLength = strlen($this->input);
		}
		log_message('debug', "Jsmin Class Initialized");
	}
	public function minify($input)
	{
		if( !empty($input) )
		{
				$this->input = str_replace("\r\n", "\n", $input);
				$this->inputLength = strlen($this->input);
		}
		return $this->min();
	}
	// --------------------------------------------------------------------
	/**
	 * Action
	 *
	 * do action depending in $command
	 *
	 * action treats a string as a single character.
	 * action recognizes a regular expression if it is preceded by ( or , or =.
	 *
	 * @access	public
	 * @param	int $command One of class constants:
	 *			ACTION_KEEP_A		Output A. Copy B to A. Get the next B.
	 *			ACTION_DELETE_A		Copy B to A. Get the next B. (Delete A).
	 *			ACTION_DELETE_A_B	Get the next B. (Delete B).		 
	 */

	protected function action( $command ) 
	{
		switch($command) {
			case self::ACTION_KEEP_A:
				$this->output .= $this->a;

			case self::ACTION_DELETE_A:
				$this->a = $this->b;

				if ($this->a === "'" || $this->a === '"') 
				{
					for (;;) 
					{
						$this->output .= $this->a;
						$this->a = $this->get();

						if ($this->a === $this->b) 
						{
							break;
						}

						if (ord($this->a) <= self::ORD_LF) 
						{
							log_message('debug', "Jsmin: Unterminated string literal.");
							// throw new JSMinException('Unterminated string literal.');
						}

						if ($this->a === '\\') 
						{
							$this->output .= $this->a;
							$this->a = $this->get();
						}
					}
				}

			case self::ACTION_DELETE_A_B:
				$this->b = $this->next();

				if ($this->b === '/' && ( $this->a === '(' || $this->a === ',' || $this->a === '=' || $this->a === ':' || $this->a === '[' || $this->a === '!' ||
					$this->a === '&' || $this->a === '|' || $this->a === '?' || $this->a === '{' || $this->a === '}' || $this->a === ';' || $this->a === "\n" ))
				{
					$this->output .= $this->a . $this->b;

					for (;;) 
					{
						$this->a = $this->get();

						if ($this->a === '[') 
						{
							for (;;) 
							{
								$this->output .= $this->a;
								$this->a = $this->get();

								if ($this->a === ']') 
								{
									break;
								} 
								elseif ($this->a === '\\') 
								{
									$this->output .= $this->a;
									$this->a = $this->get();
								} 
								elseif (ord($this->a) <= self::ORD_LF) 
								{
									// throw new JSMinException('Unterminated regular expression set in regex literal.');
									log_message('debug', "Jsmin: Unterminated regular expression set in regex literal.");
								}
							}
						} 
						elseif ($this->a === '/') 
						{
							break;
						} 
						elseif ($this->a === '\\') 
						{
							$this->output .= $this->a;
							$this->a = $this->get();
						} 
						elseif (ord($this->a) <= self::ORD_LF) 
						{
							// throw new JSMinException('Unterminated regular expression literal.');
							log_message('debug', "Jsmin: Unterminated regular expression literal.");
						}

						$this->output .= $this->a;
					}

					$this->b = $this->next();
				}
		}
	}
	// --------------------------------------------------------------------	
	/**
	 * get
	 *
	 * Get next char. Convert ctrl char to space.
	 *
	 * @return string|null
	 */
	protected function get() 
	{
		$c = $this->lookAhead;
		$this->lookAhead = null;

		if ($c === null) 
		{
			if ($this->inputIndex < $this->inputLength) 
			{
				$c = substr($this->input, $this->inputIndex, 1);
				$this->inputIndex += 1;
			} 
			else 
			{
				$c = null;
			}
		}

		if ($c === "\r") 
		{
			return "\n";
		}

		if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) 
		{
			return $c;
		}

		return ' ';
	}	
	// --------------------------------------------------------------------	
	/**
	 * isAlphaNum
	 *
	 * Is $c a letter, digit, underscore, dollar sign, or non-ASCII character
	 *
	 * @return boolean
	 */
	protected function isAlphaNum($c)
	{
		return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
	}
	// --------------------------------------------------------------------	
	/**
	 * min
	 *
	 * Perform minification, return result
	 *
	 * @return string
	 */
	protected function min() 
	{
		$this->a = "\n";
		$this->action(self::ACTION_DELETE_A_B);

		while ($this->a !== null) 
		{
			switch ($this->a) 
			{
				case ' ':
					if ($this->isAlphaNum($this->b)) 
					{
						$this->action(self::ACTION_KEEP_A);
					} 
					else
					{
						$this->action(self::ACTION_DELETE_A);
					}
					break;

				case "\n":
					switch ($this->b)
					{
						case '{':
						case '[':
						case '(':
						case '+':
						case '-':
							$this->action(self::ACTION_KEEP_A);
							break;

						case ' ':
							$this->action(self::ACTION_DELETE_A_B);
							break;

						default:
							if ($this->isAlphaNum($this->b)) 
							{
								$this->action(self::ACTION_KEEP_A);
							}
							else
							{
								$this->action(self::ACTION_DELETE_A);
							}
					}
					break;

				default:
					switch ($this->b) 
					{
						case ' ':
							if ($this->isAlphaNum($this->a)) 
							{
								$this->action(self::ACTION_KEEP_A);
								break;
							}

							$this->action(self::ACTION_DELETE_A_B);
							break;

						case "\n":
							switch ($this->a) 
							{
								case '}':
								case ']':
								case ')':
								case '+':
								case '-':
								case '"':
								case "'":
									$this->action(self::ACTION_KEEP_A);
									break;

								default:
									if ($this->isAlphaNum($this->a)) 
									{
										$this->action(self::ACTION_KEEP_A);
									}
									else
									{
										$this->action(self::ACTION_DELETE_A_B);
									}
							}
							break;

						default:
							$this->action(self::ACTION_KEEP_A);
							break;
					}
			}
		}

		return $this->output;
	}
	// --------------------------------------------------------------------		
	/**
	 * next
	 * Get the next character, skipping over comments. peek() is used to see
	 *	if a '/' is followed by a '/' or '*'.
	 *
	 * @return string
	 */
	protected function next()
	{
		$c = $this->get();

		if ($c === '/')
		{
			switch($this->peek())
			{
				case '/':
					for (;;)
					{
						$c = $this->get();

						if (ord($c) <= self::ORD_LF)
						{
							return $c;
						}
					}

				case '*':
					$this->get();

					for (;;)
					{
						switch($this->get())
						{
							case '*':
								if ($this->peek() === '/')
								{
									$this->get();
									return ' ';
								}
								break;

							case null:
								log_message('debug', "Jsmin: Unterminated comment.");
								// throw new JSMinException('Unterminated comment.');
						}
					}

				default:
					return $c;
			}
		}

		return $c;
	}		
	// --------------------------------------------------------------------	
	/**
	 * peek
	 *
	 * Get next char. If is ctrl character, translate to a space or newline.
	 *
	 * @return string|null
	 */	
	protected function peek() 
	{
		$this->lookAhead = $this->get();
		return $this->lookAhead;
	}
	
// END Jsmin Class
}
/* End of file Jsmin.php */
/* Location: ./system/libraries/Jsmin.php */