<?php
        
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;



if(isset($_POST["RD"])){
    switch ($_POST['RD']) {
        case 'SAP':
            addProduct();
            break;
            case 'EP':
                EditProduct();
                break;
                
            }
        }
    
    else{
$_POST = json_decode(file_get_contents('php://input'), true);
    

    if(isset($_POST["functionName"])){
    $_POST["functionName"]();
     }
     else if(isset($_GET["fN"])){
        // echo json_encode(["one"=>"two"]);
        $_GET["fN"]();
         }
    }







function addProduct(){

    require('Database.php');
    session_start();
    $img_name = $_FILES['img']['name'];
    $tmp_name = $_FILES['img']['tmp_name'];
     $new_img_name;
    $img_extension = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));

    $new_img_name = uniqid("IMG-", true).'.'.$img_extension;
	$img_upload_path = '../Media/ProductImages/'.$new_img_name;
    move_uploaded_file($tmp_name, $img_upload_path);

    $sql = "INSERT INTO product (p_name,p_image,p_description,p_price,p_qty,b_id,cat_id,s_id)
    VALUES ('".$_POST["name"]."','".$new_img_name."','".$_POST["desc"]."','".$_POST["price"]."','".$_POST["qty"]."','".$_POST["brand"]."','".$_POST["category"]."','".$_SESSION["id"]."');";
    $result = $conn->query($sql);
    $conn=null;
    header("Location: ../Seller/SellerPage/sellerhome.html");
}


function getBrandsAndCategories(){
    require('Database.php');

    $brands=[];
    $categories=[];

    $sql = "SELECT * FROM brand ";
    $result = $conn->query($sql);
    
    while($row = $result->fetch()){

        $brands[$row["b_id"]]=$row["b_name"];   
    }
    $sql = "SELECT * FROM category ";
    $result = $conn->query($sql);
    
    while($row = $result->fetch()){

        $categories[$row["cat_id"]]=$row["cat_name"];   
    }
    $conn=null;
    echo  json_encode(['categories' => $categories,'brands' => $brands]);
}

function GPBC(){  // Get Products BY Criteria (get method)
    require('Database.php');
    if($_GET["direction"]=="asc"){
        $direction="ASC";
    }
    else{
        $direction="DESC";

    }

    $products=[];
    if($_GET["sort"]=="price"){
        // echo json_encode(["1"=>"2"]);
        $num=1;
       $sql = "SELECT * FROM product p JOIN category c ON p.cat_id=c.cat_id WHERE c.cat_name ='".$_GET["cat"]."' ORDER BY p_price ".$direction.";";
    }
    else if($_GET["sort"]=="ratings"){
        // echo json_encode(["3"=>"4"]);
        $num=2;
        $sql ="SELECT * FROM product p JOIN category c ON p.cat_id=c.cat_id WHERE c.cat_name ='".$_GET["cat"]."' ORDER BY p_rating ".$direction.";";
    }
    else if($_GET["sort"]=="date"){
        // echo json_encode(["5"=>"6"]);
        $num=3;
        $sql = "SELECT * FROM product p JOIN category c ON p.cat_id=c.cat_id WHERE c.cat_name ='".$_GET["cat"]."' ORDER BY p_id ".$direction.";";
    }
    
    
    $result = $conn->query($sql);
    $i=1;
    while($row = $result->fetch()){

        $products[$i++] = ["id"=>$row["p_id"],"name"=>$row["p_name"],"image"=>$row["p_image"],"rate"=>$row["p_rating"],"price"=>$row["p_price"]];   
    }

    echo json_encode(["products"=>$products ,"sort"=>$num,"dir"=>$direction]);
}

function SP(){ //Search Product
    require('Database.php');
    if($_GET["direction"]=="asc"){
        $direction="ASC";
    }
    else{
        $direction="DESC";

    }

    $products=[];
    if($_GET["sort"]=="price"){
        // echo json_encode(["1"=>"2"]);
        $num=1;
       $sql = "SELECT * FROM product WHERE p_name LIKE '%".$_GET["search"]."%' ORDER BY p_price ".$direction.";";
    }
    else if($_GET["sort"]=="ratings"){
        // echo json_encode(["3"=>"4"]);
        $num=2;
        $sql ="SELECT * FROM product WHERE p_name LIKE '%".$_GET["search"]."%' ORDER BY p_rating ".$direction.";";
    }
    else if($_GET["sort"]=="date"){
        // echo json_encode(["5"=>"6"]);
        $num=3;
        $sql = "SELECT * FROM product WHERE p_name LIKE '%".$_GET["search"]."%' ORDER BY p_id ".$direction.";";
    }
    
    
    $result = $conn->query($sql);
    $i=1;
    while($row = $result->fetch()){

        $products[$i++] = ["id"=>$row["p_id"],"name"=>$row["p_name"],"image"=>$row["p_image"],"rate"=>$row["p_rating"],"price"=>$row["p_price"]];   
    }

    echo json_encode(["products"=>$products ,"sort"=>$num,"dir"=>$direction]);
}

function getProductsOfSeller(){
    require('Database.php');
    session_start();
    if($_POST["direction"]=="asc"){
        $direction="ASC";
    }
    else{
        $direction="DESC";

    }

    $products=[];
    if($_POST["sort"]=="price"){
        // echo json_encode(["1"=>"2"]);
        $num=1;
       $sql = "SELECT * FROM product WHERE s_id = '".$_SESSION["id"]."' ORDER BY p_price ".$direction.";";
    }
    else if($_POST["sort"]=="ratings"){
        // echo json_encode(["3"=>"4"]);
        $num=2;
        $sql = "SELECT * FROM product WHERE s_id = '".$_SESSION["id"]."' ORDER BY p_rating ".$direction.";";
    }
    else if($_POST["sort"]=="date"){
        // echo json_encode(["5"=>"6"]);
        $num=3;
        $sql = "SELECT * FROM product WHERE s_id = '".$_SESSION["id"]."' ORDER BY p_id ".$direction.";";
    }
    
    
    $result = $conn->query($sql);
    $i=1;
    while($row = $result->fetch()){

        $products[$i++] = ["id"=>$row["p_id"],"name"=>$row["p_name"],"image"=>$row["p_image"],"rate"=>$row["p_rating"],"price"=>$row["p_price"]];   
    }

    echo json_encode(["products"=>$products ,"sort"=>$num,"dir"=>$direction]);
}

function getProductSimilarsById(){
    require('Database.php');

        $sql_0 = "SELECT * FROM product WHERE p_id = '".$_POST["productId"]."';";
        $result_0 = $conn->query($sql_0);
        $row_0 = $result_0->fetch();

       $sql = "SELECT * FROM product p  JOIN category c ON p.cat_id = c.cat_id WHERE p.cat_id = '".$row_0["cat_id"]."';";

    
    $result = $conn->query($sql);
    $i=0;
    $products =[];
    while($row = $result->fetch()){

        $products[$i++] = ["id"=>$row["p_id"],"name"=>$row["p_name"],"image"=>$row["p_image"],"rate"=>$row["p_rating"],"price"=>$row["p_price"]];   
 
    }

    echo json_encode(["products"=>$products ]);
}

function getBestProducts(){
    require('Database.php');

    $sql_0 = "SELECT p_id, COUNT(*) AS count
    FROM orderedproducts
    GROUP BY p_id
    ORDER BY count DESC;";
    $result_0 = $conn->query($sql_0);
    
   
    $i=0;
    $products =[];
    while($row_0 = $result_0->fetch()){

    $sql = "SELECT * FROM product p WHERE p_id = '".$row_0["p_id"]."';";
    $result = $conn->query($sql);
    $row = $result->fetch();

    $products[$i++] = ["count"=>$row_0["count"],"id"=>$row["p_id"],"name"=>$row["p_name"],"image"=>$row["p_image"],"rate"=>$row["p_rating"],"price"=>$row["p_price"]];   

}

echo json_encode(["products"=>$products ]);
}

function getProductReviewsById(){
    require('Database.php');

    

       $sql = "SELECT * FROM rates r JOIN customer c ON r.c_id = c.c_id WHERE r.p_id = '".$_POST["productId"]."';";

    
    $result = $conn->query($sql);
    $i=0;
    $products=[];
    while($row = $result->fetch()){

        $products[$i++] = ["name"=>$row["c_name"],"rate"=>$row["r_rating"],"comment"=>$row["r_comment"]];   
    }

    echo json_encode(["products"=>$products ]);
}

function getProductDataById(){
    require('Database.php');
    session_start();
    

       $sql = "SELECT * FROM product WHERE p_id = '".$_POST["productId"]."';";
    
    
    $result = $conn->query($sql);
    $products =[];
    if($row = $result->fetch()){

        $products[0] = ["id"=>$row["p_id"],"name"=>$row["p_name"],"image"=>$row["p_image"],"brand"=>$row["b_id"],"category"=>$row["cat_id"],"qty"=>$row["p_qty"],"price"=>$row["p_price"],"desc"=>$row["p_description"],"rate"=>$row["p_rating"]];   
    }

    echo json_encode(["products"=>$products ]);
}

function EditProduct(){
    require('Database.php');

    if(isset($_POST["delete"])){
        unlink("../Media/ProductImages/".basename($_POST["old_img_path"]));
        $sql = "DELETE FROM rates  WHERE p_id = '".$_POST["id"]."' ";
        $result = $conn->query($sql);
        $sql = "DELETE FROM cart  WHERE p_id = '".$_POST["id"]."' ";
        $result = $conn->query($sql);
        $sql = "DELETE FROM orderedproducts  WHERE p_id = '".$_POST["id"]."' ";
        $result = $conn->query($sql);
        $sql = "DELETE FROM product  WHERE p_id = '".$_POST["id"]."' ";
        $result = $conn->query($sql);
    
        $row = $result->fetch();
    }
    else{
        echo json_encode([$_FILES]);
    echo json_encode(["done"=>var_dump(isset($Files["img"]))]);
    // echo json_encode(["d"=>var_dump($_POST["img"]=="")]);

    if(isset($_FILES['img']) && $_FILES['img']['name']!=""){

        $img_name = $_FILES['img']['name'];
        $tmp_name = $_FILES['img']['tmp_name'];
        
        $img_extension = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
    
        $new_img_name = uniqid("IMG-", true).'.'.$img_extension;
        $img_upload_path = '../Media/ProductImages/'.$new_img_name;
        move_uploaded_file($tmp_name, $img_upload_path);
        unlink("../Media/ProductImages/".basename($_POST["old_img_path"]));
        $imageUpdate = "p_image='".$new_img_name."' ,";
        echo json_encode(["done"=>"true"]);
    }
    else {
        $imageUpdate=" ";
        echo json_encode(["done"=>"false"]);

    }

    $sql = "UPDATE product SET ".$imageUpdate." p_name='".$_POST["name"]."' , p_price='".$_POST["price"]."' , p_description = '".$_POST["desc"]."' , p_qty='".$_POST["qty"]."' , b_id='".$_POST["brand"]."' , cat_id='".$_POST["category"]."'  WHERE p_id = '".$_POST["id"]."' ";
    $result = $conn->query($sql);
    
    $row = $result->fetch();
    }

    $conn=null;

    header("Location: ../Seller/ManageProductsSeller/index.html");
}




function addToCart(){
    require('Database.php');
    session_start();
    
    if(isset($_SESSION["id"])){

        $sql_0 = "SELECT * FROM cart WHERE c_id = '".$_SESSION["id"]."' AND p_id= '".$_POST["productId"]."' ;";
        
        $result_0 = $conn->query($sql_0);


        if($row_0 = $result_0->fetch()){
            $sql = "UPDATE cart SET  cart_qty='".$row_0["cart_qty"] + $_POST["qty"]."' WHERE  c_id = '".$_SESSION["id"]."' AND p_id ='".$_POST["productId"]."' ";

        }
        else{

            $sql = "INSERT INTO cart (c_id,p_id,cart_qty) VALUES ('".$_SESSION["id"]."' , '".$_POST["productId"]."' , '".$_POST["qty"]."') ;";
        }
    
    
        $result = $conn->query($sql);
       
    }

     

    $conn=null;
}


function getCart(){
    require('Database.php');
    session_start();
    

        $sql = "SELECT * FROM cart WHERE c_id = '".$_SESSION["id"]."';";
        $result = $conn->query($sql);
    $i=0;
    $products=[];
    while($row = $result->fetch()){
        $sql_2 = "SELECT * FROM product WHERE p_id = '".$row["p_id"]."';";
        $result_2 = $conn->query($sql_2);
        $row_2 = $result_2->fetch();
        $products[$i++] = ["id"=>$row["p_id"],"qty"=>$row["cart_qty"],"price"=>$row_2["p_price"],"image"=>$row_2["p_image"],"name"=>$row_2["p_name"],"max_qty"=>$row_2["p_qty"]];   
    }

    echo json_encode(["products"=>$products ]);
}
function updateCartQty(){
    require('Database.php');
    session_start();
    

    $sql = "UPDATE cart SET  cart_qty='".$_POST["qty"]."' WHERE  c_id = '".$_SESSION["id"]."' AND p_id ='".$_POST["productId"]."' ";

        $result = $conn->query($sql);
   
    echo json_encode(["state"=>"success" ]);
}
function deleteFromCart(){
    require('Database.php');
    session_start();
    

    $sql = "DELETE FROM cart  WHERE   c_id = '".$_SESSION["id"]."' AND p_id ='".$_POST["productId"]."' ";

        $result = $conn->query($sql);
   
    echo json_encode(["state"=>"success" ]);
}

function removeAddress(){
    require('Database.php');
    
    $sql = "DELETE FROM billingaddress WHERE  ba_id='".$_POST["id"]."'";


        $result = $conn->query($sql);
        

}

function updateAddress(){
    require('Database.php');
    
    $sql = "UPDATE billingaddress SET  ba_city='".$_POST["city"]."',ba_street='".$_POST["street"]."',ba_buildingNumber='".$_POST["building"]."' ,ba_floor='".$_POST["floor"]."',ba_apartmentNumber='".$_POST["apartment"]."' WHERE  ba_id = '".$_POST["id"]."'";


        $result = $conn->query($sql);
        

}

function getAddresses(){
    require('Database.php');
    session_start();
    

    $sql = "SELECT * FROM billingaddress WHERE c_id = '".$_SESSION["id"]."';";


        $result = $conn->query($sql);
        $address=[];
        $i=0;
        while($row = $result->fetch()){
           
            $address[$row["ba_id"]] = ["city"=>$row["ba_city"],"street"=>$row["ba_street"],"building"=>$row["ba_buildingNumber"],"floor"=>$row["ba_floor"],"apartment"=>$row["ba_apartmentNumber"]];   
        }

    echo json_encode(["address"=>$address ]);
}
function addAddress(){
    require('Database.php');
    session_start();
    

    $sql = "INSERT INTO billingaddress (c_id,ba_city,ba_street,ba_buildingNumber,ba_floor,ba_apartmentNumber) VALUES ('".$_SESSION["id"]."','".$_POST["city"]."','".$_POST["street"]."','".$_POST["building"]."','".$_POST["floor"]."','".$_POST["apartment"]."')";


        $result = $conn->query($sql);
   
        
}

function makeOrder(){
    require('Database.php');
    session_start();
    // echo json_encode(["id"=>"2"]);
    
    

    $products = $_POST["products"]["products"];
    // echo json_encode($_POST["products"]["products"][2]["id"]);

    $currentDateTime = new DateTime();
$formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');

    $sql = "INSERT INTO orders (c_id,o_date,ba_id,o_totalPrice) VALUES ('".$_SESSION["id"]."','".$formattedDateTime."','".$_POST["address"]."','".$_POST["total"]."')";
     $result = $conn->query($sql);
     $newRecordId= $conn->lastInsertId();
    
     $sql = "DELETE FROM cart  WHERE   c_id = '".$_SESSION["id"]."'  ";

        $result = $conn->query($sql);



     for ($i=0; $i < count($products) ; $i++) { 
        // echo json_encode(["test"=>$newRecordId]);
        
        $sql = "INSERT INTO orderedproducts (o_id,p_id,op_qty) VALUES ('".$newRecordId."','".$products[$i]["id"]."','".$products[$i]["qty"]."')";
        $result = $conn->query($sql);

        $sql = "UPDATE product SET p_qty = '".($products[$i]["max_qty"]-$products[$i]["qty"])."' WHERE  p_id = '".$products[$i]["id"]."'";
        $result = $conn->query($sql);

     }

        echo json_encode(["id"=>$newRecordId]);
        exit;
}

function getOrderDetails(){
    require('Database.php');
    
    
    $sql = "SELECT * FROM orders WHERE o_id = '".$_POST["id"]."';";
    
    $result = $conn->query($sql);
    $row = $result->fetch();

    $sql_2 = "SELECT * FROM orderedproducts WHERE o_id = '".$_POST["id"]."';";
    
    $result_2 = $conn->query($sql_2);
    
    $orderedItems = [];

    while($row_2 = $result_2->fetch()){
        $sql_3 = "SELECT * FROM product WHERE p_id = '".$row_2["p_id"]."';";
        $result_3 = $conn->query($sql_3);
        $row_3 = $result_3->fetch();
        $orderedItems[$row_2["p_id"]] = ["name"=>$row_3["p_name"],"image"=>$row_3["p_image"],"qty"=>$row_2["op_qty"],"price"=>$row_3["p_price"]];   
    } 
    // kamel al data mn al products
   
    echo json_encode(["orderTotal"=>$row["o_totalPrice"],"orderDate"=>$row["o_date"],"products"=>$orderedItems]);

}

function getOrders(){
    require('Database.php');
    session_start();
    
    $sql = "SELECT * FROM orders WHERE c_id = '".$_SESSION["id"]."';";
    
    $result = $conn->query($sql);
    
    
    
    $orders = [];

    while($row = $result->fetch()){
        $orders[$row["o_id"]] = ["date"=>$row["o_date"],"price"=>$row["o_totalPrice"]];   
    } 
    // kamel al data mn al products
   
    echo json_encode(["orders"=>$orders]);

}

function checkOrderValidation(){
    require('Database.php');
    session_start();
    
    $sql = "SELECT * FROM orders WHERE c_id = '".$_SESSION["id"]."' ;";
    
    $result = $conn->query($sql);
    
    
    
    $state=false ;

    while($row = $result->fetch()){
            
        if($_POST["id"] ==  $row["o_id"])
        {
            
            $state=true;
        }
    } 
   
    echo json_encode(["state"=>$state]);
}

function checkEditProductValidation(){
    require('Database.php');
    session_start();
    
    $sql = "SELECT * FROM product WHERE p_id = '".$_POST["id"]."' AND s_id = '".$_SESSION["id"]."'  ;";
    
    $result = $conn->query($sql);
    
    
    
    $state=false ;

    if($row = $result->fetch()){
            
       
            
            $state=true;
        
    } 
   
    echo json_encode(["state"=>$state]);
}

function checkOrderSellerValidation(){
    require('Database.php');
    session_start();
    
    $sql = "SELECT * FROM orders WHERE o_id = '".$_POST["id"]."' ;";
    
    $result = $conn->query($sql);

    $state = false;

    while($row = $result->fetch())
    {
        $sql_2 = "SELECT * FROM orderedproducts WHERE o_id = '".$row["o_id"]."' ;";
    
        $result_2 = $conn->query($sql_2);

        while($row_2=$result_2->fetch()){
            
            $sql_3 = "SELECT * FROM product WHERE p_id = '".$row_2["p_id"]."' AND s_id = '".$_SESSION["id"]."' ;";
            
            $result_3 = $conn->query($sql_3);
            
            while($row_3 = $result_3->fetch()){

                
                    $state=true;
                }
                

        }


    }
    echo json_encode(["state"=>$state]);

}
function sendReview(){
    require('Database.php');
    session_start();
    
    $sql = "INSERT INTO rates  (c_id,p_id,r_rating,r_comment) VALUES ('".$_SESSION["id"]."','".$_POST["id"]."','".$_POST["rate"]."','".$_POST["comment"]."') ;";
    
    $result = $conn->query($sql);
    
    
}

function checkReview(){
    require('Database.php');
    session_start();
    
    $state=false; //msh hybynhalo
    
    if(isset($_SESSION["id"])){
        $sql = "SELECT * FROM rates WHERE c_id = '".$_SESSION["id"]."' AND p_id = '".$_POST["id"]."' ;";
    
    $result = $conn->query($sql);
    

    if($row = $result->fetch()){
        $state = false;
    }
    else{

        $sql_2 = "SELECT * FROM orders WHERE c_id = '".$_SESSION["id"]."'  ;";
    
        $result_2 = $conn->query($sql_2);

        while($row_2 = $result_2->fetch()){

            // $sql_2 = "SELECT * FROM orders WHERE c_id = '".$_SESSION["id"]."'  ;";
    
            // $result_2 = $conn->query($sql_2);

            

                $sql_3 = "SELECT * FROM orderedproducts WHERE o_id = '".$row_2["o_id"]."' AND p_id ='".$_POST["id"]."' ;";

                $result_3 = $conn->query($sql_3);

                while($row_3 = $result_3->fetch()){
                
                    $state=true;

                }


            
        }

    }
    }

    echo json_encode(["state"=>$state]);
}

function checkFeedbackValidation(){
    require('Database.php');
    
    $sql = "SELECT * FROM feedback;";
    
    $result = $conn->query($sql);
    
    
    
    $state=false ;

    while($row = $result->fetch()){
            
        if($_POST["id"] ==  $row["f_id"])
        {
            
            $state=true;
        }
    } 
   
    echo json_encode(["state"=>$state]);
}
function getFeedbacks (){

    require('Database.php');
    session_start();
    
    $sql = "SELECT * FROM feedback;";
    
    $result = $conn->query($sql);
    
    
    
    $feedbacks = [];

    while($row = $result->fetch()){
        $feedbacks[$row["f_id"]] = ["date"=>$row["f_date"],"message"=>$row["f_message"],"email"=>$row["f_email"]];   
    } 
   
    echo json_encode(["feedbacks"=>$feedbacks]);


}

function sendFeedbackReply(){
    require('Database.php');

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    $sql_0 = "SELECT * FROM feedback WHERE f_id = '".$_POST["id"]."';";
    $result_0 = $conn->query($sql_0);
    $row_0 = $result_0->fetch();

    $mail = new PHPMailer(true);

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = "coursesoverflow2023@gmail.com"; //gmail
    $mail->Password = "rcimgxvdideooazv"; //gmail app password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('coursesoverflow2023@gmail.com'); //gmail

    $mail->addAddress($row_0["f_email"]); //to who

    $mail->isHTML(true);

    $mail->Subject = "Response on Your Feedback";
    $mail->Body = $_POST["reply"];

    $mail->send();

    $sql = "DELETE FROM feedback  WHERE   f_id = '".$_POST["id"]."'  ";
    $result = $conn->query($sql);
    
}

function dismissFeedback(){
    require('Database.php');


    
    $sql = "DELETE FROM feedback  WHERE   f_id = '".$_POST["id"]."'  ";
    $result = $conn->query($sql);

}


function getOrderDetailsSeller(){
    require('Database.php');
    session_start();
    
    $sql = "SELECT * FROM orders ;";
    
    $result = $conn->query($sql);

    $sellerOrders = [];

    while($row = $result->fetch())
    {
        $sql_2 = "SELECT * FROM orderedproducts WHERE o_id = '".$row["o_id"]."' ;";
    
        $result_2 = $conn->query($sql_2);

        while($row_2=$result_2->fetch()){
            
            $sql_3 = "SELECT * FROM product WHERE p_id = '".$row_2["p_id"]."' AND s_id = '".$_SESSION["id"]."' ;";
            
            $result_3 = $conn->query($sql_3);
            
            while($row_3 = $result_3->fetch()){

                
                    $sellerOrders[$row["o_id"]][] = ["image"=>$row_3["p_image"],"name"=>$row_3["p_name"],"qty"=>$row_2["op_qty"],"price"=>$row_3["p_price"],"date"=>$row["o_date"]];
                }
                

        }


    }
    
    echo json_encode(["sellerOrders"=>$sellerOrders]);






}





?>