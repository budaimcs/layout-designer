<?php

class Router
{
	protected $routes;
	protected $http_server;
	
	public function __construct( $http_server, $routes = array() )
	{
		$this->routes = $routes;
		$this->http_server = $http_server;
		$this->http_server->setDefaultCallback( array( $this, "route" ) );
	}
	
	public function route( $request )
	{
		
		$parameters = array();
		foreach( $this->routes as $route)
		{
			if( preg_match($route[0], $req->getUri(), $parameters ) )
			{
				call_user_func( $route[1], $req, $route[0], $route[2], $parameters );
			}
		}
	}
}
?>