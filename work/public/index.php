<?php

//7 以下記述は、config.phpに切り出し済み
require_once(__DIR__ . '/../app/config.php');

// このようにしておけば Database クラスがでてきたら MyApp\ を付けて呼び出してくれる。
use MyApp\Database;
use MyApp\Todo;
use MyApp\Utils;

//8 では、こちらで PDO のインスタンスを返す getPdoInstance() を作ってあげて、 functions.php のほうで定義していきましょう。以下関数は、functions.phpに切り出し済み。なお、読み込みはconfig.phpから行う。
$pdo = Database::getInstance();

// post で送信されたデータの処理ーーー
//  pdo を使って Todo() クラスのインスタンスを作る
//  post で送信されたデータを処理するメソッドは processPost()
//  $todo を表示するために配列を取得するメソッドは getAll()
$todo = new Todo($pdo);
$todo->processPost();
$todos = $todo->getAll();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>My Todos</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <main data-token="<?= Utils::h($_SESSION['token']); ?>">
    <header>
      <h1>Todos</h1>
      <span class="purge">Purge</span>
    </header>

    <!-- 4 Todoの追加＝コメント④群 -->
    <!-- 4 form タグを設置してあげて、送信先は index.php にしたいので、ここは空にしておきます。 post 形式で送信していきましょう。-->
    <!-- 4 name は title にしてあげて、わかりやすいように placeholder を付けてあげましょう。 -->
    <!--6-1 CSRF 対策ですがまずは上で作った createToken を実行して、トークンを作って、セッションに仕込む。そのうえでフォームにもその値を埋め込んで送信する。 -->
    <form>
      <input type="text" name="title" placeholder="Type new todo.">
      <!-- 4 あとはフォームが送信された時にデータを追加したいので、 php のほうで書いていきましょう。 -->
    </form>

    <!-- 3 データを HTML に埋め込む＝コメント③群 -->
    <!-- 3 データの数だけループをすればいいので、foreach を使う -->
    <!-- 3 input 要素の checked 属性と done クラスは todo の is_done に応じて付けてあげればいいので、条件演算子を使ってあげましょう。 -->
    <!-- 3 is_done が true だったら、 checked の文字列、そうじゃなかったら、空文字列としてあげればいいでしょう -->
    <!-- 3 では h という関数にしてあげて、文字列を受け取ったら、htmlspecialchars で処理をして、返してあげるという処理を定義してあげましょう。↑ -->
    <!-- Utils:: = Utilsクラスから呼び出す -->
    <ul>
      <?php foreach ($todos as $todo): ?>
      <li data-id="<?= Utils::h($todo->id); ?>">
        <input type="checkbox" <?= $todo->is_done ? 'checked' : ''; ?>>
        <span><?= Utils::h($todo->title); ?></span>
        <span class="delete">x</span>
      </li>
      <?php endforeach; ?>
    </ul>
  </main>
  <script src="js/main.js"></script>
</body>
</html>
