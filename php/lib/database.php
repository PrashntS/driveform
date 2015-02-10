<?php
namespace DriveForm\Database;

/**
 * Builds a MySQL Database Handler object.
 * @author  Prashant Sinha <prashant@ducic.ac.in>
 */
class Client {

    private $DbHandle;

    /**
     * Contains Count of Handle Objects Created.
     * @var integer
     */
    private static $Count = 0;

    /**
     * Default Constructor. Constructs the Handle Object.
     */
    function __construct() {
        global $_CONFIG;
        $connection_string = 'sqlite:' . $_CONFIG->config_dir() . '/' . $_CONFIG->sql;
        try {
            $this->DbHandle = new \PDO($connection_string);
            $this->DbHandle->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION );
            Client::$Count++;
        }
        catch (\PDOException $E) {
            echo "Cannot find the database.";
            die();
        }
    }

    // Sometimes
    // I believe all my Comments are ignored..

    /**
     * Executes a Query.
     * @param string $query Formed Query String.
     * @param array $Arr Optional. Contains $Query parameters.
     * @return \PDO Binding.
     */
    public function query($query, $Arr = array()) {
        try {
            $this->DbHandle->beginTransaction();
            $Binding = $this->DbHandle->prepare($query);
            var_dump($this->DbHandle->prepare($query));
            $Binding->execute($Arr);
            $this->DbHandle->Commit();
            return ($Binding);
        }
        catch (PDOException $E) {
            $this->DbHandle->rollback();
            return NULL;
        }
    }

    /**
     * Executes a stand-alone query without explicit Database object.
     * @see     <class> Handler
     * @static
     * @example Preffered usage is in Single query environments. Create a object in all other cases.
     * @param string $Query Formed Query String.
     * @param array $Arr Optional. Contains $Query parameters.
     * @return  \PDO Binding
     */
    public static function exec($Query, $Arr = array()) {
        $InnerHandle = new Handler;
        Handler::$Count++;
        return $InnerHandle->query($Query, $Arr);
    }
}