<?php

class hq2 extends app
{
	public function main($vars)
	{
		echo 'asdf';
		chdir('../blog');
		echo $this->request->apploader('blog', 'inst=HQ');
	}
}

?>