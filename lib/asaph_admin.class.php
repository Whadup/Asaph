<?php

/* The Asaph_Admin class extends the Asaph class to allow modification
of posts and users */

require_once( ASAPH_PATH.'lib/asaph.class.php' );

class Asaph_Admin extends Asaph {
	protected $userId = null;
	protected $cookieName = 'asaphAdmin';
	
	public function __construct( $postsPerPage = 25 ) {
		parent::__construct( $postsPerPage );
		
		$this->userId = $this->getUserId();
	}
	
	
	public function checkLogin() {
		return !empty( $this->userId );
	}
	
	
	public function login( $name, $pass ) {
		$user = $this->db->getRow( 
			'SELECT id FROM '.ASAPH_TABLE_USERS.' WHERE name = :1 AND pass = :2',
			$name, md5( $pass )
		);
		
		if( empty($user) ) {
			return false;
		}
		
		$this->userId = $user['id'];
		$loginId = md5(uniqid(rand()));
		setcookie( $this->cookieName, $loginId, time() + 3600 * 24 * 365 );
		
		$this->db->updateRow( 
			ASAPH_TABLE_USERS, 
			array( 'id' => $this->userId ), 
			array( 'loginId' => $loginId ) 
		);
		
		return true;
	}
	
	
	public function logout() {
		if( empty($_COOKIE[$this->cookieName]) ) {
			return false;
		}
		
		$this->db->updateRow( 
			ASAPH_TABLE_USERS, 
			array('loginId' => $_COOKIE[$this->cookieName] ), 
			array('loginId' => '') 
		);
		$this->userId = null;
		
		return true;
	}
	
	
	protected function getUserId() {
		if( empty($_COOKIE[$this->cookieName]) ) {
			return null;
		}
		
		$user = $this->db->getRow( 
			'SELECT id FROM '.ASAPH_TABLE_USERS.' WHERE loginId = :1', 
			$_COOKIE[$this->cookieName] 
		);
		
		return empty( $user ) ? null : $user['id'];
	}

	public function updateQuote($id,$quote,$speaker)
	{
		$data = array('quote' => $quote, 'speaker' => $speaker);
				
		$this->db->updateRow( 
					ASAPH_TABLE_QUOTES,
					array( 'id' => $id ),
					$data
				);
	}

	public function updatePost( $id, $created, $source, $title, $description ) {
		$data = array( 
			'source' => $source,
			'title' => $title,
			'description' => $description
		);
		
		// Valid date given (YYYY-MM-DD)?
		if( 
			preg_match('/^\d{4}.\d{2}.\d{2}.+\d{2}.\d{2}$/', $created) && 
			strtotime($created) 
		) {
			$data['created'] = $created;
			
			$initial = $this->getPost($id);
			
			// OK, this sucks hard. If the date changed, we may have to move the thumb and image
			// into another path and make sure to not overwrite any other imagess.
			$initialPath = date( 'Y/m', $initial['created'] );
			$newPath = date( 'Y/m', strtotime($created) );
			if( $initialPath != $newPath && !empty($initial['image']) ) {
				$newImageDir = ASAPH_PATH.Asaph_Config::$images['imagePath'].$newPath;
				$newThumbDir = ASAPH_PATH.Asaph_Config::$images['thumbPath'].$newPath;
				$newImageName = $this->getUniqueFileName( $newImageDir, basename($initial['image']['image'] ));
				$newThumbName = $this->getUniqueFileName( $newThumbDir, basename($initial['image']['thumb'] ));

				$initialImagePath = ASAPH_PATH.Asaph_Config::$images['imagePath'].$initialPath.'/'.basename($initial['image']['image']);
				$initialThumbPath = ASAPH_PATH.Asaph_Config::$images['thumbPath'].$initialPath.'/'.basename($initial['image']['thumb']);
				$newImagePath = $newImageDir.'/'.$newImageName;
				$newThumbPath = $newThumbDir.'/'.$newThumbName;
				
				echo $newImageDir."<br/>";
				echo $newImageName."<br/>";
				echo $initialImagePath."<br/>";
				echo $newImagePath."<br/>";

				$imageData = array();
				$imageData ['image'] = $newImageName;
				$imageData ['thumb'] = $newThumbName;
				
				$this->db->updateRow( 
							ASAPH_TABLE_IMAGES,
							array( 'id' => $initial['image']['id'] ),
							$imageData
						);

				if( 
					!$this->mkdirr($newImageDir) ||
					!$this->mkdirr($newThumbDir) ||
					!@rename($initialImagePath, $newImagePath) ||
					!@rename($initialThumbPath, $newThumbPath)
				) {
					return false;
				}
			}
		}
		
		$this->db->updateRow( 
			ASAPH_TABLE_POSTS,
			array( 'id' => $id ),
			$data
		);
		return true;
	}
	
	
	public function deletePost( $id ) {
		$post = $this->getPost($id);
		
		// Delete thumbnail an image from disk
		if( !empty($post['image']) ) {
			@unlink($post['image']['image']);
			@unlink($post['image']['thumb']);
			$this->db->query( 'DELETE FROM '.ASAPH_TABLE_IMAGES.' WHERE id = :1', $post['image']['id']);
		}
		if( !empty($post['video']) ) {
			$this->db->query( 'DELETE FROM '.ASAPH_TABLE_VIDEOS.' WHERE id = :1', $post['video']['id']);
		}
		if( !empty($post['quote']) ) {
			$this->db->query( 'DELETE FROM '.ASAPH_TABLE_QUOTES.' WHERE id = :1', $post['quote']['id']);
		}
		$this->db->query( 'DELETE FROM '.ASAPH_TABLE_POSTS.' WHERE id = :1', $id );
		return true;
	}
	
	
	public function getUsers() {
		$users = $this->db->query( 'SELECT id, name FROM '.ASAPH_TABLE_USERS.' ORDER BY id' );
		foreach( array_keys($users) as $i ) {
			$users[$i]['name'] = htmlspecialchars( $users[$i]['name'] );
		}
		return $users;
	}
	
	
	public function getUser( $id ) {
		$user = $this->db->getRow( 'SELECT id, name FROM '.ASAPH_TABLE_USERS.' WHERE id = :1', $id );
		$user['name'] = htmlspecialchars( $user['name'] );
		return $user;
	}
	
	
	public function updateUser( $id, $name, $pass, $pass2 ) {
		if( empty($name) ) {
			return 'username-empty';
		}
		$userData = array( 
			'name' => $name,
			'loginId' => ''
		);
		
		if( !empty($pass) ) {
			if( $pass != $pass2 ) {
				return 'passwords-not-equal';
			}
			$userData['pass'] = md5($pass);
		}
		
		$this->db->updateRow(
			ASAPH_TABLE_USERS,
			array( 'id' => $id ),
			$userData
		);
		return true;
	}
	
	
	public function deleteUser( $id ) {
		$posts = $this->db->query( 'SELECT id FROM '.ASAPH_TABLE_POSTS.' WHERE userId = :1', $id );
		foreach( $posts as $p ) {
			$this->deletePost( $p['id'] );
		}
		
		$this->db->query( 'DELETE FROM '.ASAPH_TABLE_USERS.' WHERE id = :1', $id );
		return true;
	}
	
	
	public function addUser( $name, $pass, $pass2 ) {
		if( empty($name) ) {
			return 'username-empty';
		}
		
		if( $pass != $pass2 ) {
			return 'passwords-not-equal';
		}
		
		$this->db->insertRow(
			ASAPH_TABLE_USERS,
			array(
				'name' => $name,
				'pass' => md5($pass),
				'loginId' => ''
			)
		);
		return true;
	}
	
	
	protected function getUniqueFileName( $directory, $initialName ) {
		$newName = $initialName;
		$path = $directory .'/'. $initialName;
		
		// Do we already have a file with this name -> Add a numerical prefix
		for( $i = 1; file_exists($path); $i++ ) {
			$newName = $i . '-' . $initialName;
			$path = $directory .'/'. $newName;
		}
		
		return $newName;
	}
	
	
	protected function mkdirr( $pathname ) {
		if( empty($pathname) || is_dir($pathname) ) {
			return true;
		}
		if ( is_file($pathname) ) {
			return false;
		} 

		$nextPathname = substr( $pathname, 0, strrpos( $pathname, '/' ) );
		if( $this->mkdirr( $nextPathname ) ) {
			if( !file_exists( $pathname ) ) {
				$oldUmask = umask(0); 
				$success = @mkdir( $pathname, Asaph_Config::$defaultChmod );
				umask( $oldUmask ); 
				return $success;
			}
		}
		return false;
	}
}

?>