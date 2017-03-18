<?php

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
	public function testResolveAnon()
	{
		$expectedOut = 'TEST|Anonymous|TSET';
		
		$replaceIn = 'TEST|{user:0}|TSET';
		$resolvedIdString = (new \lf\user)->resolveIds($replaceIn);
		
		$this->assertEquals($resolvedIdString, $expectedOut);
	}
	
	public function testCreateAnon()
	{
		$anonUser = (new \lf\user);
		stderr($anonUser->getuser());
		$display_name = $anonUser->getdisplay_name();
		$this->assertEquals($display_name, 'Anonymous');
	}
	
	public function testCreateNewUser()
	{
		// I use this exact process to create a user at first installation
		// Making this test finally leaves me certain that it works lol
		$newUser = (new \lf\user)
			->setDisplay_name('First L.')
			->setEmail('fak3@j098gj[0349hg.com')
			->setUser('zer0cool')
			->setPass('hunter2')
			->setStatus('valid')
			->setAccess('admin')
			->save()
			->toSession(); // and auto login as that new user
		
		
		// Test `->save()`
		$savedUser = (new \orm\lf_users)->getById( $newUser->getId() );
		$savedName = $savedUser['display_name'];
		$newUserName = $newUser->getDisplay_name();
		$this->assertEquals($newUserName, $savedName);
		
		$sessionUser = (new \lf\user)->fromSession();
		
		
		stderr($_SESSION);
		
		// clean up after yourself
		(new \LfUsers)->deleteById($newUser->getId());
		
		// $display_name = $user->getdisplay_name();
		// $this->assertEquals($display_name, 'Anonymous');
	}
	
	public function testCreateddAnon()
	{
		$user = new \lf\user();
		$display_name = $user->getdisplay_name();
		$this->assertEquals($display_name, 'Anonymous');
	}
}