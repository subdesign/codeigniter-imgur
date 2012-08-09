# Imgur API wrapper for anonymous resources

Imgur API provides easy way to handle images, the library makes it possible to use the API's [anonymous resources](http://api.imgur.com/resources_anon)

## Installation

Get your API key from [imgur.com](https://imgur.com/register/api_anon)

If you use this package in conventional way, then copy the files to their appropriate folders, and set up the config file.

    $config['imgur_apikey']   = ''; // Imgur API key
    $config['imgur_format']   = 'json'; // json OR xml
    $config['imgur_xml_type'] = 'object'; // array OR object

Installing this package as spark:

    php tools/spark install -v1.0.0 imgur

## Implemented methods

* move_image() /Sideloading/
* upload()
* stats()
* album()
* image()
* delete()
* oembed()

## Usage

Load the spark:

    $this->load->spark('imgur/1.0.0');  
OR load the library

    $this->load->library('imgur');

### move_image()
Image sideloading allows you to move an image from another web host onto Imgur

    $params = array(
        'url'  => 'http://yoursite.com/picture.jpg',
        'edit' => FALSE
    );

    $response = $this->imgur->move_image($params);
If you wish to edit the image first, set edit to TRUE

### upload()
Uploads an image

_Upload from url:_

    $params = array(
        'image'   => 'http://yoursite.com/picture.jpg',
        'type'    => 'url', // optional
        'name'    => 'image.jpg', // optional
        'title'   => 'Picture name', // optional
        'caption' => 'Picture caption' // optional
    );

_Upload from file:_

    $params = array(
        'image' => base64_encode(file_get_contents(FCPATH . 'picture.jpg')),			
        'type'  => 'base64'
    );

    $response = $this->imgur->upload($params);

### stats()
Display site statistics, such as bandwidth usage, images uploaded, image views, and average image size

    $response = $this->imgur->stats('month');

_Possible parameters: 'today', 'week', 'month'_

### album()
Returns album information and lists all images that belong to the album

    $response = $this->imgur->album($id);

### image()
Returns all the information about a certain image

    $response = $this->imgur->image($hash);

### delete()
Deletes an image

    $response = $this->imgur->delete($delete_hash);

### oembed()
Oembed allows you to make a request for an album or image URL and it will return the embed code as well as additional information about the object. For additional information please see the [oembed documentation](http://oembed.com)

    $params = array(
        'url'       => 'http://i.imgur.com/xxxxx.png',
        'format'    => 'json', // optional
        'maxheight' => 200, // optional
        'maxwidth'  => 200 // optional
    );

    $response = $this->imgur->oembed($params);

## Return values

As you set in the config file, you can get json or xml(array or object) return values. If you get back FALSE result, check your log file

## Requirements

[cURL](http://getsparks.org/packages/curl) spark/library by Phil Sturgeon

## Note

It's for the anonymous resources, maybe I'll do a library later for OAuth authenticated resources

## License

[MIT License](http://www.opensource.org/licenses/MIT)

C. 2012 Barna Szalai (sz.b@devartpro.com)