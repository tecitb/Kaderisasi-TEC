<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

$app->post('/generateCoupon/{num}', function (Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $number_of_coupons = $args["num"];
 if (filter_var($number_of_coupons, FILTER_VALIDATE_INT) === FALSE) {
   $error = ['error' => ['text' => 'Invalid number of coupon']];
   return $response->withJson($error);
 }

 if($number_of_coupons > 50 || $number_of_coupons < 1) {
   $error = ['error' => ['text' => 'Enter number between 1 and 50 only']];
   return $response->withJson($error);
 }

 require_once dirname(__DIR__) . '/class.coupon.php';
 $sql = "INSERT INTO `coupons`(`coupon`) VALUES ";
 $pieces = [];
 for ($i=0; $i < $number_of_coupons; $i++) { 
   $pieces[] = "('" . coupon::generate(8) . "')";
 }
 $sql .= implode(',', $pieces);

 try {
   $db = $this->get('db');
   $stmt = $db->query($sql);
   $row_affected = $stmt->rowCount();
   if ($row_affected == $number_of_coupons) {
     $success = ["notice"=>["type"=>"success", "text" => "$row_affected coupons sucessfully added"]];
     return $response->withJson($success);
   }
   else {
     $warning = ["notice"=>["type"=>"warning", "text" => "Only $row_affected coupons sucessfully added"]];
     return $response->withJson($warning);
   }
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }
});