<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

//USER ADMIN GROUP

// GET ALL USERS
$app->get('/users', function(Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
     $error = ['error' => ['text' => 'Permission denied']];
     return $response->withJson($error);
  }

  $sql = "SELECT id, name, email, created_at, tec_regno, updated_at, lunas, isAdmin, is_active FROM users";

  $searchType = $request->getQueryParam("queryType");
  if(($searchType == null)||($searchType == "")){
    $sql .= " WHERE name LIKE :query";
  }else if($searchType == "name"){
    $sql .= " WHERE name LIKE :query";
  }else if($searchType == "email"){
    $sql .= " WHERE email LIKE :query";
  }else if($searchType == "tec_regno"){
    $sql .= " WHERE tec_regno LIKE :query";
  }else if($searchType == "line"){
    $sql .= " WHERE line_id LIKE :query";
  }else if($searchType == "phone"){
    $sql .= " WHERE mobile LIKE :query";
  }else if($searchType == "instagram"){
    $sql .= " WHERE instagram LIKE :query";
  }else{
    $error = ['error' => ['text' => 'invalid parameter (queryType)']];
    return $response->withJson($error);
  }

  $searchQuery = $request->getQueryParam("query");

  if(($searchQuery == null)||($searchQuery == "")){
    $searchQuery = "%%";
  }else{
    $searchQuery = "%".$searchQuery."%";
  }

  $sortby = $request->getQueryParam("sort");
  if(($sortby == null)||($sortby == "")){

  }else if($sortby == "noTEC_asc"){
    $sql .= " ORDER BY tec_regno ASC";
  }else if($sortby == "noTEC_desc"){
    $sql .= " ORDER BY tec_regno DESC";
  }else if($sortby == "nama_asc"){
    $sql .= " ORDER BY name ASC";
  }else if($sortby == "nama_desc"){
    $sql .= " ORDER BY name DESC";
  }else{
    $error = ['error' => ['text' => 'invalid parameter (sort)']];
    return $response->withJson($error);
  }

  $page = $request->getQueryParam("page");
  $number_per_items = $request->getQueryParam("items_per_page") ? (int) $request->getQueryParam("items_per_page") : 5;
  if (isset($page) && filter_var($page, FILTER_VALIDATE_INT)) {
    $sql .= " LIMIT :limit OFFSET :offset";
  }

  $totalCount = 1;

  try {
    $db = $this->get('db');
    if (isset($page) && filter_var($page, FILTER_VALIDATE_INT)) {

      $stmt = $db->prepare($sql);
      $stmt->bindValue(':limit', $number_per_items, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $number_per_items * ($page - 1), PDO::PARAM_INT);
      $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
      $stmt->execute();
    }
    else {
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
      $stmt->execute();

    }

    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }

  try {
    $db = $this->get('db');
    $sql = "SELECT COUNT(*) as jumlah FROM users WHERE name LIKE :query";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    $stmt->execute();

    $totalCount = $stmt->fetchAll();

    $db = null;
    return $response->withJson(["total"=>$totalCount[0]["jumlah"],"data"=>$users]);
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }
});

// GET ALL ACTIVE MEMBERS (Not admin and active)
$app->get('/members', function(Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
     $error = ['error' => ['text' => 'Permission denied']];
     return $response->withJson($error);
  }

  $sql = "SELECT id, tec_regno, name, email, created_at, updated_at, lunas FROM users WHERE is_active=1 AND isAdmin = 0";

  $searchType = $request->getQueryParam("queryType");
  if(($searchType == null)||($searchType == "")){
    $sql .= " AND name LIKE :query";
  }else if($searchType == "name"){
    $sql .= " AND name LIKE :query";
  }else if($searchType == "email"){
    $sql .= " AND email LIKE :query";
  }else if($searchType == "tec_regno"){
    $sql .= " AND tec_regno LIKE :query";
  }else if($searchType == "line"){
    $sql .= " AND line_id LIKE :query";
  }else if($searchType == "phone"){
    $sql .= " AND mobile LIKE :query";
  }else if($searchType == "instagram"){
    $sql .= " AND instagram LIKE :query";
  }else{
    $error = ['error' => ['text' => 'invalid parameter (queryType)']];
    return $response->withJson($error);
  }

  $sortby = $request->getQueryParam("sort");
  if(($sortby == null)||($sortby == "")){

  }else if($sortby == "noTEC_asc"){
    $sql .= " ORDER BY tec_regno ASC";
  }else if($sortby == "noTEC_desc"){
    $sql .= " ORDER BY tec_regno DESC";
  }else if($sortby == "nama_asc"){
    $sql .= " ORDER BY name ASC";
  }else if($sortby == "nama_desc"){
    $sql .= " ORDER BY name DESC";
  }else{
    $error = ['error' => ['text' => 'invalid parameter (sort)']];
    return $response->withJson($error);
  }

  $searchQuery = $request->getQueryParam("query");

  if(($searchQuery == null)||($searchQuery == "")){
    $searchQuery = "%%";
  }else{
    $searchQuery = "%".$searchQuery."%";
  }

  $page = $request->getQueryParam("page");
  $number_per_items = $request->getQueryParam("items_per_page") ? (int) $request->getQueryParam("items_per_page") : 5;
  if (isset($page) && filter_var($page, FILTER_VALIDATE_INT)) {
    $sql .= " LIMIT :limit OFFSET :offset";
  }

  try {
    $db = $this->get('db');
    if (isset($page) && filter_var($page, FILTER_VALIDATE_INT)) {
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':limit', $number_per_items, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $number_per_items * ($page - 1), PDO::PARAM_INT);
      $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
      $stmt->execute();
    }
    else {
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
      $stmt->execute();
    }

    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }

  try {
    $db = $this->get('db');
    $sql = "SELECT COUNT(*) as jumlah FROM users WHERE is_active=1 AND isAdmin=0 AND name LIKE :query";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    $stmt->execute();
    $totalCount = $stmt->fetchAll();

    $db = null;
    return $response->withJson(["total"=>$totalCount[0]["jumlah"],"data"=>$users]);
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }
});

// GET USER RESULT IN A QUIZ
$app->get('/user/{uid:[0-9]+}/quiz/{qid:[0-9]+}', function(Request $request, Response $response, array $args) {
   if ($request->getAttribute("jwt")['isAdmin'] != 1) {
     $error = ['error' => ['text' => 'Permission denied']];
     return $response->withJson($error);
   }

  $sql = "SELECT user_answer.answer, user_score.score as quiz_score, quiz.title as quiz_title, question, question_answer.type as question_type, question_answer.answer as correct_answer, decoy FROM user_answer INNER JOIN question_answer ON user_answer.qa_id = question_answer.id INNER JOIN users ON user_answer.user_id = users.id INNER JOIN quiz ON quiz.id = question_answer.quiz_id INNER JOIN user_score on user_score.quiz_id = quiz.id WHERE quiz.id = :qid AND users.id = :uid";
 try {
   $db = $this->get('db');

   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':uid' => $args['uid'],
     ':qid' => $args['qid']
   ]);
   $result = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
   return $response->withJson($result);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }
});

$app->put('/changepasswd/{user_id}', function(Request $request, Response $response, array $args) {
    if ($request->getAttribute("jwt")['isAdmin'] != 1) {
        $error = ['error' => ['text' => 'Permission denied']];
        return $response->withJson($error);
    }

    $user_id = $args['user_id'];
    $new_password = $request->getParam("new_password");
    try {
        $db = $this->get('db');
        $password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE `users` SET password = :password WHERE id = :uid");
        $stmt->execute([
            ':password' => $password,
            ':uid' => $user_id
        ]);

        $result = ["notice"=>["type"=>"success", "text" => "Password reset success"]];
        return $response->withJson($result);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

$app->get('/token/{uid}', function(Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
    $error = ['error' => ['text' => 'Permission denied']];
    return $response->withJson($error);
  }

  $uid = $args["uid"];
  $sql = "SELECT `user_id`, `resetToken` as `reset_token` FROM `user_reset` WHERE user_id = :uid";
  try {
      $db = $this->get('db');
      $stmt = $db->prepare($sql);
      $stmt->execute([
          ':uid' => $uid
      ]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $response->withJson($result);
  }
  catch (PDOException $e) {
      $error = ['error' => ['text' => $e->getMessage()]];
      return $response->withJson($error);
  }
});

// KEMBALIKAN MEMBER TEC, SET is_active ke 1
$app->post('/user/restore', function(Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $id = $request->getParam('uid');

 if (empty($id) || filter_var($id, FILTER_VALIDATE_INT) === FALSE) {
   die('Invalid ID');
 }

 try {
  $db = $this->get('db');

  $stmt = $db->prepare('UPDATE users SET is_active = 1 WHERE id = :id AND isAdmin = 0');
  $stmt->execute([':id' => $id]);

  $data = ["notice"=>["type"=>"success", "text" => "Member sukses dikembalikan"]];
  return $response->withJson($data);
 }
 catch (PDOException $exception) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

});



// COUPON ADMIN GROUP

// GET COUPONS
$app->get('/getCoupon/{num:[0-9]+}', function (Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
    $error = ['error' => ['text' => 'Permission denied']];
    return $response->withJson($error);
  }
  $couponType = $request->getQueryParam("type");

  if(($couponType == null)||($couponType == "")){
    $sql = "SELECT `coupon` FROM `coupons` WHERE `lunas`=1 ORDER BY `id` ASC LIMIT :couponNum";
  }else if($couponType == 0){
    $sql = "SELECT `coupon` FROM `coupons` WHERE `lunas`=0 ORDER BY `id` ASC LIMIT :couponNum";
  }else{
    $sql = "SELECT `coupon` FROM `coupons` WHERE `lunas`=1 ORDER BY `id` ASC LIMIT :couponNum";
  }

  try {
    $db = $this->get("db");

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':couponNum', (int)($args['num']), PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    return $response->withJson($result);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }
});

// CHANGE COUPONS
$app->post('/changeCoupon', function (Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
    $error = ['error' => ['text' => 'Permission denied']];
    return $response->withJson($error);
  }

  $type = $request->getParam('type');
  $cid = $request->getParam('cid');

  $sql = "UPDATE `coupons` SET `lunas` = :type WHERE `coupons`.`coupon` = :cid";

  try {
    $db = $this->get("db");

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':type', (int)($type), PDO::PARAM_INT);
    $stmt->bindValue(":cid", $cid ,PDO::PARAM_STR);
    $stmt->execute();
    $db = null;

    if($stmt->rowCount()>0){
      $success = ["notice"=>["type"=>"success"]];
      return $response->withJson($success);
    }else{
      $success = ["error"=>["text"=>"Kupon tidak valid atau sudah sesuai tipe"]];
      return $response->withJson($success);
    }


 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }
});

// DELETE COUPONS
$app->post('/deleteCoupon', function (Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
    $error = ['error' => ['text' => 'Permission denied']];
    return $response->withJson($error);
  }

  $cid = $request->getParam('cid');

  $sql = "DELETE FROM `coupons` WHERE `coupons`.`coupon` = :cid";

  try {
    $db = $this->get("db");

    $stmt = $db->prepare($sql);
    $stmt->bindValue(":cid", $cid ,PDO::PARAM_STR);
    $stmt->execute();
    $db = null;
    $success = ["notice"=>["type"=>"success"]];
    return $response->withJson($success);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }
});

// GENERATE COUPON
$app->post('/generateCoupon/{num:[0-9]+}', function (Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $number_of_coupons = $args["num"];

 if($number_of_coupons > 50 || $number_of_coupons < 1) {
   $error = ['error' => ['text' => 'Enter number between 1 and 50 only']];
   return $response->withJson($error);
 }

 require_once dirname(__DIR__) . '/class.coupon.php';

 $couponType = $request->getQueryParam("type");

 $sql = "INSERT INTO `coupons`(`coupon`,`lunas`) VALUES ";
 $pieces = [];
 for ($i=0; $i < $number_of_coupons; $i++) {
   if(($couponType == null)||($couponType == "")){
     $pieces[] = "('" . coupon::generate(8) . "',1)";
   }else if($couponType == 0){
     $pieces[] = "('" . coupon::generate(8) . "',0)";
   }else{
     $pieces[] = "('" . coupon::generate(8) . "',1)";
   }
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



// QUIZ ADMIN GROUP

// GET ALL QUIZ RESULT
$app->get('/quiz/{qid:[0-9]+}/score', function(Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
     $error = ['error' => ['text' => 'Permission denied']];
     return $response->withJson($error);
  }

    $sortby = $request->getQueryParam("sort");
  if(($sortby == null)||($sortby == "")){
    $sql = "SELECT `user_id`,`tec_regno`,`NIM`,`name`,`score` FROM `user_score` INNER JOIN `users` ON `user_score`.user_id = `users`.id WHERE quiz_id = :qid";
  }
  else if($sortby == "noTEC_asc"){
    $sql = "SELECT `user_id`,`tec_regno`,`NIM`,`name`,`score` FROM `user_score` INNER JOIN `users` ON `user_score`.user_id = `users`.id WHERE quiz_id = :qid ORDER BY `tec_regno` ASC";
  }
  else if($sortby == "noTEC_desc"){
     $sql = "SELECT `user_id`,`tec_regno`,`NIM`,`name`,`score` FROM `user_score` INNER JOIN `users` ON `user_score`.user_id = `users`.id WHERE quiz_id = :qid ORDER BY `tec_regno` DESC";
  }
  else if($sortby == "nama_asc"){
     $sql = "SELECT `user_id`,`tec_regno`,`NIM`,`name`,`score` FROM `user_score` INNER JOIN `users` ON `user_score`.user_id = `users`.id WHERE quiz_id = :qid ORDER BY `name` ASC";
  }
  else if($sortby == "nama_desc"){
     $sql = "SELECT `user_id`,`tec_regno`,`NIM`,`name`,`score` FROM `user_score` INNER JOIN `users` ON `user_score`.user_id = `users`.id WHERE quiz_id = :qid ORDER BY `name` DESC";
  }
  else if($sortby == "score_asc"){
     $sql = "SELECT `user_id`,`tec_regno`,`NIM`,`name`,`score` FROM `user_score` INNER JOIN `users` ON `user_score`.user_id = `users`.id WHERE quiz_id = :qid ORDER BY `score` ASC";
  }
  else if($sortby == "score_desc"){
     $sql = "SELECT `user_id`,`tec_regno`,`NIM`,`name`,`score` FROM `user_score` INNER JOIN `users` ON `user_score`.user_id = `users`.id WHERE quiz_id = :qid ORDER BY `score` DESC";
  }
  else{
    $error = ['error' => ['text' => 'invalid parameter']];
    return $response->withJson($error);
  }

  try {
    $db = $this->get('db');

    $page = $request->getQueryParam("page");
    $number_per_items = $request->getQueryParam("items_per_page") ? (int) $request->getQueryParam("items_per_page") : 5;
    if (isset($page)) {
      $sql .= " LIMIT :limit OFFSET :offset";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':limit', $number_per_items, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $number_per_items * ($page - 1), PDO::PARAM_INT);
    }else{
      $stmt = $db->prepare($sql);
    }

    $stmt->bindValue(":qid",$args["qid"],PDO::PARAM_INT);
    $stmt->execute();
    $quizes = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }

  try {
    $db = $this->get('db');
    $sql = "SELECT COUNT(*) as jumlah FROM `user_score` WHERE quiz_id = :qid";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':qid',$args['qid'],PDO::PARAM_INT);
    $stmt->execute();
    $totalCount = $stmt->fetchAll();

    $db = null;
    return $response->withJson(["total"=>$totalCount[0]["jumlah"],"data"=>$quizes]);
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }
});

// CREATE A QUIZ
$app->post('/quiz', function(Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $title = $request->getParam('title');

 $sql = "INSERT INTO `quiz`(`title`) VALUES (:title)";
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':title' => $title
   ]);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

 $question_answer = $request->getParam('question_answer');

 $data = [];
 foreach ($question_answer as $qa) {
   $data[] = $qa['type'];
   $data[] = $qa['question'];
   $data[] = $qa['answer'];
   if($qa['type']=="isian"){
     $data[] = "";
   }elseif($qa['type']=="pilgan"){
     $data[] = implode(", ", $qa['decoy']);
   }

   $data[] = date("Y-m-d H:i:s");
   $data[] = $db->lastInsertId();
 }

 $count = count($data);
 $add = [];
 for ($i=0; $i < $count; $i = $i + 6) {
   $add[] = "(?, ?, ?, ?, ?, ?)";
 }

 $sql = "INSERT INTO `question_answer`(`type`,`question`, `answer`, `decoy`, `created_at`, `quiz_id`) VALUES " . implode(',', $add);
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute($data);

   $data = ["notice"=>["type"=>"success", "text" => "Quiz sucessfully added"]];
   return $response->withJson($data);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }
});

// EDIT A QUIZ
$app->post('/quiz/{qid:[0-9]+}', function(Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $title = $request->getParam('title');

 $sql = "UPDATE `quiz` SET `title` = :title WHERE `quiz`.`id` = :qid";
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':title' => $title,
     ':qid' => $args['qid']
   ]);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

 $question_answer = $request->getParam('question_answer');

 $data = [];
 $sql = "";

 foreach ($question_answer as $qa) {
   if($qa["id"]==-99){
     $sql.="INSERT INTO `question_answer`(`type`,`question`, `answer`, `decoy`, `created_at`, `quiz_id`) VALUES (?,?,?,?,?,?);";
     $data[] = $qa['type'];
     $data[] = $qa['question'];
     $data[] = $qa['answer'];
     if($qa['type']=="isian"){
       $data[] = "";
     }elseif($qa['type']=="pilgan"){
       $data[] = implode(", ", $qa['decoy']);
     }

     $data[] = date("Y-m-d H:i:s");
     $data[] = $args['qid'];
   }else{
     $sql.="UPDATE `question_answer` SET `question` = ?, `answer` = ?, `decoy` = ?, `created_at` = ? WHERE `question_answer`.`id` = ?;";

     $data[] = $qa['question'];
     $data[] = $qa['answer'];
     if($qa['type']=="isian"){
       $data[] = "";
     }elseif($qa['type']=="pilgan"){
       $data[] = implode(", ", $qa['decoy']);
     }

     $data[] = date("Y-m-d H:i:s");
     $data[] = $qa['id'];
   }
 }

 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute($data);

   $data = ["notice"=>["type"=>"success", "text" => "Quiz sucessfully added"]];
   return $response->withJson($data);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }
});

// DELETE A QUIZ
$app->delete('/quiz/{id:[0-9]+}', function(Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $id = $args["id"];

 if (empty($id) || filter_var($id, FILTER_VALIDATE_INT) === FALSE) {
   die('Invalid ID');
 }

 try {
  $db = $this->get('db');
  $db->beginTransaction();

  $stmt = $db->prepare('DELETE FROM `quiz` WHERE `id` = :id');
  $stmt->execute([':id' => $id]);

  $stmt2 = $db->prepare('DELETE FROM `question_answer` WHERE `quiz_id` = :id');
  $stmt2->execute([':id' => $id]);

  $db->commit();
  $data = ["notice"=>["type"=>"success", "text" => "Quiz sucessfully deleted"]];
  return $response->withJson($data);
 }
 catch (PDOException $exception) {
  $db->rollBack();
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

});

//Close submission
$app->post('/quiz/{id:[0-9]+}/close', function(Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
    $error = ['error' => ['text' => 'Permission denied']];
    return $response->withJson($error);
  }

  $sql = "UPDATE `quiz` SET `isOpen` = '0' WHERE `quiz`.`id` = :id";
  try {
    $db = $this->get('db');

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $args['id'], PDO::PARAM_INT);
    $stmt->execute();

    $db = null;

    if($stmt->rowCount()>0){
      $success = ["notice"=>["type"=>"success"]];
      return $response->withJson($success);
    }else{
      $success = ["notice"=>["type"=>"error","text"=>"No row affected"]];
      return $response->withJson($success);
    }
  }
  catch (PDOException $e) {
    $error = ['notice' => ["type"=>"error",'text' => $e->getMessage()]];
    return $response->withJson($error);
  }
});

//Reopen submission
$app->post('/quiz/{id:[0-9]+}/open', function(Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
    $error = ['error' => ['text' => 'Permission denied']];
    return $response->withJson($error);
  }

  $sql = "UPDATE `quiz` SET `isOpen` = '1' WHERE `quiz`.`id` = :id";
  try {
    $db = $this->get('db');

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $args['id'], PDO::PARAM_INT);
    $stmt->execute();

    $db = null;

    if($stmt->rowCount()>0){
      $success = ["notice"=>["type"=>"success"]];
      return $response->withJson($success);
    }else{
      $success = ["notice"=>["type"=>"error","text"=>"No row affected"]];
      return $response->withJson($success);
    }
  }
  catch (PDOException $e) {
    $error = ['notice' => ["type"=>"error",'text' => $e->getMessage()]];
    return $response->withJson($error);
  }
});


// CORET MEMBER TEC, SET is_active ke 0
$app->delete('/user/{id:[0-9]+}', function(Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $id = $args["id"];

 if (empty($id) || filter_var($id, FILTER_VALIDATE_INT) === FALSE) {
   die('Invalid ID');
 }

 try {
  $db = $this->get('db');

  $stmt = $db->prepare('UPDATE users SET is_active = 0 WHERE id = :id AND isAdmin = 0');
  $stmt->execute([':id' => $id]);

  $data = ["notice"=>["type"=>"success", "text" => "Member sukses dicoret"]];
  return $response->withJson($data);
 }
 catch (PDOException $exception) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

});

// GET A QUIZ DETAILS
$app->get('/quiz/{id}/full', function(Request $request, Response $response, array $args) {
	$sql = "SELECT title, question_answer.id, `type`, `question`, `answer`, `decoy`, `created_at` FROM `question_answer` INNER JOIN quiz ON question_answer.quiz_id = quiz.id WHERE quiz.id = :id";

   try {
     $db = $this->get('db');

     $stmt = $db->prepare($sql);
     $stmt->execute([
       ':id' => $args['id']
     ]);
     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
     $db = null;

     return $response->withJson($result);
   }
   catch (PDOException $e) {
     $error = ['error' => ['text' => $e->getMessage()]];
     return $response->withJson($error);
   }
});
