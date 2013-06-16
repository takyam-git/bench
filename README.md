# takyam/bench
PHPスクリプトのベンチマークを取るツール
フレームワークその他と連携しない単体のスクリプトファイル用。

## install
composer.json に以下を追加

```json
{
	"require-dev": {
		"takyam/bench": "dev-master"
	}
}
```

あとはいつもどおり php composer.phar install/update でいけるはず

## Usage

```php
<?php
$scripts_dir_path = '/hoge/fuga/scripts';
$bench = new Bench($scripts_dir_path . '/a.php', $scripts_dir_path . '/b.php');
$bench->set_repeat(50); //各スクリプトを50回ずつ実行
echo $bench->run();
/* 以下のような文字列が出力される
################### Benchmark results ###################
=========================================================
#0 : /hoge/fuga/scripts/a.php
    (50 times)
    avg time: 1 milli sec (0.00071602344512939 micro sec)
    max time: 3 milli sec (0.0030989646911621 micro sec)
    min time: 1 milli sec (0.0005338191986084 micro sec)
    total time: 36 milli sec (0.03580117225647 micro sec)
---------------------------------------------------------
#1 : /hoge/fuga/scripts/b.php
    (50 times)
    avg time: 0 milli sec (0.00044251441955566 micro sec)
    max time: 1 milli sec (0.00050497055053711 micro sec)
    min time: 0 milli sec (0.00041580200195312 micro sec)
    total time: 22 milli sec (0.022125720977783 micro sec)
*/
```