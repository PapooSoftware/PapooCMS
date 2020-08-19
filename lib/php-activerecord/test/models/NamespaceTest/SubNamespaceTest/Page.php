<?php
namespace NamespaceTest\SubNamespaceTest;

use ActiveRecord\Model;

class Page extends Model
{
	static $belong_to = array(
		array('book', 'class_name' => '\NamespaceTest\Book'),
	);
}