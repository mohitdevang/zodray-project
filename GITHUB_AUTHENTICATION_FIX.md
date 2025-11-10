# Fixing GitHub 403 Authentication Error

## Problem
GitHub no longer accepts passwords for HTTPS authentication. You need to use a **Personal Access Token (PAT)** or **SSH keys**.

## Solution Options

### Option 1: Use Personal Access Token (Recommended - Easier)

#### Step 1: Create a Personal Access Token
1. Go to: https://github.com/settings/tokens
2. Click "Generate new token" → "Generate new token (classic)"
3. Give it a name: `zodray-project-push`
4. Select expiration (90 days, 1 year, or no expiration)
5. **Select scopes**: Check `repo` (this gives full repository access)
6. Click "Generate token"
7. **COPY THE TOKEN IMMEDIATELY** (you won't see it again!)

#### Step 2: Clear Cached Credentials (Windows)
Open Windows Credential Manager:
1. Press `Win + R`
2. Type: `control /name Microsoft.CredentialManager`
3. Go to "Windows Credentials"
4. Find any entries for `git:https://github.com`
5. Remove/Delete them

Or use command line:
```bash
# Clear GitHub credentials from Windows Credential Manager
cmdkey /list | findstr git
# Then delete with: cmdkey /delete:git:https://github.com
```

#### Step 3: Push Using Token
When you run `git push`, it will prompt for:
- **Username**: `mohitdevang`
- **Password**: Paste your Personal Access Token (not your GitHub password!)

### Option 2: Use SSH (More Secure, One-time Setup)

#### Step 1: Check if you have SSH key
```bash
ls ~/.ssh
```

#### Step 2: Generate SSH Key (if you don't have one)
```bash
ssh-keygen -t ed25519 -C "your_email@example.com"
# Press Enter to accept default location
# Enter a passphrase (optional but recommended)
```

#### Step 3: Add SSH Key to GitHub
1. Copy your public key:
   ```bash
   cat ~/.ssh/id_ed25519.pub
   ```
2. Go to: https://github.com/settings/keys
3. Click "New SSH key"
4. Paste your public key
5. Click "Add SSH key"

#### Step 4: Change Remote URL to SSH
```bash
git remote set-url origin git@github.com:mohitdevang/zodray-project.git
git push -u origin main
```

### Option 3: Use GitHub CLI (gh)

If you have GitHub CLI installed:
```bash
gh auth login
# Follow the prompts to authenticate
git push -u origin main
```

## Quick Fix Commands

### For Personal Access Token:
1. Clear credentials:
   ```bash
   git credential-manager-core erase
   ```
   Or manually delete from Windows Credential Manager

2. Push (will prompt for credentials):
   ```bash
   git push -u origin main
   ```
   - Username: `mohitdevang`
   - Password: Your Personal Access Token

### For SSH:
1. Change remote URL:
   ```bash
   git remote set-url origin git@github.com:mohitdevang/zodray-project.git
   ```

2. Push:
   ```bash
   git push -u origin main
   ```

## Troubleshooting

### If you still get 403:
- Make sure the token has `repo` scope
- Check that you're using the token, not your password
- Verify the repository name is correct: `zodray-project`
- Make sure you're the owner or have write access

### If you get "permission denied":
- Verify your SSH key is added to GitHub
- Test SSH connection: `ssh -T git@github.com`

## Recommended: Use Personal Access Token
This is the fastest solution. Just create a token with `repo` scope and use it as your password when pushing.

