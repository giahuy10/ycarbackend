<?php
/**
 * @package   angi4j
 * @copyright Copyright (C) 2009-2017 Nicholas K. Dionysopoulos. All rights reserved.
 * @author    Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 */

defined('_AKEEBA') or die();

class AngieModelJoomlaConfiguration extends AngieModelBaseConfiguration
{
	public function __construct($config = array(), AContainer $container = null)
	{
		// Call the parent constructor
		parent::__construct($config, $container);

		// Get the Joomla! version from the configuration or the session
		$jVersion = $this->container->session->get('jversion', '2.5.0');

		if (array_key_exists('jversion', $config))
		{
			$jVersion = $config['jversion'];
		}

		// Load the configuration variables from the session or the default configuration shipped with ANGIE
		$this->configvars = $this->container->session->get('configuration.variables');

		if (empty($this->configvars))
		{
			// Get default configuration based on the Joomla! version
			$v = '30';

			if (version_compare($jVersion, '2.5.0', 'ge') && version_compare($jVersion, '3.0.0', 'lt'))
			{
				$v = '25';
			}

			$className = 'J' . $v . 'Config';
			$filename = APATH_INSTALLATION . '/platform/models/jconfig/j' . $v . '.php';
			$this->configvars = $this->loadFromFile($filename, $className, true);

			if (!empty($this->configvars))
			{
				$this->saveToSession();
			}
		}
	}

	/**
	 * Loads the configuration information from a PHP file
	 *
	 * @param   string  $file              The full path to the file
	 * @param   string  $className         The name of the configuration class
	 * @param   bool    $useDirectInclude  Should I include the .php file (if true) or should I use the Pythia-derived
	 *                                     string parser method (if false, default). The latter is safer in case your
	 *                                     file contains arbitrary, executable PHP code instead of just a class
	 *                                     declaration.
	 *
     * @return  array
	 */
	public function loadFromFile($file, $className = 'JConfig', $useDirectInclude = false)
	{
		if (!$useDirectInclude)
		{
			return $this->extractConfiguration($file);
		}

		$ret = array();

		include_once $file;

		if (class_exists($className))
		{
			foreach (get_class_vars($className) as $key => $value)
			{
				$ret[$key] = $value;
			}
		}

		return $ret;
	}

	/**
	 * Get the contents of the configuration.php file
	 *
	 * @param   string $className The name of the configuration class, by default it's JConfig
	 *
	 * @return  string  The contents of the configuration.php file
	 */
	public function getFileContents($className = 'JConfig')
	{
		$out = "<?php\nclass $className {\n";
		foreach ($this->configvars as $name => $value)
		{
			if (is_array($value))
			{
				$pieces = array();

				foreach ($value as $key => $data)
				{
					$data = addcslashes($data, '\'\\');
					$pieces[] = "'" . $key . "' => '" . $data . "'";
				}

				$value = "array (\n" . implode(",\n", $pieces) . "\n)";
			}
			else
			{
				// Log and temp paths in Windows systems will be forward-slash encoded
				if ((($name == 'tmp_path') || ($name == 'log_path')))
				{
					$value = $this->TranslateWinPath($value);
				}
				$value = "'" . addcslashes($value, '\'\\') . "'";
			}
			$out .= "\tpublic $" . $name . " = " . $value . ";\n";
		}

		$out .= '}' . "\n";

		return $out;
	}

	/**
	 * Extracts the Joomla! Global Configuration from a configuration.php file without including the file. This works
	 * very well with most sites, as long as the configuration was not messed with by the user.
	 *
	 * @param   string  $filePath  The absolute path to the configuration.php file
	 *
	 * @return  array
	 */
	private function extractConfiguration($filePath)
	{
		$ret = array();

		$fileContents = file($filePath);

		foreach ($fileContents as $line)
		{
			$line = trim($line);

			if ((strpos($line, 'public') !== 0) && (strpos($line, 'var') !== 0))
			{
				continue;
			}

			if (strpos($line, 'public') === 0)
			{
				$line = substr($line, 6);
			}
			else
			{
				$line = substr($line, 3);
			}

			$line = trim($line);
			$line = rtrim($line, ';');
			$line = ltrim($line, '$');
			$line = trim($line);
			list($key, $value) = explode('=', $line);
			$key   = trim($key);
			$value = trim($value);

			if ((strstr($value, '"') === false) && (strstr($value, "'") === false))
			{
				continue;
			}

			$value = $this->parseStringDefinition($value);

			$ret[$key] = $value;
		}

		return $ret;
	}

	/**
	 * Parses a string definition, surrounded by single or double quotes, removing any comments which may be left tucked
	 * to its end, reducing escaped characters to their unescaped equivalent and returning the clean string.
	 *
	 * @param   string  $value
	 *
	 * @return  null|string  Null if we can't parse $value as a string.
	 */
	private function parseStringDefinition($value)
	{
		// At this point the value may be in the form 'foobar');#comment'gargh" if the original line was something like
		// define('DB_NAME', 'foobar');#comment'gargh");

		$quote = $value[0];

		// The string ends in a different quote character. Backtrack to the matching quote.
		if (substr($value, -1) != $quote)
		{
			$lastQuote = strrpos($value, $quote);

			// WTF?!
			if ($lastQuote <= 1)
			{
				return null;
			}

			$value = substr($value, 0, $lastQuote + 1);
		}

		// At this point the value may be cleared but still in the form 'foobar');#comment'
		// We need to parse the string like PHP would. First, let's trim the quotes
		$value = trim($value, $quote);

		$pos = 0;

		while ($pos !== false)
		{
			$pos = strpos($value, $quote, $pos);

			if ($pos === false)
			{
				break;
			}

			if (substr($value, $pos - 1, 1) == '\\')
			{
				$pos++;

				continue;
			}

			$value = substr($value, 0, $pos);
		}

		// Finally, reduce the escaped characters.

		if ($quote == "'")
		{
			// Single quoted strings only escape single quotes and backspaces
			$value = str_replace(array("\\'", "\\\\",), array("'", "\\"), $value);
		}
		else
		{
			// Double quoted strings just need stripslashes.
			$value = stripslashes($value);
		}

		return $value;
	}

}