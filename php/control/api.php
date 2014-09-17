<?php

class Api
{
	protected $tables;
	protected $store;
	
	public function __construct( $store, $tables )
	{
		foreach( $this->tables as $table)
		{
			if( !$store->has_table( $table ) )
			{
				$this->log("Table not in the store: " . $table);
			}
			else
			{	
				$this->log("Serving from store: " . $table);
				$this->tables[] = $table;
			}
		}
		$this->store = $store;
	}
	public function handle_api_call( $req, $routePattern, $routeParameters, $parameters  )
	{
		
		$uri = preg_replace( "%^/api%", "", $req->getURI() );
		$this->log( $uri);
		
		$req->addHeader( "Content-Type", "application/json; charset=utf-8", EventHttpRequest::OUTPUT_HEADER );
		
		$method = $req->getCommand();
		$body = $req->getInputBuffer()->read(8000);
		
		$param = array();
		
		//Check URL, map endpoints
		if( preg_match( "%^/([^/]*)/?$%", $uri, $param ) )
		{
			if( !in_array( $param[1], $this->tables )
			{
				$this->send_notfound( $req, $param[1] );
				return false;
			}
			$table = $param[1];
			$id = 'all';
		}
		else if( preg_match( "%^/([^/]]*)/([^/]*)/?$%", $uri, $param ) )
		{
			if( !in_array( $param[1], $this->tables )
			{
				$this->send_notfound( $req, $param[1] );
				return false;
			}
			$table = $param[1];
			$id = $param[2];
			if( !$store->has_item( $table, $id )
			{
				$this->send_notfound( $req, "$table/$id" );
				return false;
			}
		}
		else 
		{
			$this->send_notfound( $req, $param[1] );
		}
		
		if( $body != "" )
		{
			$data = json_decode( $body );
			if( !is_object( $data ) )
			{
				$this->send_badrequest( print_r( $data , true) );
				return false;
			}
			print_r( $data );
		}
		else
		{
			$data = new stdClass;
		}
		
		switch( $method )
		{
			case EventHttpRequest::CMD_GET :
				$this->log("GET");
				if( $id == 'all' )
				{
					$json = $store->get_list( $table, true );
				}
				else
				{
					$json = $store->get_item( $table, $id, true );
				}
				$this->send_reply( "OK", $json);
				break;
			case EventHttpRequest::CMD_POST :
				$this->log("POST");
				if( $id == 'all' )
				{
					$json = $store->create_item( $table, $data, true );
				}
				else
				{
					$json = "{}";
				}
				$this->send_reply( "OK", $json );
				break;
			case EventHttpRequest::CMD_PUT :
				$this->log("PUT");
				if( $id == 'all' )
				{
					$json = "{}";
				}
				else
				{
					$json = $store->change_item( $table, $data );
				}
				$this->send_reply( "OK", $json );
				break;
			case EventHttpRequest::CMD_DELETE :
				$this->log("DELETE");
				if( $id == 'all' )
				{
					$json = "{}";
				}
				else
				{
					$store->delete_item( $table, $data );
					$json = "{}";
				}
				$this->send_reply( "OK", $json );
				break;
				
		}
		return true;
	}
	
	protected function send_reply( $req, $str, $json )
	{
		$buf = new EventBuffer;
		$buf->add( $json . "\n" );
		$req->sendReply(200, $str, $buf);
		$this->log( "{$str}");
	}
	
	protected function send_error( $code, $req, $str )
	{
		$req->sendError( $code );
		$this->log( "Not found: {$str}");
		return false;
	}
	
	protected function send_notfound( $req, $str )
	{
		return $this->send_error( 404, $req, $str );
	}
	
	protected function send_badrequest( $req, $str )
	{
		return $this->send_error( 500, $req, $str );
	}
	
	protected function log( $str, $newline = true )
	{
		echo( $str . (( $newline ) ? "\n" : "" ) );
	}

}
?>