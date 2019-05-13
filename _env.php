<?php

//overall settings

error_reporting(E_ALL ^ E_STRICT);
ini_set("display_errors", 0);
ini_set('date.timezone', 'UTC');

//path settings

define('ENV_PATH_CACHE', 'cache');
define('ENV_PATH_CODEBEHIND', 'codebehind');
define('ENV_PATH_TEMPLATE', 'templates');

//database access settings

define('ENV_DB_HOST', 'localhost');
define('ENV_DB_NAME', 'dbname');
define('ENV_DB_USER', 'dbuser');
define('ENV_DB_PASS', 'dbpass');

//user pasword hashing settings

define('ENV_USER_HASH_SALT', 'ds!2c5jx,,jgFSNJKn8G3:52D/khhySb,G31@3eDGB34!#');

//ecommerce settings

define('ENV_ECOMM_OBJ', 'AuthorizeDotNet');
define('ENV_ECOMM_USER_ID', '3t6ZEdfJSs5e');
define('ENV_ECOMM_USER_KEY', '746YsG6QdUe33gM7');
define('ENV_ECOMM_TEST_MODE', false);

//----spi integration settings

//api access: acess token
define('ENV_SPI_TOKEN', '3839125e-db6e-40df-abb3-989a0aba8b61');
//api access: orgId
define('ENV_SPI_ORG_ID', '5758cc95f7c1e5d9d481d0c1');

/*//OUR AWS
//api host: base url [some fake port on existing ip; not used, but needed]
define('ENV_SPI_HOST_BASE', 'http://52.52.95.168:3007');
//api host: authentication
define('ENV_SPI_HOST_AUTH', 'http://52.52.95.168:3000');
//api host: template
define('ENV_SPI_HOST_TEMPLATE', 'http://52.52.95.168:3005');
//api host: instance
define('ENV_SPI_HOST_INSTANCE', 'http://52.52.95.168:3001');
//api host: jobs
define('ENV_SPI_HOST_JOB', 'http://52.52.95.168:3004');
//api host: assets
define('ENV_SPI_HOST_ASSETS', 'http://52.52.95.168:3006');*/

// site domain
define('ENV_DOMAIN', 'amarki.creelit.com');

//OUR AWS - SSL
//api host: base url [some fake port on existing ip; not used, but needed]
define('ENV_SPI_HOST_BASE', 'https://spi.amarki.com:3007');
//api host: authentication
define('ENV_SPI_HOST_AUTH', 'https://spi.amarki.com:3000');
//api host: template
define('ENV_SPI_HOST_TEMPLATE', 'https://spi.amarki.com:3005');
//api host: instance
define('ENV_SPI_HOST_INSTANCE', 'https://spi.amarki.com:3001');
//api host: jobs
define('ENV_SPI_HOST_JOB', 'https://spi.amarki.com:3004');
//api host: assets
define('ENV_SPI_HOST_ASSETS', 'https://spi.amarki.com:3006');

//global font map location
define('ENV_SPI_GLOBAL_FONT_MAP', '57d9c9e25e7054b20f805530/eyJ1cmlQYXRoIjoiMTE4YjZmMzMtODMyNC00NmI4LWJkMDQtNTYwZjdiMDRiOTZlIn0%3D');
//default image asset map id
define('ENV_SPI_DEFAULT_ASSET_MAP_ID', '57d9c9dc5e7054b20f8054bf');
//default image asset uri path
define('ENV_SPI_DEFAULT_ASSET_URI_PATH', '5525abb3-bea7-418a-aa15-535772bd64ef');
//test template guid
//define('ENV_SPI_TPL_TEST_GUID', 'hsi-bc_whtblklux_diamondphoto1');
//define('ENV_SPI_TPL_TEST_GUID', 'hsi-bc_whtblklux-1-1-test');
define('ENV_SPI_TPL_TEST_GUID', 'hsi-bc_whtblklux-1-1_656');

// provide imagevars for template editor
define('ENV_SPI_IMAGEVARS', false);
// enable image api in template editor (image gallery panel)
define('ENV_SPI_IMAGEAPI', false);
// return imageapi urls with https scheme
define('ENV_SPI_IMAGEAPI_HTTPS', false);

//-----Email settings
//send emails
define('ENV_EMAIL_SEND', false);
//sender_domain|email_host|email_secure_setting|email_port|email_login|email_password
define('ENV_EMAIL_CONFIG', 'amarki.com|email-smtp.us-west-2.amazonaws.com|tls|587|AKIAIROPCV3RX7PQ5QVQ|AjaDIjNfoDgZ3M33LR/e9a59uJX3wwkZf5CZgF7jct0x');

//-----HSI integration settings
//api host: base url
define('ENV_HSI_API', 'https://homesmart.com');
//access header: email
define('ENV_HSI_EMAIL', 'wayne.creel@creelit.com');
//access header: token
define('ENV_HSI_TOKEN', 'u8ut4jiwetjiogj90sgju90sdg0sdhi0');
//php-resque redis prefix
define('ENV_RESQUE_PREFIX', 'development');
//disable hsi property data processing
define('ENV_HSI_PROCESS_PROPERTY', true);

//adds walkme code to site version
define('ENV_WALKME_ENABLED', false);
//adds hotjar code to site version
define('ENV_HOTJAR_ENABLED', false);
//adds isogram code to site version
define('ENV_ISOGRAM_ENABLED', false);
//adds livechatinc code to site
define('ENV_LIVECHATINC_ENABLED', false);

//------default context settings
//https mode
define('ENV_CONTEXT_HTTPS_DEFAULT', false);
//domain
define('ENV_CONTEXT_DOMAIN_DEFAULT', 'localdevdomain.loc');
//default company subdomain base [company registration]
define('ENV_COMPANY_DOMAIN_BASE', 'creelit.com');

//social sharing settings
define('ENV_SHARING_API_HOST', 'https://acp.api.amarki.com');
define('ENV_SHARING_API_HOST_AUTH', 'https://auth.amarki.com');
//display social sharing: true - show Social Sharing, false - hide Social Sharing
define('ENV_SHOW_SOCIAL_SHARING', false);

define('ENV_EDDM_MAP_URL', '/eddm');

define('ENV_E_COMMERCE_TOKEN', 'b2e9ef95-77f5-46df-845e-85e0eac10c3ec60943e6-1faa-4a87-8c06-0bf0390e865d759ba1d9-be5f-4716-89bc-ff04bc3ca1f18260e9b7-6aa2-41d5-83d5-093ef619797e6144b3ad-afb9-48a4-8ba5-60116a9af682');

//property update api external/internal
define('ENV_API_PROPERTY_KEY', 'wiu345$%66#');

//unlayer.com api key
define('ENV_API_UNLAYER_KEY', 'UKbA60xKQlkqrsZXhkcJ5CAwr9S2ING2l6h5kFmuHhkFJKaCMrtPNTy7LqRzsTHA');

