<?php
// <>のようなhtml記号を、タグではなく文字列実体参照として表示されるようにする。これにより、外部からのコードによる命令攻撃を防ぐとともに、すべての記述を文字列として受け入れられる。
namespace MyApp;
class Utils
{
  //3 文字列を HTML に埋め込むための h() 関数
  //インスタンスを作って操作するようなものでもないので、クラスから直接呼び出せるクラスメソッドにする。＝public static をつける
  public static function h($str)
  {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}
