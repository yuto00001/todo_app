<?php

// コンストラクター：オブジェクト指向プログラミングにおいて，オブジェクトを生成するための手続き

// こちらで渡した pdo は、プロパティとして保持して他のメソッドで使いたいので宣言して、プロパティに代入しておく。
// トークンの作成や検証は post で送信するときに必要なものなので、処理している Todo クラスのコンストラクタに入れる。
namespace MyApp;

class Todo
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
    Token::create();
  }

  //4  $_SERVER を調べて、 REQUEST_METHOD が POST だったらという条件分岐をする。定義は別箇所でする。
  // 6-2 あとは、このフォームが送信されたときにここで埋め込んだ値と、セッションのトークンが一致するか調べればいいので、 validateToken() とすれば OK
  // Todo クラスの中でメソッドを作っていきたいので $this で置き換える。
  public function processPost()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      Token::validate();
      $action = filter_input(INPUT_GET, 'action');
      switch ($action) {
        case 'add':
          $id = $this->add();
          header('Content-Type: application/json');
          echo json_encode(['id' => $id]);
          break;
        case 'toggle':
          $isDone = $this->toggle();
          header('Content-Type: application/json');
          echo json_encode(['is_done' => $isDone]);
          break;
        case 'delete':
          $this->delete();
          break;
        case 'purge':
          $this->purge();
          break;
        default:
          exit;
      }
      exit;
    }
  }
  // 以下三つのメソッドは、クラス内からしか呼び出さないので private 関数にする。
  private function add()
  {
    $title = trim(filter_input(INPUT_POST, 'title'));
    if ($title === '') {
      return;
    }

    $stmt = $this->pdo->prepare("INSERT INTO todos (title) VALUES (:title)");
    $stmt->bindValue('title', $title, \PDO::PARAM_STR);
    $stmt->execute();
    return (int) $this->pdo->lastInsertId();
  }

  // toggleTodo()の実装
  // データの更新
  private function toggle()
  {
    $id = filter_input(INPUT_POST, 'id');
    if (empty($id)) {
      return;
    }

    $stmt = $this->pdo->prepare("SELECT * FROM todos WHERE id = :id");
    $stmt->bindValue('id', $id, \PDO::PARAM_INT);
    $stmt->execute();
    $todo = $stmt->fetch();
    if (empty($todo)) {
      header('HTTP', true, 404); // HTTP Status Code
      exit;
    }

    $stmt = $this->pdo->prepare("UPDATE todos SET is_done = NOT is_done WHERE id = :id");
    $stmt->bindValue('id', $id, \PDO::PARAM_INT);
    $stmt->execute();
    return (boolean) !$todo->is_done;
  }
  private function delete()
  {
    $id = filter_input(INPUT_POST, 'id');
    if (empty($id)) {
      return;
    }

    $stmt = $this->pdo->prepare("DELETE FROM todos WHERE id = :id");
    $stmt->bindValue('id', $id, \PDO::PARAM_INT);
    $stmt->execute();
  }

  private function purge()
  {
    $this->pdo->query("DELETE FROM todos WHERE is_done = 1");
  }

// todo を表示するために配列(データ)を所得する処理ーーー
// データベースにアクセスしていくので $pdo を引数で渡す。
// まずは SELECT 文を使ってレコードを取得
// 新しい順に並べたいので、 id を降順で並び替えたうえで取得
// Pdo に関しては、引数に渡すのではなくてプロパティ(変数)を使いたいので、$this->pdo
//2 そのうえで fetchAll を実行してあげれば、この SQL 文の結果が返ってくる
  public function getAll()
  {
    $stmt = $this->pdo->query("SELECT * FROM todos ORDER BY id DESC");
    $todos = $stmt->fetchAll();
    return $todos;
  }
}
