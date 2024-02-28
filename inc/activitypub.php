<?php
    // hey uhhh this is a heavy wip because i am new to that kind of stuff
    // ~lime360
	/*
	*	"This code is not a code of honour... no highly esteemed code is commemorated here... nothing valued is here."
	*	"What is here is dangerous and repulsive to us. This message is a warning about danger."
	*	This is a rudimentary, single-file, low complexity, minimum functionality, ActivityPub server.
	*	For educational purposes only.
	*	The Server produces an Actor who can be followed.
	*	The Actor can send messages to followers.
	*	The message can have linkable URls, hashtags, and mentions.
	*	An image and alt text can be attached to the message.
	*	The Server saves logs about requests it receives and sends.
	*	This code is NOT suitable for production use.
	*	SPDX-License-Identifier: AGPL-3.0-or-later
	*	This code is also "licenced" under CRAPL v0 - https://matt.might.net/articles/crapl/
	*	"Any appearance of design in the Program is purely coincidental and should not in any way be mistaken for evidence of thoughtful software construction."
	*	For more information, please re-read.
	*/

	//	Preamble: Set your details here
	//	This is where you set up your account's name and bio.
	//	You also need to provide a public/private keypair.
	//	The posting page is protected with a password that also needs to be set here.

	//	Set up the Actor's information
	//	Edit these:
	$username = rawurlencode("example");	//	Type the @ username that you want. Do not include an "@". 
	$realName = "E. Xample. Jr.";	//	This is the user's "real" name.
	$summary  = "Some text about the user.";	//	This is the bio of your user.

	//	Generate locally or from https://cryptotools.net/rsagen
	//	Newlines must be replaced with "\n"
	$key_private = "-----BEGIN RSA PRIVATE KEY-----\n...\n-----END RSA PRIVATE KEY-----";
	$key_public  = "-----BEGIN PUBLIC KEY-----\n...\n-----END PUBLIC KEY-----";

	//	Password for sending messages
	$password = "P4ssW0rd";

	/** No need to edit anything below here. **/

	//	Internal data
	$server   = $_SERVER["SERVER_NAME"];	//	Do not change this!

	//	Logging:
	//	ActivityPub is a "chatty" protocol. This takes all the requests your server receives and saves them in `/logs/` as a datestamped text file.

	// Get all headers and requests sent to this server
	$headers     = print_r( getallheaders(), true );
	$postData    = print_r( $_POST,    true );
	$getData     = print_r( $_GET,     true );
	$filesData   = print_r( $_FILES,   true );
	$input       = file_get_contents( "php://input" );
	$body        = json_decode( $input,true );
	$bodyData    = print_r( $body,     true );
	$requestData = print_r( $_REQUEST, true );
	$serverData  = print_r( $_SERVER,  true );
	!empty( $_GET["path"] )  ? $path = $_GET["path"] : $path = "/";

	//	Get the type of request - used in the log filename
	if ( isset( $body["type"] ) ) {
		//	Sanitise before using it in a filename
		$type = " " . urlencode( $body["type"] );
	} else {
		//	Sanitise the path requested
		$type = " " . urlencode( $path );
	}

	//	Create a timestamp for the filename
	//	This format has milliseconds, so should avoid logs being overwritten.
	//	If you have > 1000 requests per second, please use a different server.
	$timestamp = ( new DateTime() )->format( DATE_RFC3339_EXTENDED );

	//	Filename for the log
	$filename  = "{$timestamp}{$type}.txt";

	//	Save headers and request data to the timestamped file in the logs directory
	if( ! is_dir( "logs" ) ) { mkdir( "logs"); }

	file_put_contents( "logs/{$filename}", 
		"Headers:     \n$headers    \n\n" .
		"Body Data:   \n$bodyData   \n\n" .
		"POST Data:   \n$postData   \n\n" .
		"GET Data:    \n$getData    \n\n" .
		"Files Data:  \n$filesData  \n\n" .
		"Request Data:\n$requestData\n\n" .
		"Server Data: \n$serverData \n\n"
	);

	//	Routing:
	//	The .htaccess changes /whatever to /?path=whatever
	//	This runs the function of the path requested.
	switch ($path) {
		case ".well-known/webfinger":
			webfinger();   //	Mandatory. Static.
		case rawurldecode( $username ):
			username();    //	Mandatory. Static
		case "following":
			following();   //	Mandatory. Static
		case "followers":
			followers();   //	Mandatory. Can be dynamic
		case "inbox":
			inbox();       //	Mandatory. Only accepts follow requests.
		case "write":
			write();       //	User interface for writing posts
		case "send":      
			send();        //	API for posting content to the Fediverse
		case "outbox":    
			outbox();      //	Optional. Dynamic.
		case "/":         
			home();        // Optional. Can be dynamic
		default:
			die();
	}

	//	The WebFinger Protocol is used to identify accounts.
	//	It is requested with `example.com/.well-known/webfinger?resource=acct:username@example.com`
	//	This server only has one user, so it ignores the query string and always returns the same details.
	function webfinger() {
		global $username, $server;

		$webfinger = array(
			"subject" => "acct:{$username}@{$server}",
 			  "links" => array(
				array(
					 "rel" => "self",
					"type" => "application/activity+json",
					"href" => "https://{$server}/{$username}"
				)
			)
		);
		header( "Content-Type: application/json" );
		echo json_encode( $webfinger );
		die();
	}

	//	User:
	//	Requesting `example.com/username` returns a JSON document with the user's information.
	function username() {
		global $username, $realName, $summary, $server, $key_public;

		$user = array(
			"@context" => [
				"https://www.w3.org/ns/activitystreams",
				"https://w3id.org/security/v1"
			],
			                       "id" => "https://{$server}/{$username}",
			                     "type" => "Person",
			                "following" => "https://{$server}/following",
			                "followers" => "https://{$server}/followers",
			                    "inbox" => "https://{$server}/inbox",
			                   "outbox" => "https://{$server}/outbox",
			        "preferredUsername" =>  rawurldecode($username),
			                     "name" => "{$realName}",
			                  "summary" => "{$summary}",
			                      "url" => "https://{$server}/{$username}",
			"manuallyApprovesFollowers" =>  true,
			             "discoverable" =>  true,
			                "published" => "2024-02-29T12:34:00Z",
			"icon" => [
				     "type" => "Image",
				"mediaType" => "image/png",
				      "url" => "https://{$server}/icon.png"
			],
			"publicKey" => [
				"id"           => "https://{$server}/{$username}#main-key",
				"owner"        => "https://{$server}/{$username}",
				"publicKeyPem" => $key_public
			]
		);
		header( "Content-Type: application/activity+json" );
		echo json_encode( $user );
		die();
	}

	//	Follower / Following:
	// These JSON documents show how many users are following / followers-of this account.
	// The information here is self-attested. So you can lie and use any number you want.
	function following() {
		global $server;

		$following = array(
			  "@context" => "https://www.w3.org/ns/activitystreams",
			        "id" => "https://{$server}/following",
			      "type" => "Collection",
			"totalItems" => 0,
			     "items" => []
		);
		header( "Content-Type: application/activity+json" );
		echo json_encode( $following );
		die();
	}
	function followers() {
		global $server;
		//	The number of followers is self-reported
		//	You can set this to any number you like
		
		//	Get all the files 
		$follower_files = glob("data/followers/*.json");
		//	Number of posts
		$totalItems = count( $follower_files );

		$followers = array(
			  "@context" => "https://www.w3.org/ns/activitystreams",
			        "id" => "https://{$server}/followers",
			      "type" => "Collection",
			"totalItems" => $totalItems,
			     "items" => []
		);
		header( "Content-Type: application/activity+json" );
		echo json_encode( $followers );
		die();
	}

	//	Inbox:
	//	The `/inbox` is the main server. It receives all requests. 
	//	This server only responds to "Follow" requests.
	//	A remote server sends a follow request which is a JSON file saying who they are.
	//	The details of the remote user's server is saved to a file so that future messages can be delivered to the follower.
	//	An accept request is cryptographically signed and POST'd back to the remote server.
	function inbox() {
		global $body, $server, $username, $key_private;

		//	Validate HTTP Message Signature
		//	This logs whether the signature was validated or not
		if ( !verifyHTTPSignature() ) { die(); }

		//	Get the message and type
		$inbox_message = $body;
		$inbox_type = $inbox_message["type"];

		//	This inbox only responds to follow requests
		if ( "Follow" != $inbox_type ) { die(); }

		//	Get the parameters
		$follower_id    = $inbox_message["id"];    //	E.g. https://mastodon.social/(unique id)
		$follower_actor = $inbox_message["actor"]; //	E.g. https://mastodon.social/users/Edent
		$follower_host  = parse_url( $follower_actor, PHP_URL_HOST );
		$follower_path  = parse_url( $follower_actor, PHP_URL_PATH );

		//	Get the actor's profile as JSON
		//	Is the actor an https URl?
		if( 
			( filter_var( $follower_actor, FILTER_VALIDATE_URL) == true) && 
			(  parse_url( $follower_actor, PHP_URL_SCHEME     ) == "https" )
		) {
			//	Request the JSON representation of the the user
			$ch = curl_init( $follower_actor );

			//	Generate signed headers for this request
			$headers  = generate_signed_headers( null, $follower_host, $follower_path, "GET" );

			// Set cURL options
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt( $ch, CURLOPT_HTTPHEADER,     $headers );

			// Execute the cURL session
			$inbox_actor_json = curl_exec( $ch );

			// Check for errors
			if (curl_errno($ch)) {
				// Handle cURL error
				die();
			} 
			
			// Close cURL session
			curl_close($ch);

		  //	Save the actor's data in `/data/followers/`
		  if( ! is_dir( "data/followers" ) ) { mkdir( "data"); mkdir( "data/followers"); }
		  $follower_filename = urlencode( $follower_actor );
		  file_put_contents( "data/followers/{$follower_filename}.json", $inbox_actor_json );
	
		} else {
			die();
		}

		//	Get the new follower's Inbox
		$follower_actor_details = json_decode( $inbox_actor_json, true );
		$follower_inbox = $follower_actor_details["inbox"];

		//	Response Message ID
		//	This isn't used for anything important so could just be a random number
		$guid = uuid();

		//	Create the Accept message to the new follower
		$message = [
			"@context" => "https://www.w3.org/ns/activitystreams",
			"id"       => "https://{$server}/{$guid}",
			"type"     => "Accept",
			"actor"    => "https://{$server}/{$username}",
			"object"   => [
				"@context" => "https://www.w3.org/ns/activitystreams",
				"id"       =>  $follower_id,
				"type"     =>  $inbox_type,
				"actor"    =>  $follower_actor,
				"object"   => "https://{$server}/{$username}",
			]
		];

		//	The Accept is POSTed to the inbox on the server of the user who requested the follow
		$follower_inbox_path = parse_url( $follower_inbox, PHP_URL_PATH );
		//	Get the signed headers
		$headers = generate_signed_headers( $message, $follower_host, $follower_inbox_path, "POST" );
	
		//	POST the message and header to the requester's inbox
		$ch = curl_init( $follower_inbox );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt( $ch, CURLOPT_POSTFIELDS,     json_encode( $message ) );
		curl_setopt( $ch, CURLOPT_HTTPHEADER,     $headers );
		curl_exec( $ch );

		//	Check for errors
		if( curl_errno( $ch ) ) {
			file_put_contents( "error.txt",  curl_error( $ch ) );
		}
		curl_close($ch);
		die();
	}

	//	Unique ID:
	// Every message sent should have a unique ID. 
	// This can be anything you like. Some servers use a random number.
	// I prefer a date-sortable string.
	function uuid() {
		return sprintf( "%08x-%04x-%04x-%04x-%012x",
			time(),
			mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffffffffffff)
		);
	}

	//	Headers:
	// Every message that your server sends needs to be cryptographically signed with your Private Key.
	// This is a complicated process.
	// Please read https://blog.joinmastodon.org/2018/07/how-to-make-friends-and-verify-requests/ for more information.
	function generate_signed_headers( $message, $host, $path, $method ) {
		global $server, $username, $key_private;
	
		//	Location of the Public Key
		$keyId = "https://{$server}/{$username}#main-key";

		//	Get the Private Key
		$signer = openssl_get_privatekey( $key_private );

		//	Timestamp this message was sent
		$date   = date( "D, d M Y H:i:s \G\M\T" );

		//	There are subtly different signing requirements for POST and GET
		if ( "POST" == $method ) {
			//	Encode the message object to JSON
			$message_json = json_encode( $message );
			//	Generate signing variables
			$hash   = hash( "sha256", $message_json, true );
			$digest = base64_encode( $hash );

			//	Sign the path, host, date, and digest
			$stringToSign = "(request-target): post $path\nhost: $host\ndate: $date\ndigest: SHA-256=$digest";
			
			//	The signing function returns the variable $signature
			//	https://www.php.net/manual/en/function.openssl-sign.php
			openssl_sign(
				$stringToSign, 
				$signature, 
				$signer, 
				OPENSSL_ALGO_SHA256
			);
			//	Encode the signature
			$signature_b64 = base64_encode( $signature );

			//	Full signature header
			$signature_header = 'keyId="' . $keyId . '",algorithm="rsa-sha256",headers="(request-target) host date digest",signature="' . $signature_b64 . '"';

			//	Header for POST request
			$headers = array(
				        "Host: {$host}",
				        "Date: {$date}",
				      "Digest: SHA-256={$digest}",
				   "Signature: {$signature_header}",
				"Content-Type: application/activity+json",
				      "Accept: application/activity+json",
			);
		} else if ( "GET" == $method ) {	
			//	Sign the path, host, date - NO DIGEST
			$stringToSign = "(request-target): get $path\nhost: $host\ndate: $date";
			
			//	The signing function returns the variable $signature
			//	https://www.php.net/manual/en/function.openssl-sign.php
			openssl_sign(
				$stringToSign, 
				$signature, 
				$signer, 
				OPENSSL_ALGO_SHA256
			);
			//	Encode the signature
			$signature_b64 = base64_encode( $signature );

			//	Full signature header
			$signature_header = 'keyId="' . $keyId . '",algorithm="rsa-sha256",headers="(request-target) host date",signature="' . $signature_b64 . '"';

			//	Header for GET request
			$headers = array(
				        "Host: {$host}",
				        "Date: {$date}",
				   "Signature: {$signature_header}",
				      "Accept: application/activity+json",
			);
		}

		return $headers;
	}

	// User Interface for Homepage:
	// This creates a basic HTML page. This content appears when someone visits the root of your site.
	function home() {
		global $username, $server, $realName, $summary;
echo <<< HTML
<!DOCTYPE html>
<html lang="en-GB">
	<head>
		<meta charset="UTF-8">
		<title>{$realName}</title>
		<style>
			body { text-align: center; font-family:sans-serif; font-size:1.1em; }
			ul   { text-align: left; }
			img  { max-width: 50%; }
		</style>
	</head>
	<body>
		<span class="h-card">
			<img src="icon.png" alt="icon" class="u-photo " width="140px" />
			<h1><span class="p-name">{$realName}</span></h1>
			<h2><a class="p-nickname u-url" href="https://{$server}/{$username}">@{$username}@{$server}</a></h2>
			<p class="note">{$summary}</p>
		</span>
		<p><a href="https://gitlab.com/edent/activitypub-single-php-file/">This software is licenced under AGPL 3.0</a>.</p>
		<p>This site is a basic <a href="https://www.w3.org/TR/activitypub/">ActivityPub</a> server designed to be <a href="https://shkspr.mobi/blog/2024/02/activitypub-server-in-a-single-file/">a lightweight educational tool</a>.</p>
		<ul>
HTML;
		//	Get all posts, most recent first
		$posts = array_reverse( glob("posts/*.json") );

		//	Loop through the posts
		foreach ($posts as $post) {
			$postJSON = file_get_contents($post);
			$postData = json_decode($postJSON, true);
			$postTime = $postData["published"];
			$postHTML = $postData["content"];
			if ( isset($postData["attachment"]) ) {
				$postImgUrl = $postData["attachment"]["url"];
				$postImgAlt = $postData["attachment"]["name"];
				$postImg = "<img src='{$postImgUrl}' alt='$postImgAlt'>";
			} else {
				$postImg = "";
			}
			//	Display the post
			echo "<li><a href='/{$post}'><time datetime='{$postTime}'>$postTime</time></a><br>{$postHTML}<br>{$postImg}</li>";
		}
echo <<< HTML
		</ul>
	</body>
</html>
HTML;
die();
	}
	
	// User Interface for Writing:
	// This creates a basic HTML form. Type in your message and your password. It then POSTs the data to the `/send` endpoint.
	function write() {
echo <<< HTML
<!DOCTYPE html>
<html lang="en-GB">
	<head>
		<meta charset="UTF-8">
		<title>Send Message</title>
		<style>
			*{font-family:sans-serif;font-size:1.1em;}
		</style>
	</head>
	<body>
		<form action="/send" method="post" enctype="multipart/form-data">
			<label   for="content">Your message:</label><br>
			<textarea id="content"  name="content" rows="5" cols="32"></textarea><br>
			<label   for="image">Attach an image</label><br>
			<input  type="file"     name="image" id="image" accept="image/*"><br>
			<label   for="alt">Alt Text</label>
			<input  type="text"     name="alt" id="alt" size="32" /><br>
			<label   for="password">Password</label><br>
			<input  type="password" name="password" id="password" size="32"><br>
			<input  type="submit"  value="Post Message"> 
		</form>
	</body>
</html>
HTML;
		die();
	}

	//	Send Endpoint:
	//	This takes the submitted message and checks the password is correct.
	//	It reads all the followers' data in `data/followers` 
	//	It constructs a list of shared inboxes and unique inboxes
	//	It sends the message to every server that is following this account.
	function send() {
		global $password, $server, $username, $key_private;

		//	Does the posted password match the stored password?
		if( $password != $_POST["password"] ) { die(); }

		//	Get the posted content
		$content = $_POST["content"];

		//	Process the content into HTML to get hashtags etc
		list( "HTML" => $content, "TagArray" => $tags ) = process_content( $content );

		//	Is there an image attached?
		if ( isset( $_FILES['image']['tmp_name'] ) && ("" != $_FILES['image']['tmp_name'] ) ) {
			//	Get information about the image
			$image      = $_FILES['image']['tmp_name'];
			$image_info = getimagesize( $image );
			$image_ext  = image_type_to_extension( $image_info[2] );
			$image_mime = $image_info["mime"];

			//	Files are stored according to their hash
			//	A hash of "abc123" is stored in "/images/abc123.jpg"
			$sha1 = sha1_file( $image );
			$image_path = "images";
			$image_full_path = "{$image_path}/{$sha1}.{$image_ext}";

			//	Move media to the correct location
			//	Create a directory if it doesn't exist
			if( ! is_dir( $image_path ) ) { mkdir( $image_path ); }
			move_uploaded_file( $image, $image_full_path );

			//	Get the alt text
			if ( isset( $_POST["alt"] ) ) {
				$alt = $_POST["alt"];
			} else {
				$alt = "";
			}

			//	Construct the attachment value for the post
			$attachment = [
				"type"      => "Image",
				"mediaType" => "{$image_mime}",
				"url"       => "https://{$server}/{$image_full_path}",
				"name"      => $alt
		  ];

		} else {
			$attachment = [];
		}

		//	Current time - ISO8601
		$timestamp = date( "c" );

		//	Outgoing Message ID
		$guid = uuid();

		//	Construct the Note
		//	contentMap is used to prevent unnecessary "translate this post" pop ups
		// hardcoded to English
		$note = [
			"@context"     => array(
				"https://www.w3.org/ns/activitystreams"
			),
			"id"           => "https://{$server}/posts/{$guid}.json",
			"type"         => "Note",
			"published"    => $timestamp,
			"attributedTo" => "https://{$server}/{$username}",
			"content"      => $content,
			"contentMap"   => ["en" => $content],
			"to"           => ["https://www.w3.org/ns/activitystreams#Public"],
			"tag"          => $tags,
			"attachment"   => $attachment
		];

		//	Construct the Message
		//	The audience is public and it is sent to all followers
		$message = [
			"@context" => "https://www.w3.org/ns/activitystreams",
			"id"       => "https://{$server}/posts/{$guid}.json",
			"type"     => "Create",
			"actor"    => "https://{$server}/{$username}",
			"to"       => [
				"https://www.w3.org/ns/activitystreams#Public"
			],
			"cc"       => [
				"https://{$server}/followers"
			],
			"object"   => $note
		];
		
		//	Save the permalink
		$note_json = json_encode( $note );
		//	Check for posts/ directory and create it
		if( ! is_dir( "posts" ) ) { mkdir( "posts"); }
		file_put_contents( "posts/{$guid}.json", print_r( $note_json, true ) );

		//	Read existing followers
		$followers = glob( "data/followers/*.json" );
		
		//	Get all the inboxes
		$inboxes = [];
		foreach ( $followers as $follower ) {
			$follower_info = json_decode( file_get_contents( $follower ), true );

			//	Some servers have "Shared inboxes"
			//	If you have lots of followers on a single server, you only need to send the message once
			if( isset( $follower_info["endpoints"]["sharedInbox"] ) ) {
				$sharedInbox = $follower_info["endpoints"]["sharedInbox"];
				if ( !in_array( $sharedInbox, $inboxes ) ) { 
					$inboxes[] = $sharedInbox; 
				}
			} else {
				//	If not, use the individual inbox
				$inbox = $follower_info["inbox"];
				if ( !in_array( $inbox, $inboxes ) ) { 
					$inboxes[] = $inbox; 
				}
			}
		}

		//	Prepare to use the multiple cURL handle
		//	This makes it more efficient to send many simultaneous messages
		$mh = curl_multi_init();

		//	Loop through all the inboxes of the followers
		//	Each server needs its own cURL handle
		//	Each POST to an inbox needs to be signed separately
		foreach ( $inboxes as $inbox ) {
			
			$inbox_host  = parse_url( $inbox, PHP_URL_HOST );
			$inbox_path  = parse_url( $inbox, PHP_URL_PATH );
	
			//	Get the signed headers
			$headers = generate_signed_headers( $message, $inbox_host, $inbox_path, "POST" );
		
			//	POST the message and header to the requester's inbox
			$ch = curl_init( $inbox );		
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
			curl_setopt( $ch, CURLOPT_POSTFIELDS,     json_encode($message) );
			curl_setopt( $ch, CURLOPT_HTTPHEADER,     $headers );

			//	Add the handle to the multi-handle
			curl_multi_add_handle( $mh, $ch );
		}

		//	Execute the multi-handle
		do {
			$status = curl_multi_exec( $mh, $active );
			if ( $active ) {
				curl_multi_select( $mh );
			}
		} while ( $active && $status == CURLM_OK );

		//	Close the multi-handle
		curl_multi_close( $mh );

		//	Render the JSON so the user can see the POST has worked
		header( "Location: https://{$server}/posts/{$guid}.json" );
		die();
	}

	//	Content can be plain text. But to add clickable links and hashtags, it needs to be turned into HTML.
	//	Tags are also included separately in the note
	function process_content( $content ) {
		global $server;

		//	Convert any URls into hyperlinks
		$link_pattern = '/\bhttps?:\/\/\S+/iu';
		$replacement = function ( $match ) {
			$url = htmlspecialchars( $match[0], ENT_QUOTES, "UTF-8" );
			return "<a href=\"$url\">$url</a>";
		};
		$content = preg_replace_callback( $link_pattern, $replacement, $content );	  

		//	Get any hashtags
		$hashtags = [];
		$hashtag_pattern = '/(?:^|\s)\#(\w+)/';	//	Beginning of string, or whitespace, followed by #
		preg_match_all( $hashtag_pattern, $content, $hashtag_matches );
		foreach ($hashtag_matches[1] as $match) {
			$hashtags[] = $match;
		}

		//	Construct the tag value for the note object
		$tags = [];
		foreach ( $hashtags as $hashtag ) {
			$tags[] = array(
				"type" => "Hashtag",
				"name" => "#{$hashtag}",
			);
		}

		//	Add HTML links for hashtags into the text
		$content = preg_replace(
			$hashtag_pattern, 
			" <a href='https://{$server}/tag/$1'>#$1</a>", 
			$content
		);

		//	Detect user mentions
		$usernames = [];
		$usernames_pattern = '/@(\S+)@(\S+)/'; //	This is a *very* sloppy regex
		preg_match_all( $usernames_pattern, $content, $usernames_matches );
		foreach ( $usernames_matches[0] as $match ) {
			$usernames[] = $match;
		}

		//	Construct the mentions value for the note object
		//	This goes in the generic "tag" property
		//	TODO: Add this to the CC field
		foreach ( $usernames as $username ) {
			list( $null, $user, $domain ) = explode( "@", $username );
			$tags[] = array(
				"type" => "Mention",
				"href" => "https://{$domain}/@{$user}",
				"name" => "{$username}"
			);

			//	Add HTML links to usernames
			$username_link = "<a href=\"https://{$domain}/@{$user}\">$username</a>";
			$content = str_replace( $username, $username_link, $content );
		}

		// Construct HTML breaks from carriage returns and line breaks
		$linebreak_patterns = array("\r\n", "\r", "\n"); // Variations of line breaks found in raw text
		$content = str_replace($linebreak_patterns, "<br/>", $content);
		
		//	Construct the content
		$content = "<p>{$content}</p>";

		return [
			"HTML" => $content, 
			"TagArray" => $tags
		];
	}

	//	The Outbox contains a date-ordered list (newest first) of all the user's posts
	//	This is optional.
	function outbox() {
		global $server, $username;

		//	Get all posts
		$posts = array_reverse( glob("posts/*.json") );
		//	Number of posts
		$totalItems = count( $posts );
		//	Create an ordered list
		$orderedItems = [];
		foreach ($posts as $post) {
			$orderedItems[] = array(
				"type"   => "Create",
				"actor"  => "https://{$server}/{$username}",
				"object" => "https://{$server}/{$post}"
			);
		}

		//	Create User's outbox
		$outbox = array(
			"@context"     => "https://www.w3.org/ns/activitystreams",
			"id"           => "https://{$server}/outbox",
			"type"         => "OrderedCollection",
			"totalItems"   =>  $totalItems,
			"summary"      => "All the user's posts",
			"orderedItems" =>  $orderedItems
		);

		//	Render the page
		header( "Content-Type: application/activity+json" );
		echo json_encode( $outbox );
		die();
	}

	//	Verify the signature sent with the message.
	//	This is optional
	//	It is very confusing
	function verifyHTTPSignature() {
		global $input, $body, $server;

		//	Get the headers send with the request
		$headers = getallheaders();
		//	Ensure the header keys match the format expected by the signature 
		$headers = array_change_key_case( $headers, CASE_LOWER );

		//	Validate the timestamp is within ±30 seconds
		if ( !isset( $headers["date"] ) ) { return null; }	//	No date set
		$dateHeader = $headers["date"];
		$headerDatetime  = DateTime::createFromFormat('D, d M Y H:i:s T', $dateHeader);
		$currentDatetime = new DateTime();

		// Calculate the time difference in seconds
		$timeDifference = abs($currentDatetime->getTimestamp() - $headerDatetime->getTimestamp());
		if ($timeDifference > 30) { 
			//	Write a log detailing the error
			$timestamp = ( new DateTime() )->format( DATE_RFC3339_EXTENDED );
			//	Filename for the log
			$filename  = "{$timestamp} Time Failure.txt";

			//	Save headers and request data to the timestamped file in the logs directory
			if( !is_dir( "logs" ) ) { mkdir( "logs"); }

			file_put_contents( "logs/{$filename}", 
				"Original Date:\n" . print_r( $dateHeader, true )    . "\n" .
				"Local Date:\n"    . print_r( $currentDatetime->format('D, d M Y H:i:s T'), true ) . "\n"
			);
			return false; 
		}

		//	Validate the Digest
		//	It is the hash of the raw input string, in binary, encoded as base64
		$digestString = $headers["digest"];
		//	Usually in the form `SHA-256=Ofv56Jm9rlowLR9zTkfeMGLUG1JYQZj0up3aRPZgT0c=`
		//	The Base64 encoding may have multiple `=` at the end. So split this at the first `=`
		$digestData = explode( "=", $digestString, 2 );
		$digestAlgorithm = $digestData[0];
		$digestHash = $digestData[1];

		//	There might be many different hashing algorithms
		//	TODO: Find a way to transform these automatically
		if ( "SHA-256" == $digestAlgorithm ) {
			$digestAlgorithm = "sha256";
		} else if ( "SHA-512" == $digestAlgorithm ) {
			$digestAlgorithm = "sha512";
		}

		//	Manually calculate the digest based on the data sent
		$digestCalculated = base64_encode( hash( $digestAlgorithm, $input, true ) );

		//	Does our calculation match what was sent?
		if ( !( $digestCalculated == $digestHash ) ) { 
			//	Write a log detailing the error
			$timestamp = ( new DateTime() )->format( DATE_RFC3339_EXTENDED );
			//	Filename for the log
			$filename  = "{$timestamp} Digest Failure.txt";

			//	Save headers and request data to the timestamped file in the logs directory
			if( ! is_dir( "logs" ) ) { mkdir( "logs"); }

			file_put_contents( "logs/{$filename}", 
				"Original Input:\n"    . print_r( $input, true )    . "\n" .
				"Original Digest:\n"   . print_r( $digestString, true ) . "\n" .
				"Calculated Digest:\n" . print_r( $digestCalculated, true ) . "\n"
			);
			return false; 
		}

		//	Examine the signature
		$signatureHeader = $headers["signature"];

		// Extract key information from the Signature header
		$signatureParts = [];
		//	Converts 'a=b,c=d e f' into ["a"=>"b", "c"=>"d e f"]
		               // word="text"
		preg_match_all('/(\w+)="([^"]+)"/', $signatureHeader, $matches);
		foreach ($matches[1] as $index => $key) {
			$signatureParts[$key] = $matches[2][$index];
		}

		//	Manually reconstruct the header string
		$signatureHeaders = explode(" ", $signatureParts["headers"] );
		$signatureString = "";
		foreach ($signatureHeaders as $signatureHeader) {
			if ( "(request-target)" == $signatureHeader ) {
				$method = strtolower( $_SERVER["REQUEST_METHOD"] );
				$target =             $_SERVER["REQUEST_URI"];
				$signatureString .= "(request-target): {$method} {$target}\n";
			} else if ( "host" == $signatureHeader ) {
				$host = strtolower( $_SERVER["HTTP_HOST"] );	
				$signatureString .= "host: {$host}\n";
			} else {
				$signatureString .= "{$signatureHeader}: " . $headers[$signatureHeader] . "\n";
			}
		}

		//	Remove trailing newline
		$signatureString = trim( $signatureString );

		//	Get the Public Key
		//	The link to the key might be sent with the body, but is always sent in the Signature header.
		$publicKeyURL = $signatureParts["keyId"];

		//	This is usually in the form `https://example.com/user/username#main-key`
		//	This is to differentiate if the user has multiple keys
		//	TODO: Check the actual key
		// This request does not need to be signed. But it does need to specify that it wants a JSON response
		$context   = stream_context_create(
			[ "http" => [ "header" => "Accept: application/activity+json" ] ] 
		);
		$userJSON  = file_get_contents( $publicKeyURL, false, $context );
		$userData  = json_decode( $userJSON, true );
		$publicKey = $userData["publicKey"]["publicKeyPem"];

		//	Get the remaining parts
		$signature = base64_decode( $signatureParts["signature"] );
		$algorithm = $signatureParts["algorithm"];

		//	Finally! Calculate whether the signature is valid
		//	Returns 1 if verified, 0 if not, false or -1 if an error occurred
		$verified = openssl_verify(
			$signatureString, 
			$signature, 
			$publicKey, 
			$algorithm
		);

		//	Convert to boolean
		if ($verified === 1) {
			$verified = true;
		} elseif ($verified === 0) {
			$verified = false;
		} else {
			$verified = null;
		}

		//	Write a log detailing the signature verification process
		$timestamp = ( new DateTime() )->format( DATE_RFC3339_EXTENDED );
		//	Filename for the log
		$filename  = "{$timestamp} Signature ". json_encode( $verified ) . ".txt";

		//	Save headers and request data to the timestamped file in the logs directory
		if( ! is_dir( "logs" ) ) { mkdir( "logs"); }

		file_put_contents( "logs/{$filename}", 
			"Original Body:\n"              . print_r( $body, true )             . "\n\n" .
			"Original Headers:\n"           . print_r( $headers, true )          . "\n\n" .
			"Signature Headers:\n"          . print_r( $signatureHeaders, true ) . "\n\n" .
			"Calculated signatureString:\n" . print_r( $signatureString, true )  . "\n\n" .
			"Calculated algorithm:\n"       . print_r( $algorithm, true )        . "\n\n" .
			"publicKeyURL:\n"               . print_r( $publicKeyURL, true )     . "\n\n" .
			"publicKey:\n"                  . print_r( $publicKey, true )        . "\n"
		);

		return $verified;
	}
?>