# Remove all tracked upload files from Git
Write-Host "Removing upload files from Git tracking..." -ForegroundColor Cyan

git rm -r --cached public/uploads/item_images/ 2>$null
git rm -r --cached public/uploads/staff_photos/ 2>$null
git rm -r --cached public/uploads/business_logos/ 2>$null
git rm -r --cached public/business_logos/ 2>$null

Write-Host "Adding .gitkeep files..." -ForegroundColor Cyan
git add public/uploads/item_images/.gitkeep
git add public/uploads/staff_photos/.gitkeep
git add public/uploads/business_logos/.gitkeep
git add public/business_logos/.gitkeep

Write-Host "Adding updated files..." -ForegroundColor Cyan
git add .gitignore deploy.sh deploy-simple.sh fix-vps-images.sh git-untrack-uploads.sh

Write-Host ""
Write-Host "✅ Done! Now run:" -ForegroundColor Green
Write-Host '   git commit -m "Fix: Exclude upload images from Git"' -ForegroundColor Yellow
Write-Host "   git push origin master" -ForegroundColor Yellow
