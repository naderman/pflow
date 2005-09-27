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
        print ("We use this crappy commandline parsing until the ConsoleTools package is made.\n\n");
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
        $settings = ezcDbFactory::parseDSN( $dsn );
       
        // Store the settings
        $ts = ezcTestSettings::getInstance();
        $ts->db->dsn = $dsn;
        try
        {
            $ts->setDatabaseSettings( $settings );
            $db = ezcDbFactory::create( $settings );
            ezcDbInstance::set( $db );
        } catch( ezcDbException $e)
        {
            switch( $e->getCode() )
            {
                case ezcDbException::MISSING_DATABASE_NAME: $this->printError( "The database name is missing."); break;
                case ezcDbException::MISSING_USER_NAME: $this->printError( "The username is missing."); break;
                case ezcDbException::MISSING_PASSWORD: $this->printError( "The password is missing."); break;
                case ezcDbException::UNKNOWN_IMPL: $this->printError( "Unknown PDO implementation. Make sure you specified an existing driver (mysql, pgsql, oci) and that your PHP version has the modules (php -m): PDO and pdo_<driver> (e.g. pdo_mysql, pdo_pgsql, etc)."); break;
                case ezcDbException::INSTANCE_NOT_FOUND: $this->printError( "Cannot find the db instance."); break;
                case ezcDbException::NOT_IMPLEMENTED: $this->printError( "The functionality is not implemented."); break;
                default: $this->getTraceAsString(); break;
            }
            exit();
        }

        // TODO Check if the database exists, and whether it is empty.
        
    }

    protected function printError( $errorString )
    {
        print( $errorString . "\n\n");

        print( "The DSN should look like: <Driver>://<User>[:Password]@<Host>/<Database> \n");
        print( "For example: mysql://root:root@localhost/unittests\n\n");
        exit();
    }
}
?>
