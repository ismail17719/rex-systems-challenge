<?php 
require_once 'Dal/DataManager.php';
require_once 'Dal/DBManager.php';
require_once 'Services/TimeZoneService.php';
use Dal\DBManager;
use Dal\DataManager;
use Services\TimeZoneService;
$dbManager = new DBManager;
$filteredCustomerID = $_GET['customer']  ?? 0;
$filteredProductID = $_GET['product'] ?? 0;
$filteredPrice = $_GET['price'] ?? -1;

$filter = array_merge( 
    ($filteredCustomerID != 0 && $filteredCustomerID != '' ? ['customer_id' => $filteredCustomerID] : []),
    ($filteredProductID != 0  && $filteredProductID != '' ? ['product_id' => $filteredProductID] : []),
    ( $filteredPrice != -1   && $filteredPrice != '' ? ['price' => $filteredPrice] : [] )
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Coding Challenge - REX Systems</title>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
  <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
  </symbol>
  <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
</svg>

    <div class="container">
        <?php
        if( !$dbManager->loadCredentials() ){
        ?>
            <div class="row justify-content-center align-items-center g-2">
                <div class="col">
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                        <div>
                            Database credentials problem. Please check the config.json file to make sure database credentials are OK.
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        
        
        if( !$dbManager->connect() ){
        ?>
            <div class="row justify-content-center align-items-center g-2">
                <div class="col">
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                        <div>
                            Couldn't connect to database. Please check the credentials.
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        if( !$dbManager->createSchema() ){
        ?>
            <div class="row justify-content-center align-items-center g-2">
                <div class="col">
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                        <div>
                            Couldn't create tables.
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
        <?php
        $dataManager = new DataManager( $dbManager->getConn() );
        $dataManager->loadData();
        $customers = $dataManager->getCustomers();
        $products = $dataManager->getProducts();
        $prices = $dataManager->getPrices();
        $sales = $dataManager->getSales($filter);
        ?>
        <div class="card mt-5">
            
            <div class="card-body">
                <div class="row justify-content-center align-items-center g-2">
                    <div class="col">
                        <form action="" method="get">
                            <select class="form-select" name="product" select-type='filter'>
                                <option value="">Filter by product</option>
                                <?php 
                                    foreach ($products as $product) {
                                        # code...
                                        ?>
                                        <option <?php echo $filteredProductID== $product->id ? 'selected' : '' ?>  value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
                                        <?php
                                    }
                                ?>
                                
                            </select>
                        </form>
                    </div>
                    <div class="col">
                        <form action="" method="get">
                            <select class="form-select" name="customer" select-type='filter'>
                                <option value="">Filter by customer</option>
                                <?php 
                                    foreach ($customers as $customer) {
                                        # code...
                                        ?>
                                        <option <?php echo $filteredCustomerID == $customer->id ? 'selected' : '' ?>  value="<?php echo $customer->id; ?>"><?php echo $customer->name; ?></option>
                                        <?php
                                    }
                                ?>
                                
                            </select>
                        </form>
                    </div>
                    
                    <div class="col">
                        <form action="" method="get">
                            <select class="form-select" name="price" select-type='filter'>
                                <option value="">Filter by price</option>
                                <?php 
                                    foreach ($prices as $price) {
                                        # code...
                                        ?>
                                        <option <?php echo $filteredPrice == $price->price ? 'selected' : '' ?>  value="<?php echo $price->price; ?>"><?php echo $price->price; ?></option>
                                        <?php
                                    }
                                ?>
                                
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="card mt-2">
            
            <div class="card-body">
                <div class="row justify-content-center align-items-center g-2">
                    <div class="col">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Version</th>
                                    <th scope="col">Sale Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $serial = 1;
                                $totalPrice = 0;
                                foreach ($sales as $sale) {
                                    # code...
                                    ?>
                                    <tr>
                                        <th scope="row"><?php echo $serial; ?></th>
                                        <td>
                                            <?php 
                                                $product = $dataManager->getProductById(  $sale->product_id);
                                                echo $product ? $product->name : '';
                                            ?>
                                        </td>
                                        <td><?php echo $sale->price; ?></td>
                                        <td>
                                            <?php 
                                                $customer = $dataManager->getCustomerById( $sale->customer_id);
                                                echo $customer ? $customer->name : '';
                                            ?>
                                        </td>
                                        <td><?php echo $sale->version; ?></td>
                                        <td><?php echo $sale->created_at.' ('. TimeZoneService::getTimeZone( $sale->version  ) .')'; ?> </td>
                                    </tr>
                                    <?php
                                    $serial++;
                                    $totalPrice += $sale->price;
                                }
                                ?>
                                
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" scope="col">Total</th>
                                    <th scope="col"><?php echo $totalPrice; ?></th>
                                    <th scope="col">&nbsp;</th>
                                    <th scope="col">&nbsp;</th>
                                    <th scope="col">&nbsp;</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/app.js"></script>
</body>
</html>