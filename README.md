# 🔐 JWT CTF Challenge - "The Secret Vault"

A **two-stage** CTF challenge focused on JWT token exploitation and privilege escalation.


<p align="center">
<a href="https://www.w3schools.com/html/" target="_blank" rel="noreferrer"> <img src="https://skillicons.dev/icons?i=php" alt="php" width="70" height="70"/> </a>
<a href="https://www.w3schools.com/html/" target="_blank" rel="noreferrer"> <img src="https://skillicons.dev/icons?i=html" alt="html" width="70" height="70"/> </a>  
<a href="#" target="_blank" rel="noreferrer"> <img src="https://skillicons.dev/icons?i=css" alt="css" width="70" height="70"/> </a>
<a href="#" target="_blank" rel="noreferrer"> <img src="https://skillicons.dev/icons?i=js" alt="javascript" width="70" height="70"/> </a>
</p>


## 📋 Challenge Overview

### Stage 1: Token Discovery (Easy-Medium)
- **Objective**: Find a hidden JWT token and decode it
- **Skills**: Source code inspection, JWT decoding
- **Difficulty**: ⭐⭐⭐☆☆

### Stage 2: Privilege Escalation (Hard)
- **Objective**: Modify JWT token to gain admin access
- **Skills**: JWT manipulation, signature forging, reconnaissance
- **Difficulty**: ⭐⭐⭐⭐☆

## 🎯 Learning Objectives

Participants will learn:
- JWT token structure and encoding
- Security risks of exposing tokens in client-side code
- JWT signature verification and bypassing
- Common web application reconnaissance techniques
- Privilege escalation through token manipulation

## 📦 Files Included

1. **index.php** - Stage 1: Main login page with hidden JWT token
2. **vault_secret_area_x9k2p.php** - Stage 2: Hidden admin area
3. **vault_secret_area_x9k2p.php.bak** - Backup file containing secret key
4. **robots.txt** - Hints about backup files

## 🚀 Installation

### Requirements
- PHP 7.0 or higher
- Apache/Nginx web server
- No database required

### Setup Steps
 1. **Clone the repository**
```bash
git clone https://github.com/DarkFeed2005/The-Secret-Vault.git
cd Car-Rental-web-
```

2. **Create directory structure:**
```bash
mkdir jwt_ctf
cd jwt_ctf
```

3. **Place all files in the web root:**
```
jwt_ctf/
├── index.php
├── vault_secret_area_x9k2p.php
├── vault_secret_area_x9k2p.php.bak
└── robots.txt
```

4. **Set permissions:**
```bash
chmod 644 *.php
chmod 644 robots.txt
chmod 644 *.bak
```

5. **Configure web server to serve the directory**

6. **Access the challenge:**
```
http://localhost/jwt_ctf/index.php
```

## 🎮 How to Play

### Stage 1 Solution Path:
1. Open `index.php` in browser
2. View page source (Ctrl+U or right-click → View Source)
3. Find the JWT token in JavaScript variable `adminAuthToken`
4. Go to [jwt.io](https://jwt.io)
5. Paste the token in the "Encoded" section
6. Extract credentials from decoded payload:
   - Username: `admin`
   - Password: `CTF_Stage1_C0mpl3t3`
7. Login to proceed to Stage 2

### Stage 2 Solution Path:
1. Decode your current user token at jwt.io
2. Notice you have limited privileges:
   - `role: "user"`
   - `admin: false`
   - `clearance_level: 1`
3. Check `robots.txt` for hints:
   ```
   http://localhost/jwt_ctf/robots.txt
   ```
4. Find reference to backup file: `vault_secret_area_x9k2p.php.bak`
5. Download the backup file:
   ```
   http://localhost/jwt_ctf/vault_secret_area_x9k2p.php.bak
   ```
6. Extract the secret key: `ultra_secure_secret_key_v2_2024`
7. Go to jwt.io and:
   - Modify payload:
     ```json
     {
       "username": "admin",
       "role": "admin",
       "admin": true,
       "clearance_level": 9,
       "exp": 1735000000
     }
     ```
   - Enter secret key in "VERIFY SIGNATURE" section
   - Copy the new signed token
8. Submit the forged token to get the flag!

**Good luck, hackers!** 🏴‍☠️



## 🛡️ Security Concepts Demonstrated

1. **Client-side Token Exposure**: Tokens should never be embedded in client-side code
2. **Weak Secret Management**: Hardcoded secrets in backup files
3. **Information Disclosure**: robots.txt revealing sensitive paths
4. **JWT Signature Verification**: Importance of proper signature validation
5. **Privilege Escalation**: Manipulating token claims to gain unauthorized access

## 🎓 Hints System

The challenge includes progressive hints:
- **Stage 1**: Hints about source code inspection
- **Stage 2**: 
  - Check robots.txt
  - Look for backup files
  - Find the secret key
  - Use jwt.io to forge new tokens


## ⚠️ Important Notes

- This is for **educational purposes only**
- Deploy only in **controlled CTF environments**
- Never use these patterns in **production applications**
- Participants should have **explicit permission** to test

## 📚 Additional Resources

- [JWT.io](https://jwt.io) - JWT decoder/encoder
- [OWASP JWT Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/JSON_Web_Token_for_Java_Cheat_Sheet.html)
- [HackTricks - JWT Attacks](https://book.hacktricks.xyz/pentesting-web/hacking-jwt-json-web-tokens)



## 👨‍💻 Author

- **Your Name** <a href="https://github.com/yourusername" target="_blank" rel="noreferrer"> <img src="https://skillicons.dev/icons?i=github" alt="github" width="20" height="20"/> </a>
- LinkedIn <a href="https://www.linkedin.com/in/yourprofile/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/linkedin/linkedin-original.svg" alt="linkedin" width="20" height="20"/> </a>
- Instagram <a href="https://www.instagram.com/yourusername/" target="_blank" rel="noreferrer"> <img src="https://skillicons.dev/icons?i=instagram" alt="instagram" width="20" height="20"/> </a>

