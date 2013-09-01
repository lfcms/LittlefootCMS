<?php

/*

public functions are the controllers
private functions are the models
view loads at the end

*/
/*
class subscribe
{
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	
	public function __construct($request, $dbconn, $ini)
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->ini = $ini;
	}
	
	public function main()
	{
		echo 'clients page';
		echo '<a href=""></a>';
	}
}*/

class pay extends app
{	
	function __construct($request, $dbconn)
	{
		parent::__construct($request, $dbconn);
		
		$this->email = 'moopd_1356924277_biz@bioshazard.com ';
		
		if(!isset($_SESSION['subscribe_pay'])) 
			$_SESSION['subscribe_pay'] = array('id' => 0);
		$this->payauth = $_SESSION['subscribe_pay'];
		
		if($this->payauth['id'] > 0)
			echo '
				<h2><a href="%appurl%">Client Portal</a></h2>
				<p>Hello '.$this->payauth['name'].',</p>';
	}
	
	function __destruct()
	{
		$_SESSION['subscribe_pay'] = $this->payauth;
	}
	
	public function main($vars)
	{
		$pay = $this->payauth;
		
		if($pay['id'] == 0)
		{
			echo '
				<h2>Client Portal Login</h2>
				<form action="%appurl%auth/" method="post">
					<ul>
						<li><input type="text" name="email" placeholder="email" /></li>
						<li><input type="password" name="pass" placeholder="password" /></li>
						<li><input type="submit" value="Log In" /></li>
					</ul>
				</form>
			';
			return;
		}
		
		
		$invoices = $this->db->fetchall("
			SELECT p.id, p.title, p.amount, p.cycle, i.due, i.status, i.id as inv_id
			FROM subscribe_pay p
			LEFT JOIN subscribe_invoice i
				ON p.id = i.plan_id
			WHERE p.client_id = ".$this->payauth['id']."
			AND i.status = 'open'
		");
		
		if($invoices == array())
		{
			echo '<p>No open invoices found.</p>';
		}
		else
		{
			echo '<p>Here are your open invoices:</p>';
			echo '<ul>';
			foreach($invoices as $sub)
			{
				echo '<li>';
				echo $sub['title'].' - '.$sub['cycle'].' @ $'.$sub['amount'];
				echo ' <a href="%appurl%pay/'.$sub['inv_id'].'/">Pay invoice</a></li>';
			}
			echo '</ul>';
		}
		
		$subscriptions = $this->db->fetchall('
			SELECT id, title, amount, cycle
			FROM subscribe_pay
			WHERE client_id = '.intval($pay['id']).'
			ORDER BY title
		');
		
		echo '<p>Here are your plans:</p>';
		
		//print_r($subscriptions);
		echo '<ul>';
		foreach($subscriptions as $sub)
		{
			echo '<li>';
			echo '<a href="%appurl%invoices/'.$sub['id'].'/">'.$sub['title'].'</a> - '.$sub['cycle'].' @ $'.$sub['amount'];
			echo '</li>';
		}
		echo '</ul>';
	}
	
	public function auth($vars)
	{
		$pay = $this->payauth;
		
		if($pay['id'] == 0 && isset($_POST['email']))
		{
			
			$subscriptions = $this->db->fetch("
				SELECT id, name, email
				FROM subscribe_clients
				WHERE 
					email = '".mysql_real_escape_string($_POST['email'])."'
					AND pass = '".sha1($_POST['pass'])."'
				LIMIT 1
			");
			
			if($subscriptions != array())
				$this->payauth = $subscriptions;
		}
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function invoices($vars)
	{
		$pay = $this->payauth;
		
		if($pay['id'] == 0)
		{
			echo '
				<h2>Client Portal Login</h2>
				<form action="%appurl%auth/" method="post">
					<ul>
						<li><input type="text" name="email" placeholder="email" /></li>
						<li><input type="password" name="pass" placeholder="password" /></li>
						<li><input type="submit" value="Log In" /></li>
					</ul>
				</form>
			';
			return;
		}
		
		$subscriptions = $this->db->fetchall("
			SELECT p.id, p.title, p.amount, p.cycle, i.due, i.status, i.id as inv_id
			FROM subscribe_pay p
			LEFT JOIN subscribe_invoice i
				ON p.id = i.plan_id
			WHERE p.client_id = ".$this->payauth['id']."
			AND i.plan_id = ".intval($vars[1])."
		");
		
		echo '<p>Invoice history:</p>';
		echo '<ul>';
		foreach($subscriptions as $sub)
		{
			echo '<li>';
			echo $sub['due'].' :: ';
			echo $sub['title'].' - '.$sub['cycle'].' @ $'.$sub['amount'];
			
			if($sub['status'] == 'Open' || $sub['status'] == 'Late')
				echo ' [<a href="%appurl%pay/'.$sub['inv_id'].'/">Pay invoice</a>]';
			else
				echo ' ('.$sub['status'].')';
				
			echo '</li>';
		}
		echo '</ul>';
	}
	
	public function pay($vars)
	{
		$pay = $this->payauth;
		
		if($pay['id'] == 0)
		{
			echo '
				<h2>Client Portal Login</h2>
				<form action="%appurl%auth/" method="post">
					<ul>
						<li><input type="text" name="email" placeholder="email" /></li>
						<li><input type="password" name="pass" placeholder="password" /></li>
						<li><input type="submit" value="Log In" /></li>
					</ul>
				</form>
			';
			return;
		}
		
		$subscription = $this->db->fetch("
			SELECT p.id, p.title, p.amount, p.cycle, i.due, i.status, i.id as inv_id
			FROM subscribe_pay p
			LEFT JOIN subscribe_invoice i
				ON p.id = i.plan_id
			WHERE p.client_id = ".$this->payauth['id']."
			AND i.id = ".intval($vars[1])."
			LIMIT 1
		");
		
		if($subscription)
		{
			if(isset($_GET['action']))
			{
				if($_GET['action'] == 'success')
				{
					echo 'Invoice paid!';
					
					$invoice = file_get_contents('model/invoice_'.intval($subscription['inv_id']).'.txt');
					$invoice = json_decode($invoice);
					unlink('model/invoice_'.intval($subscription['inv_id']).'.txt');
					
					if($invoice->mc_gross != $subscription['amount'])
					{
						echo 'Invalid payment amount detected. Canceling Payment...';
						return;
					}
					
					// pay it
					$sql = "
						UPDATE subscribe_invoice SET
							status = '".$invoice->payment_status."', 
							txn_id = '".$invoice->txn_id."'
						WHERE id = ".intval($subscription['inv_id'])."
					";
					$this->db->query($sql);
					
					
					echo '<pre>';
					print_r($invoice);
					echo '</pre>';
				}
					
				if($_GET['action'] == 'cancel')
					echo 'Payment cancled!';
					
				return;
			}
		
			require_once('model/paypal.class.php'); // include the class file

			$p             = new paypal_class; // initiate an instance
			$p->paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr"; //test url
			
			$ipn = 'http://'.$_SERVER['SERVER_NAME'].$this->request->relbase.'lf/apps/subscribe/model/ipn.php';
			// if no action variable, set 'process' as default action
			
			
			$script   = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			
			
			$p->add_field('business', $this->email); // 356924342 - asdf_1356922824_per@bioshazard.com
			$p->add_field('return', $script . '?action=success');
			$p->add_field('cancel_return', $script . '?action=cancel');
			$p->add_field('notify_url', $ipn);
			$p->add_field('item_name', $subscription['title']); // 'ITEM NAME'
			$p->add_field('item_number', $subscription['inv_id']); // 'ITEM ID'
			$p->add_field('amount', $subscription['amount']); // 'ITEM AMOUNT'
			$p->add_field('currency_code', 'USD'); //CURRENCY VALUE USD/EUR…
			$p->submit_paypal_post(); // submit the fields to paypal
		} else echo 'Invalid Invoice';
	}
}

?>