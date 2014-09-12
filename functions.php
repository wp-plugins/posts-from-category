<?php 
/*** Function limit the number of words ***/

function custom_limit_words($string, $word_limit) {

	$words = explode(' ', $string, ($word_limit + 1));

	if(count($words) > $word_limit) {

		if(count($words) > $word_limit) {

	    	array_pop($words);

	     	return implode(' ', $words).'...';
	 	}

	} else {  

		return $string;
	}
}	

	

	
