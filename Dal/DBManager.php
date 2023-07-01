<?php
namespace Dal;

use PDO;
use PDOException;

class DBManager {
    /**
     * DB file path
     * @var string
     */
    protected const CONFIG = __DIR__.'/../config.json';
    /**
     * Server address
     * @var string
     */
    protected $server;
    /**
     * DB name 
     * @var string
     */
    protected $db;
    /**
     * DB username
     * @var string
     */
    protected $username;
    /**
     * DB password
     * @var string
     */
    protected $pass;
    /**
     * DB connection
     * @var PDO
     */
    protected $conn = null;
    /**
     * ===================Schema tables =================
     */
    /**
     * Customer table
     * @const string
     */
    const CUSTOMER = 'customers';
    /**
     * Customer table
     * @const string
     */
    const PRODUCT = 'products';
    /**
     * Customer table
     * @const string
     */
    const SALE = 'sales';
    
    function __construct() {
        $this->server = '';
        $this->db = '';
        $this->username = '';
        $this->pass = '';
    }
    /**
     * Read DB credentials from config file
     * 
     */
    public function loadCredentials(): bool {
        $fileContents = file_get_contents(self::CONFIG);
        $jsonDBCredntials = json_decode( $fileContents );
        if( $jsonDBCredntials === null && json_last_error() !== JSON_ERROR_NONE){
            return false;
        }
        if (  !isset( $jsonDBCredntials->server ) || !isset( $jsonDBCredntials->db ) 
        && ( $jsonDBCredntials->server == '' || $jsonDBCredntials->dbname == '')  ){
            return false;
        }
        $this->server =  $jsonDBCredntials->server ;
        $this->db =  $jsonDBCredntials->dbname ;
        $this->username =  !isset( $jsonDBCredntials->username ) ?: 'root' ;
        $this->pass =  isset( $jsonDBCredntials->pass )  ? $jsonDBCredntials->pass: '' ;
        return true;
    }
    /**
     * Read DB credentials from config file
     * 
     */
    public function connect(): bool  {
        if( $this->server == ''){
            $this->loadCredentials();
        }
        try{

            $this->conn = new PDO("mysql:host=$this->server;dbname=$this->db", $this->username, $this->pass);

        } catch(PDOException $e ){
            
            return false;
        }
        
        return true;
    }
    /**
     * Read DB credentials from config file
     * 
     */
    public function close(): void  {
        $this->conn = null;
    }
    /**
     * Does table exist
     * 
     */
    public function tableExists( string $tableName): bool  {
        $sql = 'SHOW TABLES LIKE :tableName';
        $stmt = $this->conn->prepare( $sql );
        $stmt->bindParam(':tableName', $tableName );
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    /**
     * Create database schema
     * 
     */
    public function createSchema(): bool  {
        //Customer table SQL query
       $customer = "CREATE TABLE IF NOT EXISTS ".self::CUSTOMER."(
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(200) UNIQUE,
        name VARCHAR(150) NOT NULL
        ) ENGINE = MyISAM";
        //Product table SQL query
       $product = "CREATE TABLE IF NOT EXISTS ".self::PRODUCT."(
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(300) NOT NULL
        ) ENGINE = MyISAM";
        //Sale table SQL query
       $sale = "CREATE TABLE IF NOT EXISTS ".self::SALE."(
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        price NUMERIC(10,2) NOT NULL,
        version VARCHAR(20),
        customer_id BIGINT,
        product_id BIGINT,
        created_at TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE = InnoDB";
        
        try {
            //code...

            $this->tableExists(self::CUSTOMER) ?: $this->conn->exec($customer) ;
            $this->tableExists(self::PRODUCT) ?: $this->conn->exec($product) ;
            $this->tableExists(self::SALE) ?: $this->conn->exec($sale) ;

        } catch (PDOException $e) {
            var_dump( $e->getMessage() );
            exit;
            return false;
        }
        return true;
    }

    /**
     * Get database connection
     * 
     */
    public function getConn(): PDO  {

        if( !$this->conn ){
            $this->loadCredentials();
            $this->connect();
        }
         return $this->conn;
    }
}