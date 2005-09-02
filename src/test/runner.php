<?php

// Prevent that our extended class starts to run. 
if (!defined('PHPUnit2_MAIN_METHOD')) 
{
    define('PHPUnit2_MAIN_METHOD', 'TestRunner::main');
}

require_once 'PHPUnit2/TextUI/TestRunner.php';
require_once 'PHPUnit2/Util/Filter.php';


class ezcTestRunner extends PHPUnit2_TextUI_TestRunner 
{
	const SUITE_FILENAME = "tests/suite.php";

    public function __construct()
    {
        // Call this method only once?
        $printer = new ezcTestPrinter();
        $this->setPrinter($printer);
         
        // Remove this file name from the assertion trace.
        // (Displayed when a test fails)
        PHPUnit2_Util_Filter::addFileToFilter(__FILE__);     
    }
	
	/**
	 * For now, until the Console Tools is finished, we use the following 
	 * parameters:
	 *
	 * Arguments:
	 * 
	 * [1] => Database DSN
	 * [2] => Database DSN, Suite file.
	 * [3] => Database DSN, file, class name.
	 * 
	 */
    public static function main( $args ) 
	{
		$tr = new ezcTestRunner();
		$tr->runFromArguments( $args );
	}

	public function showHelp()
	{
		print ("./runtests DSN [ [Suite name] file_name ]\n\n");
        print ("We use this crappy commandline parsing until the ConsoleTools package is made.");
	}

	public function runFromArguments( $args )
	{
		if ( count( $args )  < 2 ) 
		{
			$this->showHelp();
			return;
		}
		$this->initializeDatabase( $args[1] );

        $directory =  dirname( __FILE__ ) . "/../../../../";

        // If a package is given, use that package, otherwise parse all directories.
	    $packages = (isset($args[2]) ? array($args[2]) : $this->getPackages( $directory ));
		
        $allSuites = new ezcTestSuite("[Testing]");

		foreach ($packages as $package)
		{
			$releases = $this->getReleases( $directory, $package );

			foreach( $releases as $release )
			{
			 	$suite = $this->getTestSuite( $directory, $package, $release );

                if ( !is_null( $suite ) )
                    $allSuites->addTest($suite);
			}
		}

        $this->doRun($allSuites);
	}

	public function runTest( $filename )
	{
	}


	/**
	 * @param string $dir Absolute or relative path to directory to look in.
	 *
	 * @returns array Package names.
	 */
	protected function getPackages( $dir )
	{
		$packages = array();

		if ( is_dir( $dir ) ) 
		{
    		if ( $dh = opendir( $dir ) ) 
			{
        		while ( ( $entry = readdir( $dh ) ) !== false ) 
				{
					if( $this->isPackage( $dir, $entry ) ) $packages[] = $entry;
        		}
        		closedir( $dh );
    		}
 		}

		return $packages;
	}

	protected function isPackage( $dir, $entry ) 
	{
		// Prepend directory if needed.
		$fullPath = ( $dir == "" ? $entry : $dir ."/". $entry );

		// Check if it is a package.
		if( !is_dir( $fullPath ) ) return false;
		if( $entry[0] == "." ) return false; // .svn, ., .. 

		return true;
	}

	protected function isRelease( $dir, $entry ) 
	{
		// for now, they have the same rules.
		return $this->isPackage( $dir, $entry );
	}

	/**
	 * @returns array Releases from a package.
	 */
	protected function getReleases( $dir, $package )
	{
		$dir .= "/" . $package;

		$releases = array();
		if ( is_dir( $dir ) ) 
		{
    		if ( $dh = opendir( $dir ) ) 
			{
        		while ( ( $entry = readdir( $dh ) ) !== false ) 
				{
					if( $this->isRelease( $dir, $entry ) ) 
					{
						$releases[] = $entry;
					}
        		}
        		closedir( $dh );
    		}
 		}

		return $releases;
	}

	/**
	 * Runs a specific test suite from a package and release.
	 *
	 * @returns boolean True if the test has been run, false if not. 
	 */
	protected function getTestSuite( $dir, $package, $release )
	{
		$suitePath = implode( "/", array( $dir, $package, $release, self::SUITE_FILENAME ) );
		if( file_exists( $suitePath ) )
		{
			require_once( $suitePath );

			$className = "ezc". $package . "Suite";
			$s = call_user_func( array( $className, 'suite' ) );

            return $s;
		}

        return null;
	}

	protected function initializeDatabase( $dsn )
	{
		$settings = $this->parseDSN( $dsn );

		print( "Pretend to initialize the database with the following settings: " );
		print_r( $settings );
	}

    // {{{ parseDSN()

    /**
	 * This function is 'borrowed' from PEAR /DB.php . 
	 *
	 * 
     * Parse a data source name
     *
     * Additional keys can be added by appending a URI query string to the
     * end of the DSN.
     *
     * The format of the supplied DSN is in its fullest form:
     * <code>
     *  phptype(dbsyntax)://username:password@protocol+hostspec/database?option=8&another=true
     * </code>
     *
     * Most variations are allowed:
     * <code>
     *  phptype://username:password@protocol+hostspec:110//usr/db_file.db?mode=0644
     *  phptype://username:password@hostspec/database_name
     *  phptype://username:password@hostspec
     *  phptype://username@hostspec
     *  phptype://hostspec/database
     *  phptype://hostspec
     *  phptype(dbsyntax)
     *  phptype
     * </code>
     *
     * @param string $dsn Data Source Name to be parsed
     *
     * @return array an associative array with the following keys:
     *  + phptype:  Database backend used in PHP (mysql, odbc etc.)
     *  + dbsyntax: Database used with regards to SQL syntax etc.
     *  + protocol: Communication protocol to use (tcp, unix etc.)
     *  + hostspec: Host specification (hostname[:port])
     *  + database: Database to use on the DBMS server
     *  + username: User name for login
     *  + password: Password for login
     */
    function parseDSN($dsn)
    {
        $parsed = array(
            'phptype'  => false,
            'dbsyntax' => false,
            'username' => false,
            'password' => false,
            'protocol' => false,
            'hostspec' => false,
            'port'     => false,
            'socket'   => false,
            'database' => false,
        );

        if (is_array($dsn)) {
            $dsn = array_merge($parsed, $dsn);
            if (!$dsn['dbsyntax']) {
                $dsn['dbsyntax'] = $dsn['phptype'];
            }
            return $dsn;
        }

        // Find phptype and dbsyntax
        if (($pos = strpos($dsn, '://')) !== false) {
            $str = substr($dsn, 0, $pos);
            $dsn = substr($dsn, $pos + 3);
        } else {
            $str = $dsn;
            $dsn = null;
        }

        // Get phptype and dbsyntax
        // $str => phptype(dbsyntax)
        if (preg_match('|^(.+?)\((.*?)\)$|', $str, $arr)) {
            $parsed['phptype']  = $arr[1];
            $parsed['dbsyntax'] = !$arr[2] ? $arr[1] : $arr[2];
        } else {
            $parsed['phptype']  = $str;
            $parsed['dbsyntax'] = $str;
        }

        if (!count($dsn)) {
            return $parsed;
        }

        // Get (if found): username and password
        // $dsn => username:password@protocol+hostspec/database
        if (($at = strrpos($dsn,'@')) !== false) {
            $str = substr($dsn, 0, $at);
            $dsn = substr($dsn, $at + 1);
            if (($pos = strpos($str, ':')) !== false) {
                $parsed['username'] = rawurldecode(substr($str, 0, $pos));
                $parsed['password'] = rawurldecode(substr($str, $pos + 1));
            } else {
                $parsed['username'] = rawurldecode($str);
            }
        }

        // Find protocol and hostspec

        if (preg_match('|^([^(]+)\((.*?)\)/?(.*?)$|', $dsn, $match)) {
            // $dsn => proto(proto_opts)/database
            $proto       = $match[1];
            $proto_opts  = $match[2] ? $match[2] : false;
            $dsn         = $match[3];

        } else {
            // $dsn => protocol+hostspec/database (old format)
            if (strpos($dsn, '+') !== false) {
                list($proto, $dsn) = explode('+', $dsn, 2);
            }
            if (strpos($dsn, '/') !== false) {
                list($proto_opts, $dsn) = explode('/', $dsn, 2);
            } else {
                $proto_opts = $dsn;
                $dsn = null;
            }
        }

        // process the different protocol options
        $parsed['protocol'] = (!empty($proto)) ? $proto : 'tcp';
        $proto_opts = rawurldecode($proto_opts);
        if ($parsed['protocol'] == 'tcp') {
            if (strpos($proto_opts, ':') !== false) {
                list($parsed['hostspec'],
                     $parsed['port']) = explode(':', $proto_opts);
            } else {
                $parsed['hostspec'] = $proto_opts;
            }
        } elseif ($parsed['protocol'] == 'unix') {
            $parsed['socket'] = $proto_opts;
        }

        // Get dabase if any
        // $dsn => database
        if ($dsn) {
            if (($pos = strpos($dsn, '?')) === false) {
                // /database
                $parsed['database'] = rawurldecode($dsn);
            } else {
                // /database?param1=value1&param2=value2
                $parsed['database'] = rawurldecode(substr($dsn, 0, $pos));
                $dsn = substr($dsn, $pos + 1);
                if (strpos($dsn, '&') !== false) {
                    $opts = explode('&', $dsn);
                } else { // database?param1=value1
                    $opts = array($dsn);
                }
                foreach ($opts as $opt) {
                    list($key, $value) = explode('=', $opt);
                    if (!isset($parsed[$key])) {
                        // don't allow params overwrite
                        $parsed[$key] = rawurldecode($value);
                    }
                }
            }
        }

        return $parsed;
    }

    // }}}
}
	
	

?>
