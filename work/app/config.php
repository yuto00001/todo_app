<?php
//6 フォームですが、データを送信する時には必ず CSRF 対策を施しておきましょう。
session_start();
//PDOを使ってデータベースにアクセする作業＝コメント①群（裸コメント）
//テーブルの設定ができたので、次は php から PDO を使ってアクセスしていきましょう
//データベースにアクセスするための定数をいくつか定義。まずは DSN (データソースネーム)
//mysql を今回は使っていくよと書いてあげて、 host の名前が db 、データベースの名前が myapp と書いてあげます。
define('DSN', 'mysql:host=db;dbname=myapp;charset=utf8mb4');
//あとは myapp データベースにアクセスするためのユーザー名とパスワードを定義してあげます。
define('DB_USER', 'myappuser');
define('DB_PASS', 'myapppass');
//５二重投稿を防ぐために、リダイレクト処理を追加していきます。
//5 SITE_URL という定数で URL を保持することにしましょう。 localhost の 8562 番だよとしてあげればいいですね。
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);

// 切り出し済みの関数のあるファイルの呼び出し
// 自動ロードの仕組み ＝ spl_autoload_register()
// まだ読み込まれていないクラスが使われると、クラス名がこの関数の引数に入ってくるので、 $class という変数で受ける。
// あとは読み込むファイル名を作ればいいので、 sprintf() を使う。
// 上にある require で読み込んでいるファイル名と同じにすればいいので、 __DIR__ のあとに、 '/' 、クラス名 .php としてあげれば OK
spl_autoload_register(function ($class) {
  $prefix = 'MyApp\\';
  if (strpos($class, $prefix) === 0) {
    $fileName = sprintf(__DIR__ . '/%s.php', substr($class, strlen($prefix)));
    if (file_exists($fileName)) {
      require($fileName);
    } else {
      echo 'File not found: ' . $fileName;
      exit;
    }
  }
});
