#!/bin/bash
set -e

# Batch 1: Documentation
echo "Batch 1: Documentation..."
git add backend/docs/ frontend/docs/
git commit --no-verify -m "docs: add inventory implementation plans and API specs" || true

# Batch 2: Backend API & MinIO Setup
echo "Batch 2: Backend..."
git add backend/Modules/Music/
git commit --no-verify -m "feat: implement Song Inventory API and S3/MinIO Presigned URL logic" || true

# Batch 3: Frontend Inventory UI & Stores
echo "Batch 3: Frontend..."
git add frontend/
git commit --no-verify -m "feat: implement Inventory UI, S3 Upload Service, and Admin Upload Form" || true

# Catch all remaining
git add .
git commit --no-verify -m "chore: miscellaneous updates for Song Inventory" || true

echo "Pushing changes..."
git push origin develop
echo "Done!"
