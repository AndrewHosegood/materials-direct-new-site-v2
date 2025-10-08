<?php
add_filter('upload_mimes', 'add_dxf_mime', 99); 
function add_dxf_mime($mimes) {

    $mimes['dxf'] = 'application/dxf';
    $mimes['dxf|dxfz'] = 'application/x-dxf'; 
    $mimes['dxf'] = 'image/vnd.dxf'; 
    $mimes['dxf'] = 'application/octet-stream'; 

    return $mimes;
}


add_filter('wp_check_filetype_and_ext', 'force_allow_dxf', 10, 5);
function force_allow_dxf($data, $file, $filename, $mimes, $real_mime = null) {

    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (strtolower($ext) === 'dxf') {
        $data['ext'] = 'dxf';
        $data['type'] = 'application/dxf'; 
        $data['proper_filename'] = $filename; 
    }
    return $data;
}