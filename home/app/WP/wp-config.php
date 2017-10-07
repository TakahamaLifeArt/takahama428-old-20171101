<?php
/**
 * WordPress の基本設定
 *
 * このファイルは、インストール時に wp-config.php 作成ウィザードが利用します。
 * ウィザードを介さずにこのファイルを "wp-config.php" という名前でコピーして
 * 直接編集して値を入力してもかまいません。
 *
 * このファイルは、以下の設定を含みます。
 *
 * * MySQL 設定
 * * 秘密鍵
 * * データベーステーブル接頭辞
 * * ABSPATH
 *
 * @link http://wpdocs.sourceforge.jp/wp-config.php_%E3%81%AE%E7%B7%A8%E9%9B%86
 *
 * @package WordPress
 */

// 注意: 
// Windows の "メモ帳" でこのファイルを編集しないでください !
// 問題なく使えるテキストエディタ
// (http://wpdocs.sourceforge.jp/Codex:%E8%AB%87%E8%A9%B1%E5%AE%A4 参照)
// を使用し、必ず UTF-8 の BOM なし (UTF-8N) で保存してください。

// ** MySQL 設定 - この情報はホスティング先から入手してください。 ** //
/** WordPress のためのデータベース名 */
define('DB_NAME', 'd026z32ydb3');

/** MySQL データベースのユーザー名 */
define('DB_USER', 'd026z32y');

/** MySQL データベースのパスワード */
define('DB_PASSWORD', 'takahama428');

/** MySQL のホスト名 */
define('DB_HOST', 'localhost');

/** データベースのテーブルを作成する際のデータベースのキャラクターセット */
define('DB_CHARSET', 'utf8');

/** データベースの照合順序 (ほとんどの場合変更する必要はありません) */
define('DB_COLLATE', '');

/**#@+
 * 認証用ユニークキー
 *
 * それぞれを異なるユニーク (一意) な文字列に変更してください。
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org の秘密鍵サービス} で自動生成することもできます。
 * 後でいつでも変更して、既存のすべての cookie を無効にできます。これにより、すべてのユーザーを強制的に再ログインさせることになります。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '%%@oVn2hX8m~==GPTi!UY]Q<M-0OOL>|?P!C)~[gm%|%QqP}+E!Oe4_Z>#)7+=oI');
define('SECURE_AUTH_KEY',  '$`!DKVjecMY2%_u0m-+K7P[#}[n<{_HkTOs5Ng+gAJK(`,/)OrBe|(aZ7p8Orog;');
define('LOGGED_IN_KEY',    'Ll#V-+ZjF2D:A)3aO2YDJ^4-Nz+q[~-dD_CM*L0O>s^oH*B-hw~:KwY IC%>M#bk');
define('NONCE_KEY',        '?TavuX@xcGTh[8G#l)^KNZA~}{<m59&iUU11] pc,3N;%o#MX@<?stALK)=lud5{');
define('AUTH_SALT',        '} ~0F7-O$u/0?|c?o!eh_OVN.eyK9}A|Xe.1gBv36%lCOol])s^cPwok^T25e1LH');
define('SECURE_AUTH_SALT', ',!,f3As7r@&5zzH?e2r!|^+Tz5y!}Peb*rB+>czE1+DhPO5BNY1W@bh(~7oJdz%v');
define('LOGGED_IN_SALT',   'wn~.C%fO#K<#)[o=^}{qKSTvjaJ>451$hK|lcfsV++R`cnnZ5dLZur=cg+dpfm7Q');
define('NONCE_SALT',       '|3r$hF(VIq`k,+$%/8!!#A~3h-WH=mpCwZqp&V1XIl9B#sUrt>yJGW(WBRr0S,ki');
/**#@-*/

/**
 * WordPress データベーステーブルの接頭辞
 *
 * それぞれにユニーク (一意) な接頭辞を与えることで一つのデータベースに複数の WordPress を
 * インストールすることができます。半角英数字と下線のみを使用してください。
 */
$table_prefix  = 'wp_';

/**
 * ローカル言語 - このパッケージでは初期値として 'ja' (日本語 UTF-8) が設定されています。
 *
 * WordPress のローカル言語を設定します。設定した言語に対応する MO ファイルが
 * wp-content/languages にインストールされている必要があります。例えば de_DE.mo を
 * wp-content/languages にインストールし WPLANG を 'de_DE' に設定することでドイツ語がサポートされます。
 */
define('WPLANG', 'ja');


/**
 * 開発者へ: WordPress デバッグモード
 *
 * この値を true にすると、開発中に注意 (notice) を表示します。
 * テーマおよびプラグインの開発者には、その開発環境においてこの WP_DEBUG を使用することを強く推奨します。
 *
 * その他のデバッグに利用できる定数については Codex をご覧ください。
 *
 * @link http://wpdocs.osdn.jp/WordPress%E3%81%A7%E3%81%AE%E3%83%87%E3%83%90%E3%83%83%E3%82%B0
 */
define('WP_DEBUG', false);


/**
 * マルチサイト機能を有効にする
 */
define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'www.takahama428.com');
define('PATH_CURRENT_SITE', '/app/WP/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/* 編集が必要なのはここまでです ! WordPress でブログをお楽しみください。 */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
define( 'RELOCATE', true );