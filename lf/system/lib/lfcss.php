<?php

// this php framework generates lf.css HTML

/* eg,

echo 
row(
	col(6, 'asdf').
	col(6, 'asdf2')
).
row(
	col(12, 
		tile(
			tileHeader('blue light','<p>Some Header</p>').
			tileContent('<p>And some content</p>')
		)
	)
);

*/

function div($class, $content)
{
	return '<div class="'.$class.'">'.$content.'</div>'; 
}

function row($content = '', $class = NULL) 			
{ 
	$addClass = '';
	if(!is_null($class))
	{
		// if a class was provided, make sure it has a space in front
		$addClass = ' '.$class;
		
		// switch second arg to content. real sneaky ;)
		//$content = $class;
	}
	
	return div('row'.$addClass, $content);
}

function col($num, $content = '', $class = NULL) 			
{ 
	$addClass = '';
	if(!is_null($class))
	{
		// if a class was provided, make sure it has a space in front
		$addClass = ' '.$class;
		
		// switch second arg to content. real sneaky ;)
		//$content = $class;
	}
	
	return div('col-'.$num.$addClass, $content); 
}

function tile($content = '', $class = NULL) 			
{ 
	$addClass = '';
	if(!is_null($class))
	{
		// if a class was provided, make sure it has a space in front
		$addClass = ' '.$class;
		
		// switch second arg to content. real sneaky ;)
		//$content = $class;
	}
	
	return div('tile'.$addClass, $content); 
}


function tileHeader($content = '', $class = NULL) 			
{ 
	$addClass = '';
	if(!is_null($class))
	{
		// if a class was provided, make sure it has a space in front
		$addClass = ' '.$class;
		
		// switch second arg to content. real sneaky ;)
		//$content = $class;
	}
	
	return div('tile-header'.$addClass, $content); 
}

function tileContent($content = '', $class = NULL) 			
{ 
	$addClass = '';
	if(!is_null($class))
	{
		// if a class was provided, make sure it has a space in front
		$addClass = ' '.$class;
		
		// switch second arg to content. real sneaky ;)
		//$content = $class;
	}
	
	return div('tile-content'.$addClass, $content); 
}

function tileFooter($content = '', $class = NULL) 			
{ 
	$addClass = '';
	if(!is_null($class))
	{
		// if a class was provided, make sure it has a space in front
		$addClass = ' '.$class;
		
		// switch second arg to content. real sneaky ;)
		//$content = $class;
	}
	
	return div('tile-footer'.$addClass, $content); 
}

// and so on

/* 

//dont think i need this, 
//but really wish PHP had magic functions as with methods

class lfcss
{
	public function __construct()
	{
		
	}
	
	public function __call($method, $args)
	{
		pre($method);
		pre($args);
		//echo '<div class="row">'.$content.'</div>'; 
		
		return $this;
	}
}*/