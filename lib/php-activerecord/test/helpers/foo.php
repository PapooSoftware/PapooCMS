<?php

namespace foo\bar\biz;

use ActiveRecord\Model;

class User extends Model {
	static $has_many = array(
		array('user_newsletters'),
		array('newsletters', 'through' => 'user_newsletters')
	);

}

class Newsletter extends Model {
	static $has_many = array(
		array('user_newsletters'),
		array('users', 'through' => 'user_newsletters'),
	);
}

class UserNewsletter extends Model {
	static $belong_to = array(
		array('user'),
		array('newsletter'),
	);
}