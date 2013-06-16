# takyam/bench
PHPスクリプトのベンチマークを取るツール
フレームワークその他と連携しない単体のスクリプトファイル用。

## install
composer.json に以下を追加

```json
{
	"require-dev": {
		"takyam/bench": "*"
	}
}
```

あとはいつもどおり php composer.phar install/update でいけるはず

## Usage

```php
<?php
$scripts_dir_path = 'hoge/fuga/scripts';
$bench = new Bench($scripts_dir_path . '/a.php', $scripts_dir_path . '/b.php');
echo $bench->run();
```