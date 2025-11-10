# Quick Guide: Push to GitHub with Personal Access Token

## Steps:

1. **Create Personal Access Token:**
   - Go to: https://github.com/settings/tokens/new
   - Name: `zodray-project`
   - Expiration: Choose your preference
   - **Check the `repo` scope** (this is important!)
   - Click "Generate token"
   - **COPY THE TOKEN** (you won't see it again!)

2. **Push your code:**
   ```bash
   git push -u origin main
   ```

3. **When prompted:**
   - Username: `mohitdevang`
   - Password: **Paste your Personal Access Token** (not your GitHub password!)

## Alternative: Use SSH (if you prefer)

If you have SSH keys set up:
```bash
git remote set-url origin git@github.com:mohitdevang/zodray-project.git
git push -u origin main
```

## Note:
- The cached credential has been cleared
- GitHub no longer accepts passwords for HTTPS
- You must use a Personal Access Token with `repo` scope

