<?php

class WJECF_Sanitizer {

    /**
     * Singleton Instance
     *
     * @static
     * @return Singleton Instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    protected static $_instance = null;

    /**
     * Sanitizes form input for database output
     * 
     * @param mixed $value 
     * @param string $requested_format The output format requested 
     * @param mixed|null $fallback_value Value to return in case of invalid value
     * @return mixed Sanitized
     */
    public function sanitize( $value, $requested_format, $fallback_value = null ) {

        switch ($requested_format) {
            case '': 
                return (string) $value;

            case 'html':
                return wp_kses_post( $value );
                
            case 'clean': 
                //applies sanitize_text_field; or recursively if it's an array
                return wc_clean( $value );

            case 'int,':
            case 'int[]':
                if ( is_array( $value ) ) {
                    $values = $value;
                } elseif ( $value === '' || $value === null || $value === false ) {
                    $values = array();
                } else {
                    $values = explode( ',', $value ); // int[] also accepts comma separated string
                }
                $retval = array();
                foreach( $values as $value) {
                    $sane = $this->sanitize( $value, 'int' );
                    if ( $sane !== null ) $retval[] = $sane;
                }
                if ($requested_format === 'int,') {
                    return implode( ',', $retval );
                }
                return $retval;

            case 'int': 
                return is_numeric( $value ) ? intval( $value ) : $fallback_value;

            case 'yesno': 
                return $value === 'yes' ? 'yes' : 'no';

            case 'decimal': 
                $value = wc_format_decimal( $value );
                return ( $value === '' || $value === false ) ? $fallback_value : $value;
        }

        throw new Exception("Unknown sanitization rule " . $requested_format);
    }    
}