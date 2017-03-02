# Specifications
	
### Core
* Same Database CRUD functions with Webcore
> Ex.
>
> updateRecord([array_fields], '<table>', '<conditions>'); 
>
> Improved with 'No Edit' Debugging Capabilities
> > var_dump($this->db->error); // show errors
> > echo $this->db->query; // show executed query
>
> Converted to Object Oriented
> > $this->db->updateRecord([array_fields], '<table>', '<conditions>');
* Object Oriented Model and Controller
	* Multiple Controller (frontend and backend), One Model per Module

### Module Activation
* Enable and Disable Module on Apanel
* Activating a Module will Auto Create needed Tables
* Deactivating a Module will prevent anyone from Accessing the Module
* Option for Clearing Module: Delete uneeded Tables (not affecting other modules)

### Module Access
* Access Permission per User Group
* One Module for Multiple Front end Page
> Ex. 
>
> > <web_root>/about_us
>
> > <web_root>/contact_us
>
> > Both are in one Module (Ongoing)
> 
> Ex.
>
> > <web_root>/gallery/images
>
> > <web_root>/gallery/videos
>
> > Both are in one Module
* Ajax is access through the same Module page
> Ex.
>
> > <web_root>/contact_us
>
> > Ajax Post will be to <web_root>/contact_us/ajax

### Default Database Tables
* wc_users
* wc_user_group
* wc_modules
* wc_module_access
* wc_reference
* wc_option
* wc_admin_logs
* wc_sequence_control