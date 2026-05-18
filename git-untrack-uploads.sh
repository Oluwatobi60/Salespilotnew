#!/bin/bash
# Remove all tracked upload files from Git
echo "Removing upload files from Git tracking..."

git rm -r --cached public/uploads/item_images/*.* 2>/dev/null || true
git rm -r --cached public/uploads/staff_photos/*.* 2>/dev/null || true
git rm -r --cached public/uploads/business_logos/*.* 2>/dev/null || true
git rm -r --cached public/business_logos/*.* 2>/dev/null || true

echo "Adding .gitkeep files..."
git add public/uploads/item_images/.gitkeep
git add public/uploads/staff_photos/.gitkeep
git add public/uploads/business_logos/.gitkeep
git add public/business_logos/.gitkeep

echo "Adding updated files..."
git add .gitignore deploy.sh deploy-simple.sh fix-vps-images.sh

echo ""
echo "✅ Done! Now run:"
echo "   git commit -m 'Fix: Exclude upload images from Git'"
echo "   git push origin master"
