<?php
// todo に関するデータ処理

// getInstance() が呼ばれるたびにデータベースに接続してしまうと複数の接続ができてしまって無駄なので、こちらの PDO クラスから作られる instance は必ずひとつになるように工夫してあげるとなお良い。
namespace MyApp;
class Database
{
  private static $instance;
    //インスタンスを作って操作するようなものでもないので、クラスから直接呼び出せるクラスメソッドにする。＝public static をつける
  // 検証したトークンをデータベースと接続するための関数
  public static function getInstance()
  {
    //あとは PDO のインスタンスを作っていきたいのですが、エラーになったら例外をなげるようにしたいので、 try {} catch(){} をとりあえず書いておきます。
    //そのうえで try の中でインスタンスを作っていきましょう。
    try {
      if (!isset(self::$instance)) {
        self::$instance = new \PDO(
          DSN,
          DB_USER,
          DB_PASS,
          [
            //それから、オプションとしてエラーが起きた時は例外を投げてねという設定をしてあげましょう。
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            //2 今回はオブジェクト形式で結果を取得したいので、 FETCH_MODE のオプションを FETCH_OBJ にしておいてあげます。
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            //2 それから、取得したデータの型を SQL で定義した型に合わせて取得したいので、EMULATE_PREPARES のオプションを false にしてあげます。
            \PDO::ATTR_EMULATE_PREPARES => false,
          ]
        );
      }

      return self::$instance;
        //あとは例外をこちらで受ければいいので、 PDOException 型の例外が投げられた時は $e でその例外を受けてあげて、
    } catch (\PDOException $e) {
      //とりあえずメッセージを表示してあげてから、ここで終了させたいので exit; としておくといいでしょう。
      echo $e->getMessage();
      exit;
    }
  }
}
