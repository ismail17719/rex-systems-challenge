<?php
namespace Dal;

use PDO;

class DataManager {
    /**
     * DB file path
     * @var string
     */
    protected const DATA = __DIR__.'/../data.json';
    
    /**
     * Customer table
     * @var PDO
     */
    protected $conn;
    
    function __construct(PDO $conn) {
        $this->conn = $conn;
    }
    /**
     * Load data to the database
     * 
     */
    public function loadData(): bool {
         
        $sql = 'SELECT * FROM customers';
        $stmt = $this->conn->prepare( $sql );
        $stmt->execute();
        if( $stmt->rowCount() <= 0 ){
            $fileContents = file_get_contents(self::DATA);
            $sales = json_decode( $fileContents );
            if( $sales === null && json_last_error() !== JSON_ERROR_NONE){
                return false;
            }
            foreach ($sales as $sale) {
                # insert customer
                if( !$this->customerExists( $sale->customer_mail )){
                    $customerInsert = 'INSERT INTO '.DBManager::CUSTOMER.' (email, name) VALUES (:email, :name)';
                    $stmtC = $this->conn->prepare( $customerInsert );
                    $stmtC->bindParam(':email', $sale->customer_mail);
                    $stmtC->bindParam(':name', $sale->customer_name);
                    $stmtC->execute();
                    $customerID = $this->conn->lastInsertId();
                }

                # insert product
                if( !$this->productExists( $sale->product_id ) ){

                    $productInsert = "INSERT INTO ".DBManager::PRODUCT."(id,name) VALUES (:id,:name)";
                    $stmtP = $this->conn->prepare( $productInsert );
                    $stmtP->bindParam(':id', $sale->product_id);
                    $stmtP->bindParam(':name', $sale->product_name);
                    
                    $stmtP->execute();

                }
                
                # insert sale record
                $saleInsert = "INSERT INTO ".DBManager::SALE." (id,price,version,customer_id, product_id,created_at) VALUES (?,?,?,?,?,?)";
                $stmtS = $this->conn->prepare( $saleInsert );
                $stmtS->execute([$sale->sale_id, $sale->product_price, $sale->version, $customerID, $sale->product_id, $sale->sale_date]);
            }
        }
       
        return true;
    }
    /**
     * Get all customers
     * 
     */
    public function getCustomers(): array {
         
        $sql = "SELECT * FROM ".DBManager::CUSTOMER;
        $stmt = $this->conn->query( $sql );
        return $stmt->fetchAll(PDO::FETCH_OBJ);;
       
    }
    /**
     * Filter customers
     * 
     */
    public function getCustomerById($customerID): mixed {
         
        $sql = "SELECT * FROM ".DBManager::CUSTOMER." WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$customerID]);
        return  $stmt->fetch(PDO::FETCH_OBJ) ;
       
    }
    /**
     * Filter customers
     * 
     */
    public function customerExists($email): bool {
         
        $sql = "SELECT * FROM ".DBManager::CUSTOMER." WHERE email=:email";
        $stmt = $this->conn->prepare( $sql );
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0 ;
       
    }
    /**
     * Get all records
     * 
     */
    public function getProducts(): array {
         
        $sql = "SELECT * FROM ".DBManager::PRODUCT;
        $stmt = $this->conn->query( $sql );
        return $stmt->fetchAll(PDO::FETCH_OBJ);;
       
    }
    /**
     * Filter customers
     * 
     */
    public function productExists($id): bool {
         
        $sql = "SELECT * FROM ".DBManager::PRODUCT." WHERE id=:id";
        $stmt = $this->conn->prepare( $sql );
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0 ;
       
    }
    /**
     * Filter products
     * 
     */
    public function getProductById($productID): mixed {
         
        $sql = "SELECT * FROM ".DBManager::PRODUCT." WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$productID]);
        return $stmt->fetch(PDO::FETCH_OBJ);
       
    }
    /**
     * Get all sales
     * 
     */
    public function getSales(array $filter = [] ): array {
         
        $sql = "SELECT * FROM ".DBManager::SALE;
        $first = true;
        foreach ($filter as $col => $value) {
            # code...
            if( $first ){
                $sql .= " WHERE $col = ".$value;
                $first = false;
            } else {
                $sql .= " AND WHERE $col = ".$value;
            }
        }
        $stmt = $this->conn->query( $sql );
        return $stmt->fetchAll(PDO::FETCH_OBJ);
       
    }
    /**
     * Get all prices
     * 
     */
    public function getPrices(): array {
         
        $sql = "SELECT DISTINCT(price) FROM ".DBManager::SALE;
       
        $stmt = $this->conn->query( $sql );
        return $stmt->fetchAll(PDO::FETCH_OBJ);
       
    }
    
    
}