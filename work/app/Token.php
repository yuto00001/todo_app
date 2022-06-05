<?php
// フォームに対して複雑なトークンを割り当てることで外部からの攻撃を防ぎ、尚且つ不正なトークンで実行された場合にはエラーコードが表示されるようにする。
namespace MyApp;
class Token
{
  //インスタンスを作って操作するようなものでもないので、クラスから直接呼び出せるクラスメソッドにする。＝public static をつける（index.phpから、クラス名:: を使って呼び出せるようになる。）

  // トークンを作るための関数
  //5 フォームの送信時に必要なCSRF対策を施す。
  public static function create()
  {
    if (!isset($_SESSION['token'])) {
      $_SESSION['token'] = bin2hex(random_bytes(32));
    }
  }
  // 5 作ったトークンを検証するための関数
  // セッションのトークンが空か、もしくはセッションのトークンとフォームが送信されたときに一緒に送信されるトークンが一致していなかったらという条件を書く。その場合は不正な処理になるので、こちらでメッセージを出して、終了させる。
  public static function validate()
  {
    if (
      empty($_SESSION['token']) ||
      $_SESSION['token'] !== filter_input(INPUT_POST, 'token')
    ) {
      exit('Invalid post request');
    }
  }
}
