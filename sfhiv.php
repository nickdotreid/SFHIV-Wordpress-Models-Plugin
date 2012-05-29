<?php
/*
Plugin Name: SFHIV.ORG Models
Plugin URI: github??
Description: Creates and wraps the models used in the SFHIV.ORG Website
Version: 1.0
Author: nickreid
Author URI: http://nickreid.com
Author Email: nickreid@nickreid.com
License:

  Copyright 2012 TODO (nickreid@nickreid.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

require_once('page.php');

require_once('related_pages/related_pages.php');

// Register Taxonomies
require_once('sfhiv_year_tag.php');

// Register Post Types
require_once('sfhiv_user.php');

require_once('sfhiv_group.php');
require_once('sfhiv_services.php');
require_once('sfhiv_documents.php');
require_once('sfhiv_studies.php');

require_once('sfhiv_events.php');
require_once('sfhiv_location.php');

require_once('sfhiv_website_link.php');
require_once('sfhiv_link_to_page.php');

?>