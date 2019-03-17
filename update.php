<?php
    $url = 'https://api.instagram.com/v1/';
    $clientId = '';
    $clientSecret = '';
    $accessToken = '';

    error_reporting( E_ALL );
    ini_set( 'display_errors', 1 );

    if( is_file( 'index.json' ) ):
        $currentItems = (array)json_decode( file_get_contents( 'index.json' ) );
    else :
        $currentItems = (array)json_decode( file_get_contents( 'start.json' ) );
    endif;

    $url = $url . 'users/self/media/recent/?access_token=' . $accessToken;
    $JSONResponse = file_get_contents( $url );
    $response = json_decode( $JSONResponse );

    $addedTimestamps = array();

    foreach( $currentItems as $currentItem ):
        $addedTimestamps[] = $currentItem->timestamp;
    endforeach;

    foreach( $response->data as $item ) :
        if( !in_array( $item->created_time, $addedTimestamps ) ) :
            $currentItems[] = array(
                'timestamp' => $item->created_time,
                'image' => $item->images->standard_resolution->url,
                'id' => $item->id,
                'url' => $item->link
            );

            $addedTimestamps[] = $item->created_time;
        else :
            break;
        endif;
    endforeach;

    $JSONItems = json_encode( $currentItems );

    $fp = fopen( 'index.json', 'w' );
    fwrite( $fp, $JSONItems );
    fclose( $fp );
