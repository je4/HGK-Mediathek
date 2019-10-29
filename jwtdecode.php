<?php 
require __DIR__ . '/vendor/autoload.php';

function decodeJWTPayload($data, $asJson = true)
    {
        if (!$asJson) {
            return \base64_decode(\strtr($data, '-_', '+/'));
        }
        $data = \json_decode(\base64_decode(\strtr($data, '-_', '+/')), true);
        return $data;
    }
	

if( !array_key_exists( 'jwt', $_REQUEST )) {
	http_response_code( 403 );
	exit;
}

$token = $_REQUEST['jwt'];
$tokens = \explode('.', $token, 3);

$payload = decodeJWTPayload($tokens[1]);
$äuth = array_key_exists('auth', $payload );
$result = [
	'auth'=> $auth,
	'data'=> !$auth ? [] : $payload.auth,
];
?>
<html>
<head>
</head>
<body>
<script>
	let token = '<?php echo $token; ?>'
	let tokens = token.split('.')
	let jsonstr = window.atob(tokens[1])
	let data = JSON.parse(jsonstr)
	console.log( data )
</script>
</body>
</html>