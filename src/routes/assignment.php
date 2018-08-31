<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;
use Slim\Http\UploadedFile;
use Aws\S3\S3Client;

// CREATE AN ASSIGNMENT
$app->post('/assignment', function(Request $request, Response $response, array $args) {
    if ($request->getAttribute("jwt")['isAdmin'] != 1) {
        $error = ['error' => ['text' => 'Permission denied']];
        return $response->withJson($error);
    }

    $title = $request->getParam('title');
    $description = $request->getParam('description');

    $sql = "INSERT INTO `assignments`(`title`,`description`) VALUES (:title, :description)";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description
        ]);

        $data = ["notice"=>["type"=>"success", "text" => "Assignment sucessfully added"]];
        return $response->withJson($data);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }

});

// EDIT AN ASSIGNMENT
$app->put('/assignment/{id:[0-9]+}', function(Request $request, Response $response, array $args) {
    if ($request->getAttribute("jwt")['isAdmin'] != 1) {
        $error = ['error' => ['text' => 'Permission denied']];
        return $response->withJson($error);
    }

    $title = $request->getParam('title');
    $description = $request->getParam('description');
    $id = $args['id'];

    $sql = "UPDATE `assignments` SET `title` = :title, `description` = :description WHERE `id` = :id";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':id' => $id
        ]);

        $data = ["notice"=>["type"=>"success", "text" => "Assignment sucessfully updated"]];
        return $response->withJson($data);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }

});

// DELETE AN ASSIGNMENT
$app->delete('/assignment/{id:[0-9]+}', function(Request $request, Response $response, array $args) {
    if ($request->getAttribute("jwt")['isAdmin'] != 1) {
        $error = ['error' => ['text' => 'Permission denied']];
        return $response->withJson($error);
    }

    $id = $args['id'];

    $sql = "DELETE FROM `assignments` WHERE `id` = :id";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $data = ["notice"=>["type"=>"success", "text" => "Assignment sucessfully deleted"]];
        return $response->withJson($data);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }

});

// GET USER ASSIGNMENT By ASSIGNMENT ID
$app->get('/assignment/{id:[0-9]+}/submission', function(Request $request, Response $response, array $args) {
    if ($request->getAttribute("jwt")['isAdmin'] != 1) {
        $error = ['error' => ['text' => 'Permission denied']];
        return $response->withJson($error);
    }

    $sortby = $request->getQueryParam("sort");
    $sql = "SELECT users.tec_regno AS tec_regno, users.NIM AS NIM, users.name AS name, user_assignment.* FROM user_assignment INNER JOIN users ON  users.id = user_assignment.user_id WHERE assignment_id = :id";
    if(($sortby == null)||($sortby == "")){
        $sql .= "";
    }
    else if($sortby == "noTEC_asc"){
        $sql .= " ORDER BY `tec_regno` ASC";
    }
    else if($sortby == "noTEC_desc"){
        $sql .= " ORDER BY `tec_regno` DESC";
    }
    else if($sortby == "nama_asc"){
        $sql .= " ORDER BY `name` ASC";
    }
    else if($sortby == "nama_desc"){
        $sql .= " ORDER BY `name` DESC";
    }
    else if($sortby == "waktu_asc"){
        $sql .= " ORDER BY `uploaded_at` ASC";
    }
    else if($sortby == "waktu_desc"){
        $sql .= " ORDER BY `uploaded_at` DESC";
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

      $stmt->bindValue(':id',$args['id'],PDO::PARAM_INT);
      $stmt->execute();
      $assignments = $stmt->fetchAll(PDO::FETCH_OBJ);

      $db = null;
    }
    catch (PDOException $e) {
      $error = ['error' => ['text' => $e->getMessage()]];
      return $response->withJson($error);
    }

    try {
      $db = $this->get('db');
      $sql = "SELECT COUNT(*) AS jumlah FROM user_assignment WHERE assignment_id = :id";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':id',$args['id'],PDO::PARAM_INT);
      $stmt->execute();
      $totalCount = $stmt->fetchAll();

      $db = null;
      return $response->withJson(["total"=>$totalCount[0]["jumlah"],"data"=>$assignments]);
    }
    catch (PDOException $e) {
      $error = ['error' => ['text' => $e->getMessage()]];
      return $response->withJson($error);
    }
});

//Close submission
$app->post('/assignment/{id:[0-9]+}/close', function(Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
    $error = ['error' => ['text' => 'Permission denied']];
    return $response->withJson($error);
  }

  $sql = "UPDATE `assignments` SET `isOpen` = '0' WHERE `assignments`.`id` = :id";
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
$app->post('/assignment/{id:[0-9]+}/open', function(Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
    $error = ['error' => ['text' => 'Permission denied']];
    return $response->withJson($error);
  }

  $sql = "UPDATE `assignments` SET `isOpen` = '1' WHERE `assignments`.`id` = :id";
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


// GET ALL ASSIGNMENT

$app->get('/assignment', function(Request $request, Response $response, array $args) {

    $sql = "SELECT `id`,`title`,`description`,`isOpen` FROM `assignments`";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $assignments = $stmt->fetchAll(PDO::FETCH_OBJ);

        $user_id = $request->getAttribute("jwt")['id'];

        foreach($assignments as $assignment){
            $sql_check = "SELECT * FROM user_assignment WHERE user_id = :uid AND assignment_id = :aid";
            $stmt = $db->prepare($sql_check);
            $stmt->execute([':uid' => $user_id, ':aid'=>$assignment->id]);
            $cek = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($cek) !=0 ) {
                $assignment->terkirim=1;
            }else{
                $assignment->terkirim=0;
            }


        }
        $db = null;
        return $response->withJson($assignments);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }

});

// GET ASSIGNMENT

$app->get('/assignment/{id:[0-9]+}', function(Request $request, Response $response, array $args) {

    $id = $args['id'];

    $sql = "SELECT `id`,`title`,`description`,`isOpen` FROM `assignments` WHERE `id` = :id";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $assignment = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if($assignment->isOpen==0){
          $error = ['error' => ['text' => "Assignment sudah tidak menerima upload"]];
          return $response->withJson($error);
        }

        return $response->withJson($assignment);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }

});

// SUBMIT AN ASSIGNMENT
$app->post('/user/assignment/{id:[0-9]+}', function(Request $request, Response $response, array $args) {
    $id = $args["id"];
    $user_id = $request->getAttribute("jwt")['id'];

    $sql = "SELECT * FROM `assignments` WHERE id = :aid";

    try {
      $db = $this->get('db');
      $stmt = $db->prepare($sql);
      $stmt->execute([':aid'=>$id]);
      $result = $stmt->fetch();

      if($result['isOpen'] == 0) {
        return $response->withJson(['error'=>['text' => 'Assignment sudah tidak menerima upload']]);
      }
    }
    catch (PDOException $e) {
      $error = ['error' => ['text' => $e->getMessage()]];
      return $response->withJson($error);
    }

    /** @var Aws\S3\S3Client $spaces */
    $spaces = $this->spaces;

    $uploadedFiles = $request->getUploadedFiles();

    $uploadedFile = $uploadedFiles['assignment'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = 'assignment/assignment_' . $id . '_' . sprintf('%s.%0.8s', $basename, $extension);

        try {
            //check if already submitted
            $sql = "SELECT filename, file_url FROM user_assignment WHERE user_id = :uid AND assignment_id = :aid";

            $db = $this->get('db');
            $stmt = $db->prepare($sql);
            $stmt->execute([':uid' => $user_id, ':aid'=>$id]);
            $result = $stmt->fetch();

            // Upload a file to the Space

            /** @var \Aws\Result $insert */
            $insert = $spaces->putObject([
                'Bucket' => $this->get('settings')['spaces']['name'],
                'Key'    => $filename,
                'ACL'    => 'public-read',
                'Body'   => $uploadedFile->getStream()->getContents()
            ]);

            $objectUrl = $insert->get("ObjectURL");

            if($result != false) {
                //delete previous
                if(!empty($result['filename'])) {
                    $spaces->deleteObject([
                        'Bucket' => $this->get('settings')['spaces']['name'],
                        'Key' => $result['filename']
                    ]);
                }
                $sql="UPDATE `user_assignment` SET `filename` = :filename, `file_url`=:furl WHERE `user_id` = :user_id AND `assignment_id` = :assignment_id ";
            }else {
                $sql="INSERT INTO user_assignment(user_id, assignment_id, filename, file_url) VALUES (:user_id, :assignment_id, :filename, :furl)";
            }

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':assignment_id' => $id,
                ':filename' => $filename,
                ':furl' => $objectUrl
            ]);

            $result = ["notice"=>["type"=>"success", "text" => "Assignment sucessfully uploaded"], "filename" => $filename];
            return $response->withJson($result);
        }
        catch (PDOException $e) {
            $error = ['error' => ['text' => $e->getMessage()]];
            return $response->withJson($error);
        }
    }
    else {
        return $response->withJson(['error'=>['text' => 'Upload failed']]);
    }
});

// GET ALL USER ASSIGNMENT
$app->get('/user/assignment', function(Request $request, Response $response, array $args) {
    try {
        $db = $this->get('db');
        $user_id = $request->getAttribute("jwt")['id'];
        $stmt = $db->prepare("SELECT assignment_id, uploaded_at, title as assignment_title, filename, file_url FROM user_assignment INNER JOIN assignments WHERE user_id = :user_id");
        $stmt->execute([
            ':user_id' => $user_id
        ]);
        $assignments = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach($assignments as $assignment){
            $sql_check = "SELECT * FROM user_assignment WHERE user_id = :uid AND assignment_id = :aid";
            $stmt = $db->prepare($sql_check);
            $stmt->execute([':uid' => $user_id, ':aid'=>$assignment->id]);
            $cek = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($cek) !=0 ) {
                $assignment->terkirim=1;
            }else{
                $assignment->terkirim=0;
            }

        }
        $db = null;
        return $response->withJson($assignments);
    }
    catch (PDOException $e) {
        return $response->withJson(['error'=>['text' => 'Something wrong happened']]);
    }
});

// GET USER ASSIGNMENT By ASSIGNMENT ID
$app->get('/user/assignment/{id:[0-9]+}', function(Request $request, Response $response, array $args) {
    try {
        $db = $this->get('db');
        $user_id = $request->getAttribute("jwt")['id'];
        $stmt = $db->prepare("SELECT filename, file_url, uploaded_at, title as assignment_title, filename FROM user_assignment INNER JOIN assignments WHERE user_id = :user_id AND assignment_id = :id");
        $stmt->execute([
            ':user_id' => $user_id,
            ':id' => $args['id']
        ]);
        $assignments = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        return $response->withJson($assignments);
    }
    catch (PDOException $e) {
        return $response->withJson(['error'=>['text' => 'Something wrong happened']]);
    }
});

// GET USER ASSIGNMENT By User ID
$app->get('/user/{id:[0-9]+}/assignment', function(Request $request, Response $response, array $args) {
    try {
        $db = $this->get('db');
        $user_id = $request->getAttribute("jwt")['id'];
        if($user_id != $args['id']){
            if ($request->getAttribute("jwt")['isAdmin'] != 1) {
                $error = ['error' => ['text' => 'Permission denied']];
                return $response->withJson($error);
            }

        }
        $stmt = $db->prepare("SELECT filename, file_url, uploaded_at, title as assignment_title FROM user_assignment INNER JOIN assignments ON assignments.id = user_assignment.assignment_id WHERE user_id = :user_id");
        $stmt->execute([
            ':user_id' => $args['id']
        ]);
        $assignments = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        return $response->withJson($assignments);
    }
    catch (PDOException $e) {
        return $response->withJson(['error'=>['text' => 'Something wrong happened']]);
    }
});


// GET ALL USERS ASSIGNMENT
$app->get('/users/assignments', function(Request $request, Response $response, array $args) {

    if ($request->getAttribute("jwt")['isAdmin'] != 1) {
      $error = ['error' => ['text' => 'Permission denied']];
      return $response->withJson($error);
    }

    try {
        $db = $this->get('db');
        $sql = "SELECT `NIM`, `users`.`name`, `tec_regno`, `assignment_id`, `uploaded_at`, `title` as `assignment_title`, `filename`, `file_url` FROM user_assignment INNER JOIN assignments ON `assignments`.`id` = `user_assignment`.`assignment_id` INNER JOIN users ON `user_assignment`.`user_id` = `users`.`id` ORDER BY `uploaded_at` DESC";

        $page = $request->getQueryParam("page");
        $number_per_items = $request->getQueryParam("items_per_page") ? (int) $request->getQueryParam("items_per_page") : 5;
        if (isset($page)) {
          $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', $number_per_items, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $number_per_items * ($page - 1), PDO::PARAM_INT);
        $stmt->execute();
        $assignments = $stmt->fetchAll();
        return $response->withJson($assignments);
    }
    catch (PDOException $e) {
        die($e->getMessage());
        return $response->withJson(['error'=>['text' => 'Something wrong happened']]);
    }
});
