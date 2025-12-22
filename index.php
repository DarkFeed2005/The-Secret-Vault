<?php
// JWT CTF Challenge - "The Secret Vault" - Stage 1
// Goal: Find the JWT token, decode it at jwt.io, and use credentials to access the next stage

session_start();

// Simple JWT implementation for CTF
class SimpleJWT {
    private static $secret = "ctf_secret_key_2024_do_not_share";
    
    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($payload);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    public static function decode($jwt) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }
        
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        return json_decode($payload, true);
    }
    
    public static function verify($jwt) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }
        
        $signature = hash_hmac('sha256', $parts[0] . "." . $parts[1], self::$secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return hash_equals($base64UrlSignature, $parts[2]);
    }
}

// Initialize admin token (hidden in source)
$adminToken = SimpleJWT::encode([
    'username' => 'admin',
    'password' => 'CTF_Stage1_C0mpl3t3',
    'role' => 'administrator',
    'exp' => time() + 3600
]);

// Handle login
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Check credentials from JWT token
    if ($username === 'admin' && $password === 'CTF_Stage1_C0mpl3t3') {
        $_SESSION['stage1_complete'] = true;
        $_SESSION['username'] = $username;
        header('Location: vault_secret_area_x9k2p.php');
        exit;
    } else {
        $error = "Invalid credentials. Keep looking...";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Secret Vault - CTF Challenge</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 28px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-bottom: 20px;
        }
        
        .challenge-info {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #667eea;
        }
        
        .challenge-info h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .challenge-info ul {
            margin-left: 20px;
            color: #555;
            font-size: 14px;
        }
        
        .challenge-info li {
            margin: 5px 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Courier New', monospace;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }
        
        .hint {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-radius: 6px;
            border-left: 4px solid #ffc107;
            font-size: 13px;
            color: #856404;
        }
        
        .hint strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
    <!-- The admin token is hidden somewhere in this page... -->
    <script>
        // Debug mode disabled in production
        const DEBUG = false;
        const adminAuthToken = "<?php echo $adminToken; ?>";
        
        if (DEBUG) {
            console.log("Admin Token:", adminAuthToken);
            console.log("Decode this token at jwt.io to get credentials!");
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>🔐 The Secret Vault</h1>
        <p class="subtitle">
            <span class="badge">STAGE 1 OF 2</span>
        </p>
        
        <div class="challenge-info">
            <h3>📋 Challenge Instructions:</h3>
            <ul>
                <li>Find the hidden JWT token in this application</li>
                <li>Decode the JWT token at <a href="https://jwt.io" target="_blank">jwt.io</a></li>
                <li>Extract the username and password from the token</li>
                <li>Login to proceed to Stage 2!</li>
            </ul>
        </div>
        
        <?php if ($error): ?>
            <div class="error">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">🚀 Submit Credentials</button>
        </form>
        
        <div class="hint">
            <strong>💡 Hints:</strong>
            Try inspecting the page source code, checking JavaScript variables, or looking at network requests. 
            JWT tokens are often stored in places developers think are "hidden" but are actually visible to anyone 
            who knows where to look!
        </div>
    </div>
</body>
</html>