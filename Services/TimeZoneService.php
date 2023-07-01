<?php
namespace Services;


class TimeZoneService {
    
    /**
     * Time zone finder
     * 
     */
    public static function getTimeZone($version): string {
        
        $vParts = explode('.', $version);
        $v1stDigit = isset( $vParts[0] ) ? $vParts[0] : 0;
        $v2ndDigit = isset( $vParts[1] ) ? $vParts[1] : 0;
        $v3rdDigit = isset( $vParts[2] ) ? $vParts[2] : 0;
        $lastPart = explode('+', $vParts[2] );
        $v3rdDigit = isset( $lastPart[0] ) ? $lastPart[0] : 0;
        $v4thDigit = isset( $lastPart[1] ) ? $lastPart[1] : 0;
        if(  intval( $v1stDigit )   > 1 ){
            return  'UTC';
        }
        if(   intval( $v1stDigit )   >= 1 && intval( $v2ndDigit )  > 0 ){
            return 'UTC';
        }
        if( intval( $v3rdDigit )  >= 17 ){
            if( intval( $v4thDigit )   > 60 ){
                return 'UTC';
            }
        } 
        
        return 'Europe/Berlin';
        
    }
    
}