<?php 

/**
 * @ignore
 */
class dashboard
{
	private $pwd;
	private $simple = false;
	
	public function init()
	{
		$this->pwd = ROOT.'apps/';
		
		// if simple cms is enabled, load the select app's admin instead of the usual nav interface
		if($this->lf->settings['simple_cms'] != '_lfcms')
		{
			$cwd = getcwd();
			chdir(ROOT.'apps/'.$this->lf->settings['simple_cms']);
			
			echo '<div class="dashboard_manage">';
			if(is_file('admin.php')) 
				include 'admin.php';
			else 
				echo 'No Admin';
			echo '</div>';
			
			chdir($cwd);
			$this->simple = true;
		}
	}
	
	public function main()
	{
		
		if($this->simple) return;
		
		$this->updatenavcache(); // idk if this needs to be here lol
		
		/*
		
		$install = extension_loaded('zip') 
			? '<input type="submit" value="Upload" /> <span>('.ini_get('upload_max_filesize').' Upload Limit)</span>'
			: "<strong>Error: PHP Zip Extension missing.</strong>";
		
		*/
		
		include 'model/dashboard.main.php';
		
		ob_start();
		
		include 'view/dashboard.main.php';
		
		echo str_replace('%subalias%', $this->subalias, ob_get_clean());
		
		
	}
	
	public function wysiwyg($vars)
	{
		
		echo '<p>Return to <a href="%baseurl%dashboard/main/'.$vars[1].'/#nav_'.$vars[1].'">dashboard</a></p>';
		echo '<h2>WYSIWYG</h2>';
		
		$save = (new LfActions)->getById($vars[1]);
		
		//$thelink = $this->links[$save['id']][0];
		$thelink = (new LfLinks)->getById($vars[1]);
		
		include 'view/dashboard.wysiwyg.php';
	}

	public function preview($vars)
	{
		
		$action = (new LfActions)->findById($vars[1]);
		$links = (new LfLinks)->findByInclude($vars[1]);
		
		
		$skin = $action->template;
		if($skin == 'default')
			$skin = $this->lf->settings['default_skin'];
		
		ob_start();
		readfile(LF.'skins/'.$skin.'/index.php');
		$template = ob_get_clean();
		
		ob_start();
		include LF.'cache/nav.cache.html';
		$nav = ob_get_clean();
		
		$content = '<h2>%content%</h2>';
		
		
		//pre($links->result);
		
		$content .= implode(', ', $action->get()).'<br />';
		
		foreach($links->result as $row)
		{
			$content .= implode(', ', $row).'<br />';
		}
		
		
		
		$content .= '
			<div class="row">
				<div class="col-4">Add new app:</div>
				<div class="col-4">
					<select name="" id="">
						<option value="">App1</option>
					</select>
				</div>
				<div class="col-4">
					<input type="submit" />
				</div>
			</div>
		 
		
		';
		
		
		
		$template = str_replace(
			array(
				'%content%',
				'%skinbase%',
				'%nav%',
				'%baseurl%',
				'</head>'
			),
			array(
				$content,
				$this->lf->wwwInstall.'lf/skins/'.$skin,
				$nav,
				$this->lf->wwwAdmin.'dashboard/preview/'.$vars[1].'/',
				'<link rel="stylesheet" href="'.$this->lf->relbase.'lf/system/lib/lf.css" /><link rel="stylesheet" href="'.$this->lf->relbase.'lf/system/lib/3rdparty/icons.css" /></head>'
			),
			$template
		);
		
		echo $template;
		
		exit();
	}
	
	public function linkapp($vars)
	{	
		if($this->simple) return;
		
		if(!isset($vars[1])) return 'invalid arguement';
		
		$pwd = LF.'/apps/';
		
		// address conflicting alias names.
		$result = $this->db->fetchall("
			SELECT *
			FROM lf_actions
			WHERE parent = '-1'
				AND (
					alias = '".$this->db->escape($vars[1])."'
					OR alias LIKE '".$this->db->escape($vars[1])."_%'
				)
			ORDER by alias ASC
		");
		
		// add _# to ensure unique alias
		$alias = $vars[1];
		if($result[0]['alias'] == $vars[1])
			$alias .= '_'.(count($result));
		
		// no form, just make and redirect
		$_POST = array(
			'alias' => $alias,
			'parent' => -1,
			'args' => '',
			'app' => $vars[1]
		);
		
		$id = $this->create($vars);
		
		redirect302($this->request->appurl.'main/'.$id.'#nav_'.$id);
	}
	
	public function create($vars) // nav/item create
	{
		if($this->simple) return;
		
		if(!isset($_POST['title'])) // if simple post, auto-set other settings
		{
			if($_POST['alias'] == '') $_POST['alias'] = 'Home';
			
			$_POST['title'] = ucwords($_POST['alias']);
			$_POST['label'] = ucwords($_POST['alias']);
			$_POST['position'] = 9999; // it will auto adjust to the last position below
			$_POST['isapp'] = 'off'; // is not an app by default
			$_POST['template'] = 'default';
		}
		
		/* -=-=-=-=- Add Nav Item -=-=-=-=- */
		$pos = intval($_POST['position']);
		
		if($pos != 0)
		{
			$sql = 'SELECT COUNT(id) as pos FROM lf_actions WHERE parent = '.$this->db->escape($_POST['parent']).' AND position != 0';
			$result = $this->db->query($sql);
			$row = $this->db->fetch($result);
			
			if($row['pos'] >= $pos)
				$this->db->query('UPDATE lf_actions SET position = position + 1 WHERE parent = '.$this->db->escape($_POST['parent']).' AND position >= '.$pos);
			else
				$pos = $row['pos'] + 1;
		}
		/*
		echo '<pre>';
		print_r($_POST);
		print_r($pos);
		echo '</pre>';*/
		
		
		
		//echo $sql;
		
		//echo $sql;
		//exit();
		
		if(!isset($_POST['section'])) // simple link
		{
			$_POST['section'] = 'content';
		}
		
		$this->updatenavcache();
		
		/* -=-=-=-=- Add Link to Nav -=-=-=-=- */
		$pwd = LF.'/apps';
		foreach(scandir($pwd) as $file)
		{
			if($file == '.' || $file == '..') 
				continue;

			if(is_file($pwd.'/'.$file.'/index.php'))
				$app_filter[$file] = $file;
		}
		
		if(isset($app_filter[$_POST['app']]))
			$app = $app_filter[$_POST['app']];
		else
			exit();
		
		// ^ link was valid, move on to running the sql
		
		$insert = array(
			"parent"	=> $this->db->escape($_POST['parent']),
			"position"	=> $pos,
			"alias"		=> $this->db->escape($_POST['alias']),
			"title"		=> $this->db->escape($_POST['title']),
			"label"		=> $this->db->escape($_POST['label']),
			"app"		=> $_POST['isapp'] == 'on' ? '1' : '0',
			"template"	=> $this->db->escape($_POST['template'])
		);
		
		$id = (new LfActions)->insertArray($insert);
		
		//$recurse = $_POST['recursive'] == 'on' ? 1 : 0;
		$insert = array(
			"include"	=> $id,
			"app"		=> $app,
			"ini"		=> $this->db->escape($_POST['ini']),
			"section"	=> $this->db->escape($_POST['section']),
			"recursive"	=> 0
		);
		
		(new LfLinks)->insertArray($insert);
		
		if($vars[0] == 'create')
			// redirect them after this completes
			redirect302($this->request->base.'apps/');
		else
			return $id;
	}
	
	private function deleteAll($directory, $empty = false)
	{
		if($this->simple) return;
		
		if(substr($directory,-1) == "/") {
			$directory = substr($directory,0,-1);
		}

		if(!file_exists($directory) || !is_dir($directory)) {
			return false;
		} elseif(!is_readable($directory)) {
			return false;
		} else {
			$directoryHandle = opendir($directory);
		   
			while ($contents = readdir($directoryHandle)) {
				if($contents != '.' && $contents != '..') {
					$path = $directory . "/" . $contents;
					
					if(is_dir($path)) {
						$this->deleteAll($path);
					} else {
						unlink($path);
					}
				}
			}
		   
			closedir($directoryHandle);

			if($empty == false) {
				if(!rmdir($directory)) {
					return false;
				}
			}
		   
			return true;
		}
	}	
	
	public function rm($vars)
	{
		if($this->simple) return;
		
		// get current position/parent
		$current = $this->db->fetch('SELECT position, parent FROM lf_actions WHERE id = '.intval($vars[1]));
		
		if(isset($current['parent']))
		{
			$this->db->query('DELETE FROM lf_actions WHERE id = '.intval($vars[1]));
			$this->db->query('DELETE FROM lf_links WHERE include = '.intval($vars[1]));
			
			// update positions of all item behind the rm'd sibling
			if($current['position'] > 0)
				$this->db->query('UPDATE lf_actions SET position = position - 1 WHERE parent = '.$current['parent'].' AND position > '.$current['position']);
			
			// find all orphaned nav items and remove them, 
			// loop until all are cleared
			while(true) 
			{
				$result = $this->db->query('
					SELECT a.id	FROM `lf_actions` a 
					LEFT JOIN lf_actions b ON a.parent = b.id
					WHERE b.id IS NULL AND a.parent != -1
				');
				
				if($this->db->numrows() == 0) 
					break;
				
				$orphans = array();
				while($row = $this->db->fetch())
					$orphans[] = $row['id'];
					
				$this->db->query('DELETE FROM lf_actions WHERE id IN ('.implode(',', $orphans).')');
				$this->db->query('DELETE FROM lf_links WHERE include IN ('.implode(',', $orphans).')');
			}
		}
		
		$this->updatenavcache();
		redirect302();
	}
	
	public function delapp($var)
	{
		if($this->simple) return;
		
		$success = preg_match('/[a-z]+/', $var[1], $matches);
		
		if(!$success) return 0;
		
		$app = $this->pwd.$matches[0];
		if(is_dir($app))
			$this->deleteAll($app);
		
		redirect302();
	}
	
	public function manage($var)
	{
		// backward compatible
		redirect302($this->lf->base.'apps/'.$var[1]);
	}
	
	public function download($var)
	{
		if($this->simple) return;
		
		$apps = file_get_contents('http://littlefootcms.com/files/download/apps/apps.txt');
		$apps = array_flip(explode("\n",$apps,-1));
		$files = array_flip(scandir(ROOT.'apps'));
		
		include 'view/dashboard.download.php';
	}
	
	public function getappfromnet($vars)
	{
		if($this->simple) return;
		
		$apps = file_get_contents('http://littlefootcms.com/files/download/apps/apps.txt');
		$apps = array_flip(explode("\n",$apps,-1));
		
		if(isset($apps[$vars[2]]))
		{
			print_r($apps[$vars[2]]);
			exit();
		}
		else if(isset($apps[$vars[1]]))
		{
			$files = array_flip(scandir(ROOT.'apps'));
			
			if(isset($files[$vars[1]]))
				return 'App already downloaded: '.$vars[1];
			
			$file = 'http://littlefootcms.com/files/download/apps/'.$vars[1].'.zip';
			$dest = ROOT.'apps/'.$vars[1].'.zip';
			echo $file.'<br />';
			echo $dest.'<br />';
			
			// download and unzip into apps/
			downloadFile($file, $dest);
			Unzip( ROOT.'apps/', $vars[1].'.zip' );
			unlink($dest);
			
			$this->installsql($vars[1]);
			
		} else echo "App not found: ".$vars[1];
		
		redirect302();
	}
	
	public function install($vars)
	{
		// this has been deprecated for now. kinda works... kinda doesnt...
		redirect302();
		
		if($this->simple) return;
		
		preg_match('/^([_\-a-zA-Z0-9]+)\.(zip|tar\.gz)/', $_FILES['app']['name'], $match);
		
		if($match[2] != 'zip') return;
		//if($_FILES['app']['type'] != 'application/zip') return;
		if($_FILES['app']['size'] > 55000000) return;
				
		$target =  $this->pwd.$match[1];
		
		if(is_dir($target)) return;
		if(!mkdir($target)) return;
		
		if(!move_uploaded_file($_FILES['app']['tmp_name'], $target.'/install.zip')) 
		{ 
			echo "Sorry, there was a problem uploading your file."; 
			return; 
		}
		else
		{
			//echo "The file ". $match[0]. " has been uploaded";
			$zip = zip_open($target.'/install.zip');
			if($zip)
			{
				while ($zip_entry = zip_read($zip)) { 
				
					if(preg_match('/^(.+)\/$/', zip_entry_name($zip_entry), $file))
					{
						if(!mkdir($target.'/'.$file[1]))
						{
							echo "fail";
						}
					}		
					else if(zip_entry_open($zip, $zip_entry, "r"))
					{
						$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
						
						$fp = fopen($target.'/'.zip_entry_name($zip_entry), "w");
						fwrite($fp,"$buf");
						zip_entry_close($zip_entry);
						fclose($fp);
					}
				}
				zip_close($zip);
				unlink($target.'/install.zip');
				$this->installsql($match[1]);
			}
		}
		
		redirect302();
	}

	public function update($vars) // nav/item update
	{
		if($this->simple) return;
				
		$post = $_POST;
		
		// save, unset ini
		$id = intval($post['id']);
		$ini = $this->db->escape($post['ini']);
		unset($post['id'], $post['ini']);
		if($post['position'] <= 0) 
		{
			$post['position'] = 0;
			$post['parent'] = -1;
		}
		
		//select current children id's and positions
		$old = $this->db->fetch('SELECT position, parent FROM lf_actions WHERE id = '.$id);
		
		// get # of children of destination parent
		$result = $this->db->fetch('SELECT COUNT(id) as count FROM lf_actions WHERE parent = '.$this->db->escape($post['parent']));
		$count = $result['count'];
		
		// handle parent/position updates
		if($post['parent'] != $old['parent']) // parent updated
		{
			if($post['position'] > $count + 1) // cant be further down than last
				$post['position'] = $count + 1;
				
			// make room in destination parent children: update pos++ where pos > dest[pos]
			if($post['position'] != 0)
			$this->db->query("UPDATE lf_actions SET position = position + 1 WHERE parent = ".intval($post['parent'])." AND position >= ".intval($post['position']));
			
			// move into new parent: update parent = dest[parent] where id = old[id]
			$this->db->query("UPDATE lf_actions SET parent = ".intval($post['parent']).", position = ".intval($post['position'])." WHERE id = ".$id);
			
			// close gap left behind: update pos-- where pos > dest[pos]
			if($old['position'] != 0)
			$this->db->query("UPDATE lf_actions SET position = position - 1 WHERE parent = ".$old['parent']." AND position > ".$old['position']);
		}
		else if($post['position'] != $old['position']) // if moving within current siblings
		{
			if($post['position'] > $count) // cant be further down than last
				$post['position'] = $count;
				
			if($old['position'] == 0) // starting from 0
				$this->db->query('
					UPDATE lf_actions SET position = position + 1 
					WHERE parent = '.$old['parent'].' AND position >= '.intval($post['position'])); // make room for new item
					
			else if($post['position'] == 0) // going to 0
				$this->db->query('
					UPDATE lf_actions SET position = position - 1 
					WHERE parent = '.$old['parent'].' AND position > '.intval($old['position'])); // make room for new item
					
			else if($post['position'] < $old['position']) // moving lower
				$this->db->query('
					UPDATE lf_actions SET position = position + 1 
					WHERE parent = '.$old['parent'].' 
					AND position >= '.intval($post['position']).' 
					AND position < '.$old['position']);
					
			else if($post['position'] > $old['position']) // moving higher
				$this->db->query('
					UPDATE lf_actions SET position = position - 1 
					WHERE parent = '.$old['parent'].' 
					AND position <= '.intval($post['position']).' 
					AND position > '.$old['position']);
			
			// move to place
			$this->db->query("UPDATE lf_actions SET position = ".intval($post['position'])." WHERE id = ".$id);
		}

		// This has already been taken care of above
		unset($post['position'], $post['parent']);
	
		// other data updates that only affect the item itself
		if(!isset($post['section']))
		{
			$update = array();
			foreach($post as $key => $var)
				$update[$key] = $this->db->escape($key)." = '".$this->db->escape($var)."'";
			
			// Move the item
			$sql = "UPDATE lf_actions SET ".implode(', ', $update)." WHERE id = ".$id;
			$this->db->query($sql);
			
			// update ini
			$sql = "UPDATE lf_links SET ini = '".$ini."' WHERE include = ".$id;
			$this->db->query($sql);
		}
		else
		{
			/*$update = array();
			foreach($post as $key => $var)
				$update[$key] = $this->db->escape($key)." = '".$this->db->escape($var)."'";*/
				
			//$post['isapp'] = $post['isapp'] == 'on' ? '1' : '0';
			$update = array(
//				"parent = 	'".$this->db->escape($post['parent'])."'",
//				"position = ".intval($post['position']),
				"alias = 	'".$this->db->escape($post['alias'])."'",
				"title = 	'".$this->db->escape($post['title'])."'",
				"label = 	'".$this->db->escape($post['label'])."'",
				//"app = 		'".$post['isapp']."'",
				"template = '".$this->db->escape($post['template'])."'"
			);
			
			// Move the item
			$sql = "UPDATE lf_actions SET ".implode(', ', $update)." WHERE id = ".$id;
			$this->db->query($sql);
			
			$update = array(
				"app = 	'".$this->db->escape($post['app'])."'",
				"ini = 	'".$ini."'",
				"section = 	'".$this->db->escape($post['section'])."'"
			);
			
			// Move the item
			$sql = "UPDATE lf_links SET ".implode(', ', $update)." WHERE include = ".$id;
				
			/*pre($post);
			pre($update);
			pre(implode(', ', $update));
			echo $sql;*/
		
			//echo $sql;
			$this->db->query($sql);
		}
		$this->updatenavcache();
		
		if(strpos($_SERVER['HTTP_REFERER'],'wysiwyg') !== false)
			redirect302();
			
		redirect302($this->request->appurl.'main/'.$id.'#nav_'.$id);
	}
	
	public function updatenavcache()
	{
		if($this->simple) return;
		
		include 'model/apps.navcache.php';
		
		// Grab all possible actions
		$actions = (new \lf\orm)->fetchall("SELECT * FROM lf_actions WHERE position != 0 ORDER BY ABS(parent), ABS(position) ASC");
		
		// Make a matrix sorted by parent and position
		$menu = array();
		foreach($actions as $action)
			$menu[$action['parent']][$action['position']] = $action;
		
		$nav = build_nav_cache($menu);
		if(!is_dir(ROOT.'cache')) mkdir(ROOT.'cache', 0755, true); // make if not exists
		file_put_contents(ROOT.'cache/nav.cache.html', $nav);
	}
}