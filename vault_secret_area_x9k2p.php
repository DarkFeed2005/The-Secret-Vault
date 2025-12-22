<?php
// JWT CTF Challenge - Stage 2: "The Final Vault"
// This is a harder challenge involving JWT manipulation and privilege escalation

session_start();

// Check if user completed stage 1
if (!isset($_SESSION['stage1_complete']) || $_SESSION['stage1_complete'] !== true) {
    header('Location: index.php');
    exit;
}

class SecureJWT {
    private static $secret = "ultra_secure_secret_key_v2_2024";
    
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

// Initialize user token with limited privileges
if (!isset($_SESSION['user_token'])) {
    $_SESSION['user_token'] = SecureJWT::encode([
        'username' => $_SESSION['username'],
        'role' => 'user',
        'admin' => false,
        'clearance_level' => 1,
        'exp' => time() + 3600
    ]);
}

$message = '';
$showFlag = false;
$error = '';

// Handle token submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $submittedToken = trim($_POST['token']);
    
    // Verify token signature
    if (!SecureJWT::verify($submittedToken)) {
        $error = "Invalid token signature! Token has been tampered with.";
    } else {
        $payload = SecureJWT::decode($submittedToken);
        
        if ($payload) {
            // Check if user escalated privileges
            if (isset($payload['admin']) && $payload['admin'] === true && 
                isset($payload['clearance_level']) && $payload['clearance_level'] >= 9) {
                $showFlag = true;
                $message = "🎉 Congratulations! You've successfully escalated your privileges!";
            } else {
                $error = "Access Denied! You need admin privileges and clearance level 9 or higher.";
                $message = "Current Role: " . ($payload['role'] ?? 'unknown') . 
                          " | Admin: " . ($payload['admin'] ? 'true' : 'false') . 
                          " | Clearance: " . ($payload['clearance_level'] ?? 0);
            }
        } else {
            $error = "Failed to decode token!";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Final Vault - Stage 2</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            max-width: 700px;
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
            background: #f5576c;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .challenge-info {
            background: #f0f0f0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #f5576c;
        }
        
        .challenge-info h3 {
            color: #f5576c;
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
        
        .token-display {
            background: #2d2d2d;
            color: #0f0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 12px;
            word-wrap: break-word;
            overflow-x: auto;
        }
        
        .token-display strong {
            color: #fff;
            display: block;
            margin-bottom: 10px;
        }
        
        .token-display code {
            background: transparent;
            color: #0f0;
            display: block;
            white-space: pre-wrap;
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
        
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            min-height: 120px;
            resize: vertical;
        }
        
        textarea:focus {
            outline: none;
            border-color: #f5576c;
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.3);
        }
        
        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }
        
        .info {
            background: #e3f2fd;
            color: #1565c0;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #1565c0;
        }
        
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2e7d32;
            text-align: center;
        }
        
        .flag {
            background: #2d2d2d;
            color: #ffd700;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 3px solid #ffd700;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
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
            margin-bottom: 8px;
        }
        
        .hint ol {
            margin-left: 20px;
            margin-top: 10px;
        }
        
        .hint li {
            margin: 5px 0;
        }
        
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            color: #c7254e;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔓 The Final Vault</h1>
        <p class="subtitle">
            <span class="badge">STAGE 2 OF 2 - HARD</span>
        </p>
        
        <div class="challenge-info">
            <h3>🎯 Final Challenge - JWT Privilege Escalation:</h3>
            <ul>
                <li>You have a user-level JWT token with limited access</li>
                <li>Modify the token to gain admin privileges</li>
                <li>You need: <code>admin: true</code> and <code>clearance_level: 9</code></li>
                <li>BUT... you need to keep a valid signature!</li>
                <li>Find the secret key and forge a new token</li>
            </ul>
        </div>
        
        <div class="token-display">
            <strong>📝 Your Current Token:</strong>
            <code><?php echo htmlspecialchars($_SESSION['user_token']); ?></code>
        </div>
        
        <?php if ($error): ?>
            <div class="error">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($message && !$showFlag): ?>
            <div class="info">ℹ️ <?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($showFlag): ?>
            <div class="success">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <div class="flag">
                🏁 FLAG: CTF{JWT_M4ST3R_PR1V_3SC4L4T10N_2026}
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="token">Submit Your Modified Token:</label>
                    <textarea id="token" name="token" placeholder="Paste your modified JWT token here..." required></textarea>
                </div>
                
                <button type="submit">🔑 Validate Token</button>
            </form>
        <?php endif; ?>
        
        <div class="hint">
            <strong>💡 Advanced Hints:</strong>
            <ol>
                <li>Decode your current token at jwt.io - what do you see?</li>
                <li>You need to find the <code>secret key</code> used to sign tokens</li>
                <li>Look for clues in the PHP source code (view-source or check robots.txt)</li>
                <li>The secret might be in a backup file or source file...</li>
                <li>Once you have the secret, modify the payload and re-sign at jwt.io</li>
                <li>Check the directory for any .txt or .bak files that might contain hints</li>
            </ol>
        </div>
    </div>
    
    
</body>
</html>