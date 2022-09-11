<?php
    // echo "データベースに接続します"."<br>";
    $dsn = 'databasename';
    $user = 'username';
    $password = 'password';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql = "CREATE TABLE IF NOT EXISTS bss_data"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date DATETIME,"
    . "password char(32)"
    .");";
    $stmt = $pdo->query($sql);
    // echo "データベースに接続しました"."<br>";

    if (isset($_POST["user_name"]) && isset($_POST["comment"])) {
        
        //-------- 編集機能 --------//
        if(!empty($_POST["edit_ok"])){
            $edit_name = $_POST["user_name"];
            $edit_comment = $_POST["comment"];
            $edit = $_POST["edit_ok"];
            $edit_date = date ( "Y/m/d H:i:s" );
            
            //-------- フォームの値が空でなければ送信  --------//
            if($edit_comment != NULL && $edit_name != NULL){
                $sql = $pdo -> prepare("UPDATE bss_data SET name=:name,comment=:comment,date=:date WHERE id=:id");
                $sql -> bindParam(':id', $edit, PDO::PARAM_INT);
                $sql -> bindParam(':name', $edit_name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $edit_comment, PDO::PARAM_STR);
                $sql -> bindValue(':date', $edit_date, PDO::PARAM_STR);
                $sql -> execute();
            }else{
                // echo "編集のフォームが未入力です";
            }
            
        //-------- 投稿機能  --------//
        }else if(isset($_POST["password"])){
            $user_name = $_POST["user_name"];
            $comment = $_POST["comment"];
            $password = $_POST["password"];
            $date = date ( "Y/m/d H:i:s" );
            
            if($comment != NULL && $user_name != NULL && $password != NULL){
                
                //-------- 特定のコメント処理  --------//
                if($comment == "完成！"){
                    echo "おめでとう!!!!<br><br>";
                }
                
                // -------- データベース操作 --------//
                $sql = $pdo -> prepare("INSERT INTO bss_data (id, name, comment,date,password) VALUES (:id, :name, :comment, :date, :password)");
                $sql -> bindParam(':id', $id, PDO::PARAM_INT);
                $sql -> bindParam(':name', $user_name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindValue(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql -> execute();
            }else{
                // echo "投稿のフォームが未入力です";
            }
        }
    }
    
    
    if(isset($_POST["delete"])){
        $delete = $_POST["delete"];
        $delete_password = $_POST["delete_password"];
        
        if($delete != NULL && $delete_password != NULL){
            $sql = 'delete from bss_data where id=:delete && password=:delete_password LIMIT 1';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':delete', $delete, PDO::PARAM_INT);
            $stmt->bindParam(':delete_password', $delete_password, PDO::PARAM_STR);
            $stmt->execute();
        }else{
            echo "削除フォームが未入力です";
        }

    }
    
    if(isset($_POST["edit_number"])){
        $edit_number = $_POST["edit_number"];
        $edit_name = $_POST["edit_name"];
        $edit_comment = $_POST["edit_comment"];
        $edit_password = $_POST["edit_password"];
        
        if($edit_number != NULL && $edit_password != NULL){
            
            // -------- 編集したい行を習得  --------//
            $sql = 'SELECT * from bss_data where id=:edit_number & password=:edit_password';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':edit_number', $edit_number, PDO::PARAM_INT);
            $stmt->bindParam(':edit_password', $edit_password, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if($row['id'] == $edit_number && $row['password'] == $edit_password){
                    $edit_name=$row['name'];
                    $edit_comment=$row['comment'];
                }
            }
        }else{
            echo "編集番号フォームが未入力です";
        }
        
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_3_05</title>
    <style>
        .form{
            display:block;
        }
    </style>
</head>
<body>
    
        <!-- コメントを表示 -->
    <form action="" method="post">
        <label class="form">
            <input type="text" name="user_name" placeholder ="名前" value="<?php if (isset($_POST["edit_name"])) { echo $edit_name;} ?>">
        </label>
        <label class="form">
            <input type="text" name="comment" placeholder ="コメント" value="<?php if (isset($_POST["edit_comment"])) {echo $edit_comment;}?>">
        </label>
        <label class="form">
            <input type="text" name="password" placeholder ="パスワード" value="">
            <input type="submit" name="submit">
        </label>
        <label class="form">
            <input type="hidden" name="edit_ok" placeholder ="" value="<?php if (isset($_POST["edit_number"])) {echo $edit_number;} ?>">
        </label>
        <div>
            <br>
        </div>
    </form>
    
    <form action="" method="post">
        <label class="form">
            <input type="text" name="delete" placeholder ="削除対象番号">
        </label>
        <label class="form">
            <input type="text" name="delete_password" placeholder ="パスワード">
            <input type="submit" value = "削除">
        </label>
    </form>
    
    <div>
        <br>
    </div>
        
    <form action="" method="post">
        <label class="form">
            <input type="text" name="edit_number" placeholder ="編集対象番号">
            <input type="hidden" name="edit_name">
            <input type="hidden" name="edit_comment">
        </label>
        <label class="form">
            <input type="text" name="edit_password" placeholder ="パスワード">
            <input type="submit" value = "編集">
        </label>
    </form>
    
<?php

    $sql = 'SELECT * FROM bss_data';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].'  ';
        echo $row['name'].'  ';
        echo $row['comment'].'  ';
        echo $row['date'].'  ';
        echo $row['password'].'<br>';
        echo "<hr>";
    }

    //-------- テーブル削除  --------//
    // $sql = 'DROP TABLE bss_data';
    // $stmt = $pdo->query($sql);
?>
</body>
</html>