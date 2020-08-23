<?php

// 管理ページのログインパスワード
define( 'PASSWORD', 'adminPassword');

// データベースの接続情報
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', 'root');
define( 'DB_NAME', 'board');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();

session_start();

if( !empty($_GET['btn_logout']) ) {
	unset($_SESSION['admin_login']);
}

if( !empty($_POST['btn_submit']) ) {

	if( !empty($_POST['admin_password']) && $_POST['admin_password'] === PASSWORD ) {
		$_SESSION['admin_login'] = true;
	} else {
		$error_message[] = 'ログインに失敗しました。';
	}
}

// データベースに接続
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 接続エラーの確認
if( $mysqli->connect_errno ) {
	$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {

	$sql = "SELECT id,view_name,message,post_date FROM message ORDER BY post_date DESC";
	$res = $mysqli->query($sql);

    if( $res ) {
		$message_array = $res->fetch_all(MYSQLI_ASSOC);
    }

    $mysqli->close();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="style.css">
<title>ひと言掲示板 管理ページ</title>
</head>
<body>
<h1>ひと言掲示板 管理ページ</h1>
<?php if( !empty($error_message) ): ?>
	<ul class="error_message">
		<?php foreach( $error_message as $value ): ?>
			<li>・<?php echo $value; ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
<section>
<?php if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ): ?>

<form method="get" action="./download.php">
	<select name="limit">
		<option value="">全て</option>
		<option value="10">10件</option>
		<option value="30">30件</option>
	</select>
	<input type="submit" name="btn_download" value="ダウンロード">
</form>

<?php if( !empty($message_array) ){ ?>
<?php foreach( $message_array as $value ){ ?>
<article>
	<div class="info">
		<h2><?php echo $value['view_name']; ?></h2>
		<time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
		<p><a href="edit.php?message_id=<?php echo $value['id']; ?>">編集</a>&nbsp;&nbsp;<a href="delete.php?message_id=<?php echo $value['id']; ?>">削除</a></p>
	</div>
	<p><?php echo nl2br($value['message']); ?></p>
</article>
<?php } ?>
<?php } ?>

<form method="get" action="">
    <input type="submit" name="btn_logout" value="ログアウト">
</form>

<?php else: ?>

<form method="post">
	<div>
		<label for="admin_password">ログインパスワード</label>
		<input id="admin_password" type="password" name="admin_password" value="">
	</div>
	<input type="submit" name="btn_submit" value="ログイン">
</form>
<?php endif; ?>
</section>
</body>
</html>
