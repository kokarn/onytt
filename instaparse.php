<?php
$parsedItems = array();
foreach( glob( 'responses/*.json' ) as $filename ):
    $data = json_decode( file_get_contents( $filename ) );
    foreach( $data->media->nodes as $item ):

        $parsedItems[] = array(
            'timestamp' => $item->date,
            'image' => str_replace( '/e35/', '/s640x640/sh0.08/e35/', $item->display_src ),
            'id' => $item->id,
            'url' => 'https://www.instagram.com/p/_' . $item->code
        );
    endforeach;
endforeach;

$fp = fopen( 'start.json', 'w' );
fwrite( $fp, json_encode( $parsedItems, JSON_PRETTY_PRINT ) );
fclose( $fp );
