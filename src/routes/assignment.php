<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

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
  if(($sortby == null)||($sortby == "")){
    $sql = "SELECT tec_regno,NIM,name,filename, uploaded_at FROM user_assignment INNER JOIN users ON  users.id = user_assignment.user_id WHERE assignment_id = :id";
  }
  else if($sortby == "noTEC_asc"){
    $sql = "SELECT tec_regno,NIM,name,filename, uploaded_at FROM user_assignment INNER JOIN users ON  users.id = user_assignment.user_id WHERE assignment_id = :id ORDER BY `tec_regno` ASC";
  }
  else if($sortby == "noTEC_desc"){
     $sql = "SELECT tec_regno,NIM,name,filename, uploaded_at FROM user_assignment INNER JOIN users ON  users.id = user_assignment.user_id WHERE assignment_id = :id ORDER BY `tec_regno` DESC";
  }
  else if($sortby == "nama_asc"){
     $sql = "SELECT tec_regno,NIM,name,filename, uploaded_at FROM user_assignment INNER JOIN users ON  users.id = user_assignment.user_id WHERE assignment_id = :id ORDER BY `name` ASC";
  }
  else if($sortby == "nama_desc"){
     $sql = "SELECT tec_regno,NIM,name,filename, uploaded_at FROM user_assignment INNER JOIN users ON  users.id = user_assignment.user_id WHERE assignment_id = :id ORDER BY `name` DESC";
  }
  else if($sortby == "waktu_asc"){
     $sql = "SELECT tec_regno,NIM,name,filename, uploaded_at FROM user_assignment INNER JOIN users ON  users.id = user_assignment.user_id WHERE assignment_id = :id ORDER BY `uploaded_at` ASC";
  }
  else if($sortby == "waktu_desc"){
     $sql = "SELECT tec_regno,NIM,name,filename, uploaded_at FROM user_assignment INNER JOIN users ON  users.id = user_assignment.user_id WHERE assignment_id = :id ORDER BY `uploaded_at` DESC";
  }
  else{
    $error = ['error' => ['text' => 'invalid parameter']];
    return $response->withJson($error);
  }

  try {
    $db = $this->get('db');
    $stmt = $db->prepare($sql);
    $stmt->execute([
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


// GET ALL ASSIGNMENT

$app->get('/assignment', function(Request $request, Response $response, array $args) {

 $sql = "SELECT `id`,`title`,`description` FROM `assignments`";
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':id' => $id
   ]);

   $assignment = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
   return $response->withJson($assignment);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

});

// GET ASSIGNMENT

$app->get('/assignment/{id:[0-9]+}', function(Request $request, Response $response, array $args) {

 $id = $args['id'];

 $sql = "SELECT `id`,`title`,`description` FROM `assignments` WHERE `id` = :id";
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':id' => $id
   ]);

   $assignment = $stmt->fetch(PDO::FETCH_OBJ);
   $db = null;
   return $response->withJson($assignment);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

});

// SUBMIT AN ASSIGNMENT
$app->post('/user/assignment/{id:[0-9]+}', function(Request $request, Response $response, array $args) {
  $directory = $this->get('settings')['assignment_directory'];
  $id = $args["id"];
  $user_id = $request->getAttribute("jwt")['id'];

  $uploadedFiles = $request->getUploadedFiles();

  $uploadedFile = $uploadedFiles['assignment'];
  if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
      $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
      $basename = bin2hex(random_bytes(8)); 
      $filename = 'assignment_' . $id . '_' . sprintf('%s.%0.8s', $basename, $extension);

      $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

      try {
        $db = $this->get('db');
        $stmt = $db->prepare("INSERT INTO user_assignment(user_id, assignment_id, filename) VALUES (:user_id, :assignment_id, :filename)");
        $stmt->execute([
          ':user_id' => $user_id,
          ':assignment_id' => $id,
          ':filename' => $filename
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
    $stmt = $db->prepare("SELECT assignment_id, uploaded_at, title as assignment_title, filename FROM user_assignment INNER JOIN assignments WHERE user_id = :user_id");
    $stmt->execute([
      ':user_id' => $user_id
    ]);
    $assignments = $stmt->fetchAll(PDO::FETCH_OBJ);
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
    $stmt = $db->prepare("SELECT filename, uploaded_at, title as assignment_title, filename FROM user_assignment INNER JOIN assignments WHERE user_id = :user_id AND assignment_id = :id");
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
    $stmt = $db->prepare("SELECT filename, uploaded_at, title as assignment_title FROM user_assignment INNER JOIN assignments ON assignments.id = user_assignment.assignment_id WHERE user_id = :user_id");
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

// DOWNLOAD ASSIGNMENT
$app->get('/download/assignment/{filename}', function(Request $request, Response $response, array $args) {
    $directory = $this->get('settings')['assignment_directory'];
    $filename = $args["filename"];
    $file = $directory . DIRECTORY_SEPARATOR . $filename;
    $fh = fopen($file, 'rb');

    $stream = new \Slim\Http\Stream($fh); // create a stream instance for the response body

    return $response->withHeader('Content-Type', 'application/force-download')
                    ->withHeader('Content-Type', 'application/octet-stream')
                    ->withHeader('Content-Type', 'application/download')
                    ->withHeader('Content-Description', 'File Transfer')
                    ->withHeader('Content-Transfer-Encoding', 'binary')
                    ->withHeader('Content-Disposition', 'attachment; filename="' . basename($file) . '"')
                    ->withHeader('Expires', '0')
                    ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                    ->withHeader('Pragma', 'public')
                    ->withBody($stream);
});
