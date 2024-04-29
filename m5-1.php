<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
</head>
    <body>

 
 <?php 
    //データベース接続設定
    $dsn = 'mysql:dbname=XXXDB;host=localhost';
    $user = 'XXXUSER';
    $pw = 'XXXPASSWORD';
    $pdo = new PDO($dsn, $user, $pw, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //投稿機能
    if(!empty($_POST["name"]) && !empty($_POST["comment"])){
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        //$date = date("Y/n/j G:i:s");
        $password = $_POST["password"];
        if(!empty($_POST["newnum"])){
            $newnum = $_POST['newnum']; 
            $sql = 'UPDATE Post SET name=:name, comment=:comment, password=:password WHERE id=:newnum'; 
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':newnum', $newnum, PDO::PARAM_INT); 
            $stmt->execute();
        }else{
            try{
                $pdo->beginTransaction();
                $sql = "INSERT INTO Post (name, comment, password) VALUES (:name, :comment, :password)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->execute();
                $pdo->commit();
            }catch(Exception $e){
                $pdo->rollback();
                echo "エラー". $e->getMessage();
            }
        }
    }
    
    //削除機能
    if(!empty($_POST["delete"])){
        $delete = $_POST["delete"];
        $sql1 = 'SELECT password FROM Post WHERE id = :delete';
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->bindParam(':delete', $delete, PDO::PARAM_INT);
        $stmt1->execute();
        $password_row = $stmt1->fetch(PDO::FETCH_ASSOC);
        $password = $_POST["deletepassword"]; 
        $password2 = $password_row['password'];
    
        if($password == $password2){
                $sql2 = 'DELETE FROM Post WHERE id=:id'; 
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->bindParam(':id', $delete, PDO::PARAM_INT);
                $stmt2->execute();
        }
    }  
    
    //編集番号選択
    if(!empty($_POST["edit"])){
        $edit = $_POST["edit"];
        $sql1 = 'SELECT id, name, comment, password FROM Post WHERE id= :editnumber';
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->bindParam(':editnumber', $edit, PDO::PARAM_INT); 
        $stmt1->execute();
        $password_row = $stmt1->fetch(PDO::FETCH_ASSOC);
        $password = $_POST["editpassword"];
        $password2 = $password_row['password'];
        if($password == $password2){
                $editnumber = $password_row['id'];
                $editname = $password_row['name'];
                $editcomment = $password_row['comment'];
        }
    }
    
 ?>
 
 <form action="" method="POST">
     <input type="text" name="name" value="<?php if(isset($editname)) {echo $editname;} ?>"><br>
     <input type="text" name="comment" value="<?php if(isset($editcomment)) {echo $editcomment;} ?>"><br>
     <input type="text" name="password" value="<?php if(isset($password2)) {echo $password2;} ?>">
     <input type="hidden" name="newnum" value="<?php if(isset($editnumber)) {echo $editnumber;} ?>">
     <input type="submit" name="submit" value="送信"><br>
     <br>
     <input type="number" name="delete" placeholder="削除対象番号"><br>
     <input type="text" name="deletepassword" placeholder="パスワード"> 
     <input type="submit" name="submit" value="削除"><br>
     <br>
     <input type="number" name="edit" placeholder="編集対象番号"><br>
     <input type="text" name="editpassword" placeholder="パスワード"> 
     <input type="submit" name="submit" value="編集">
 </form>


 <?php
    $sql = 'SELECT * FROM Post';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['created_at'].'<br>';
        echo "<hr>";
    }
 ?>
</body>
</html>