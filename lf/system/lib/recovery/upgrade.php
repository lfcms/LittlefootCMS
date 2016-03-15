<?php

$db = \lf\db::init();

// 1.13.5-r129
$index = <<<'EOF'
<?php

namespace lf;

require_once('lf/system/bootstrap.php');	// Bootstrap the littlefoot PHP suite.
$cms = new cms(); 						// Could have just done (new cms)->run();,
$cms->run();								// but I like to be able to catch if you are `PHP 5.3`.
EOF;
file_put_contents(ROOT.'../index.php', $index);

$acl = array();
$acl_user = $db->fetchall("SHOW COLUMNS FROM `lf_acl_user`");
foreach($acl_user as $user) $acl[] = $user['Field'];
if(in_array('nav_id', $acl)) $db->query('ALTER TABLE lf_acl_user DROP COLUMN nav_id');

$acl = array();
$acl_user = $db->fetchall("SHOW COLUMNS FROM `lf_acl_global`");
foreach($acl_user as $user) $acl[] = $user['Field'];
if(in_array('nav_id', $acl)) $db->query('ALTER TABLE lf_acl_global DROP COLUMN nav_id');

$db->query("UPDATE lf_users SET status = 'valid' WHERE status = 'online'");


// 1.13.5-r130

// update user table
$columns = array();
$cols = $db->fetchall("SHOW COLUMNS FROM lf_users");
foreach($cols as $col) $columns[] = $col['Field'];
if(in_array('salt', $columns)) $db->query('ALTER TABLE lf_users DROP COLUMN salt');
if(!in_array('hash', $columns)) $db->query('ALTER TABLE lf_users ADD hash VARCHAR( 40 ) NOT NULL');

// add settings
$rewrite = $db->fetch("SELECT * FROM lf_settings WHERE var = 'rewrite'");
if(!$rewrite) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'rewrite', 'off')");
$debug = $db->fetch("SELECT * FROM lf_settings WHERE var = 'debug'");
if(!$debug) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'debug', 'off')");
$url = $db->fetch("SELECT * FROM lf_settings WHERE var = 'force_url'");
if(!$url) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'force_url', '')");
$nav = $db->fetch("SELECT * FROM lf_settings WHERE var = 'nav_class'");
if(!$nav) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'nav_class', '')");
$simple = $db->fetch("SELECT * FROM lf_settings WHERE var = 'simple_cms'");
if(!$simple) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'simple_cms', '_lfcms')");

// for handling signup within system/
$signup = $db->fetch("SELECT * FROM lf_settings WHERE var = 'signup'");
if(!$signup) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'signup', 'disabled')");

// when plugins were introduced
$db->query('CREATE TABLE IF NOT EXISTS lf_plugins (
  id int(11) NOT NULL AUTO_INCREMENT,
  hook varchar(128) NOT NULL,
  plugin varchar(128) NOT NULL,
  status varchar(64) NOT NULL,
  config varchar(1024) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1');

// really need to add something here to make this interactive in case of a problem



?>

<h2><i class="fa fa-cog"></i> <a href="%appurl%">Settings</a></h2>
<div class="tile rounded">
	<div class="light_gray tile-header">
		<h3><i class="fa fa-cogs"></i> Upgrade</h3>
	</div>
	<div class="tile-content">
		<p>Upgrade complete. <a href="%appurl%">Go Back</a></p>
	</div>
</div>