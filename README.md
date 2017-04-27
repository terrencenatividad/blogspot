# Specifications
	
### How to make a project
* Fork webcore_modular
> Owner: cidsystems
>
> Project: <Create new project>
* Clone Project to www
> git clone <Project Git Link>
* Add webcore_modular as upstream
> git remote add upstream <Webcore_modular Git Link>
* Delete Ignored Files in GitIgnore
> apanel/modules/*
>
> !apanel/modules/home
>
> !apanel/modules/wc_core
* Add Modules
> cd apanel/modules
>
> git submodule add upstream <Module Git Link>

### How to update project's webcore
* Merge from upstream
> git merge upstream/master

### How to update all modules
* Pull 
> git pull --recurse-submodules