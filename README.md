# Specifications

### How to make a project
* Fork webcore_modular_full

### How to update prime
* Copy Modules from webcore_modular to prime
* Run
```
git pull origin master
cd apanel/modules/sales_module/
git reset --hard origin/master
git pull origin master
cd ../purchase_module/
git reset --hard origin/master
git pull origin master
cd ../inventory_module/
git reset --hard origin/master
git pull origin master
cd ../reports_module/
git reset --hard origin/master
git pull origin master
cd ../maintenance_module/
git reset --hard origin/master
git pull origin master
cd ../financials_module/
git reset --hard origin/master
git pull origin master
cd ../../../
rm apanel/modules/sales_module/bitbucket-pipelines.yml
rm apanel/modules/purchase_module/bitbucket-pipelines.yml
rm apanel/modules/inventory_module/bitbucket-pipelines.yml
rm apanel/modules/reports_module/bitbucket-pipelines.yml
rm apanel/modules/maintenance_module/bitbucket-pipelines.yml
rm apanel/modules/financials_module/bitbucket-pipelines.yml
find * -type d -print0 | xargs -0 chmod 0755
find * -type f -print0 | xargs -0 chmod 0644
echo 'Finished Pull'
```