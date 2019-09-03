<?php
//データーベースの接続
	$dsn ='データベース名';
	$user ='ユーザー名';
	$password ='パスワード';
//データベースへの接続
	$pdo =new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブル内へのテーブル作成
	$sql = "CREATE TABLE IF NOT EXISTS mission5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "time TEXT,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);

	$edit_num = 0; 			//編集番号
	$edit_name = "名前"; 		//名前
	$edit_comment = "コメント";	//コメント
	$message = ""; 			//表示するもの
	$edit_pass = "";		//編集パスワード

?>

<!DOCTYPE html>

<html>
<head>
  <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
	<meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
	<title>mission_5.php</title>
</head>

<body>


<?php

	if($_POST == NULL ){		//最初のアクセス
                $message = "最初のアクセス <br>";
	}
	elseif(isset($_POST["delete"])){	//削除フォーム

		//echo $_POST["delete_num"]." を削除します.<br>";	//分岐確認用
		$id = $_POST["delete_num"];
	
		$sql = 'SELECT id,pass FROM mission5';
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();
		foreach ($results as $row){
			
			if($row['id'] == $_POST["delete_num"]){
				
				if($row['pass'] == $_POST["delete_pass"]){	//削除する
					$sql = 'delete from mission5 where id=:id';
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id, PDO::PARAM_INT);
					$stmt->execute();
					$message = $_POST["delete_num"]."を削除しました"."<br>";
				}
				else{	//削除しない
					$message = "パスワードが違います"."<br>";	//パスワード×
				}
			}
			
		}

	}
	elseif(isset($_POST["edit"])){	//編集フォーム

		//echo $_POST["edit_num"]." を編集します.<br>";		//分岐確認用
		$id = $_POST["edit_num"];
	
		$sql = 'SELECT * FROM mission5';
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();
		foreach ($results as $row){

			if($row['id'] == $_POST["edit_num"]){
				
				if($row['pass'] == $_POST["edit_pass"]){	//編集する
					$edit_num = $row['id'];
					$edit_name = $row['name'];
					$edit_comment = $row['comment'];
					$edit_pass = $row['pass'];
					$message = $_POST["edit_num"]."を編集します"."<br>";
				
				}
				else{	//編集しない
					$message = "パスワードが違います"."<br>";	//パスワード×
				}
			}
		}
	}
	elseif(isset($_POST["send"])){	//投稿フォーム

		if($_POST["comment"] == ""){	//コメントなし
			 $message = "コメントが入力されていません<br>";
                }
		elseif($_POST["edit_judge"] != 0){	//編集モード

			$message = "編集モード";

			$id = $_POST["edit_judge"];	//対象の投稿番号
			$name = $_POST["name"];		//変更する名前
			$comment = $_POST["comment"];	//変更するコメント
			$time = date("Y/m/d H:i:s");	//対象の時間
			$pass = $_POST["pass"];		//対象のパスワード

			$sql = 'update mission5 set name=:name,comment=:comment,time=:time,pass=:pass where id=:id';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->bindParam(':time', $time, PDO::PARAM_STR);
			$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);

			$stmt->execute();
		}
		else{	//新規投稿モード

			$message = "新規投稿モード";

			$sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, time, pass) VALUES (:name, :comment, :time, :pass)");
			$sql -> bindParam(':name', $name, PDO::PARAM_STR);
			$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
			$sql -> bindParam(':time', $time, PDO::PARAM_STR);
			$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);

			$name = $_POST["name"];		//投稿番号
			$comment = $_POST["comment"];	//コメント
			$time = date("Y/m/d H:i:s");	//時間
			$pass = $_POST["pass"];		//パスワード

			$sql -> execute();
		}
	}
	else{
		$message = "エラー"."<br>";
	}
?>
<form action="mission_5.php" method="POST">
	<div style = "display:inline-flex">
	<div><!-- 新規登録モードフォーム -->
		<input type="text" name="name" value="<?php echo $edit_name; ?>">
		<input type="text" name="comment" value="<?php echo $edit_comment; ?>">
		<input type="password" name="pass" value="<?php echo $edit_pass; ?>">
		<input type="submit" name="send" value="送信">
	</div>
	<div><!-- 編集モードフォーム -->
		<input type="hidden" name="edit_judge" value="<?php echo $edit_num; ?>">
	</div>

	<div><!-- 削除フォーム -->
		<input type="text" name="delete_num" value="削除番号">
		<input type="password" name="delete_pass" value="パスワード">
		<input type="submit" name="delete" value="削除">
	</div>

	<div><!-- 編集フォーム -->
		<input type="text" name="edit_num" value="編集対象番号">
		<input type="password" name="edit_pass" value="パスワード">
		<input type="submit" name="edit" value="編集">
	</div>
	</div>
</form>

<?php

        echo $message."<br>";	//表示する

        $sql = 'SELECT * FROM mission5';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();

	foreach ($results as $row){

		echo "投稿番号:: ".$row['id']."  ";
		echo "名前:: ".$row['name']."  ";
		echo "コメント:: ".$row['comment']."  ";
		echo "時間::".$row['time']."  "."<br>";
		//echo "パスワード : ".$row['pass'];	//パスワード確認用
	}
?>

</body>
</html>