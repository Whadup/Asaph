<?php

/* The Asaph class hosts all functions to select and process posts for
the front page.

To integrate Asaph within other systems, just define your ASAPH_PATH
and include this file. You can then create a new Asaph object and fetch 
the newest $numberOfPosts posts to an array.

$asaph = new Asaph( $numberOfPosts );
$asaphPosts = $asaph->getPosts( $pageToFetch ); */

require_once( ASAPH_PATH.'lib/asaph_config.class.php' );
require_once( ASAPH_PATH.'lib/db.class.php' );
date_default_timezone_set("Europe/Berlin");
class Asaph {
	protected $db = null;
	protected $postsPerPage = 0;
	protected $currentPage = 0;

	public function __construct( $postsPerPage = 25 ) {
		$this->postsPerPage = $postsPerPage;
		$this->db = new DB(
			Asaph_Config::$db['host'],
			Asaph_Config::$db['database'],
			Asaph_Config::$db['user'],
			Asaph_Config::$db['password']
		);
	}
	
	
	public function getPosts( $page ) {
		$this->currentPage = abs( intval($page) );
		
		$posts = $this->db->query( 
			'SELECT SQL_CALC_FOUND_ROWS
				UNIX_TIMESTAMP(p.created) as created, 
				p.id, p.source, p.description, p.image,p.video,p.quote, p.title, u.name AS user
			FROM 
				'.ASAPH_TABLE_POSTS.' p
			LEFT JOIN '.ASAPH_TABLE_USERS.' u 
				ON u.id = p.userId
			ORDER BY 
				created DESC
			LIMIT 
				:1, :2',
			$this->currentPage * $this->postsPerPage, 
			$this->postsPerPage
		);
		$this->totalPosts = $this->db->foundRows();
		
		foreach( array_keys($posts) as $i ) {
			$this->processPost( $posts[$i] );
		}
		
		return $posts;
	}
	
	public function getPost( $id ) {
		$post = $this->db->getRow( 
			'SELECT 
				UNIX_TIMESTAMP(p.created) as created, 
				p.id, p.source, p.description, p.image, p.title,p.video,p.quote, u.name
			FROM 
				'.ASAPH_TABLE_POSTS.' p
			LEFT JOIN '.ASAPH_TABLE_USERS.' u 
				ON u.id = p.userId
			WHERE 
				p.id = :1
			ORDER BY 
				created DESC',
			$id
		);
		if( empty($post) ) {
			return array();
		}
		$this->processPost( $post );
		return $post;
	}
	
	public function getPages() {
		$pages = array( 
			'current' => 1,
			'total' => 1,
			'prev' => false,
			'next' => false,
		);
		if( $this->totalPosts > 0 ) {
			$pages['current'] = $this->currentPage + 1;
			$pages['total'] = ceil($this->totalPosts / $this->postsPerPage );
			if( $this->currentPage > 0 ) {
				$pages['prev'] = $this->currentPage;
			}
			if( $this->totalPosts > $this->postsPerPage * $this->currentPage + $this->postsPerPage ) {
				$pages['next'] = $this->currentPage + 2;
			}
		}
		
		return $pages;
	}
	
	protected function queryImage($image,$datePath)
	{
		$query = 'SELECT SQL_CALC_FOUND_ROWS
				id, image, thumb, width, height
			FROM 
				'.ASAPH_TABLE_IMAGES.'
			WHERE 
				id = '.$image.';';
		$img = $this->db->query($query);
		$img = $img[0];
		$img['thumb'] = 
				Asaph_Config::$absolutePath
				.Asaph_Config::$images['thumbPath']
				.$datePath
				.$img['thumb'];
				
		$img['image'] = 
				Asaph_Config::$absolutePath
				.Asaph_Config::$images['imagePath']
				.$datePath
				.$img['image'];
		return $img;
	}
	
	protected function queryVideo($video)
	{
		$query = 'SELECT SQL_CALC_FOUND_ROWS
				id, src, width, height, type, thumb
			FROM 
				'.ASAPH_TABLE_VIDEOS.'
			WHERE 
				id = '.$video.';';
		$img = $this->db->query($query);
		$img = $img[0];
		return $img;
	}

	protected function queryQuote($quote)
	{
		$query = 'SELECT SQL_CALC_FOUND_ROWS
				id, quote,speaker
			FROM 
				'.ASAPH_TABLE_QUOTES.'
			WHERE 
				id = '.$quote.';';
		$img = $this->db->query($query);
		$img = $img[0];
		return $img;
	}

	protected function processPost( &$post ) {
		$urlParts = parse_url( $post['source'] );
		$datePath = date( 'Y/m/', $post['created'] );
		$post['sourceDomain'] = $urlParts['host'];
		$post['source'] = htmlspecialchars( $post['source'] );
		$post['title'] = htmlspecialchars( $post['title'] );
		$post['description'] = $post['description'];
		if( $post['image'])
		{
			$post['image'] = $this->queryImage($post['image'],$datePath);
		}
		if( $post['video'])
		{
			$post['video'] = $this->queryVideo($post['video']);
		}
		if( $post['quote'])
		{

			$post['quote'] = $this->queryQuote($post['quote']);
		}
		// TODO: Link & Quote
	}
}

?>