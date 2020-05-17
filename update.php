<?php
    $url = 'https://api.instagram.com/v1/';
    $clientId = '26b368f8fc144881b1e1b856fbaf6b67';
    $clientSecret = 'ef33473eff2840e1a6f061780b9d1978';
    $accessToken = '2342875871.26b368f.a29f275b9f3a4993bafa4b34218cbb4c';

    // Elfsight Access token
    // 2342875871.745c638.d20ce3d3970a4ad18de69d839a213069
    
    $POST_LIMIT = 50;

    error_reporting( E_ALL );
    ini_set( 'display_errors', 1 );
    
    function timestampSort($a, $b) {
        if ( $a->timestamp > $b->timestamp ) {
            return -1;
        }
        
        if ( $a->timestamp < $b->timestamp ) {
            return 1;
        }
        
        return 0;
    }

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
            $currentItems[] = (object) array(
                'timestamp' => $item->created_time,
                'image' => $item->images->standard_resolution->url,
                'id' => $item->id,
                'url' => $item->link
            );
            
            file_put_contents('images/' . $item->id . '.jpg', file_get_contents($item->images->standard_resolution->url));

            $addedTimestamps[] = $item->created_time;
        else :
            break;
        endif;
    endforeach;
    
    usort($currentItems, 'timestampSort');

    $JSONItems = json_encode( array_slice($currentItems, 0, $POST_LIMIT ) );

    $fp = fopen( 'index.json', 'w' );
    fwrite( $fp, $JSONItems );
    fclose( $fp );
