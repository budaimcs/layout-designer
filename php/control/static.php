<?php
function ServeStatic( $req, $routePattern, $routeParameters, $parameters  )
{
	if( isset( $routeParameters[ 'path' ]
		$path = $routeParameters[ 'path' ];
	else
		$path = $parameters[1];
	echo( "Static: /" . $path );
	//   echo( getcwd() );
	if( file_exists( "html/" . $path ) and is_readable( "html/" . $path ) )
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		$mime = finfo_file($finfo, "html/" . $path );
		finfo_close($finfo);

		$matches = array();
		preg_match( "%.([a-zA-Z]*)$%", basename( $path ), $matches );
		$extension = end( $matches );
		switch( strtolower( $extension ) )
		{
			case "css" :
				$mime = "text/css";
				break;
			case "js" :
				$mime = "text/javascript";
				break;
			case "html" :
			case "htm" :
				$mime = "text/html";
				break;
			case "png" :
			case "gif" :
			case "jpg" :
			case "jpeg" :
				if( $extension == 'jpg' ) $extension = 'jpeg';
				$mime = "image/" . $extension;
				break;
			default :
				$mime = "text/plain";
				break;
		}
		echo( " ({$mime})");
		$req->addHeader ( "Content-type" , $mime , EventHttpRequest::OUTPUT_HEADER );
		$buf = new EventBuffer;
		$buf->add( file_get_contents( "html/" . $path ) );
		$req->sendReply(200, "OK", $buf);
		echo( " 200 OK\n");
	}
	else
	{
		$req->sendError(404);
		echo( " 404 Not found!\n");
	}

}
?>