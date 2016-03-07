<?php

if(!function_exists('DQH_notice_error')):
    function DQH_notice_error($msg) {
        $class = 'notice notice-error';
        $message = __( $msg, 'sample-text-domain' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }
endif;

if(!function_exists('DQH_notice_success')):
    function DQH_notice_success($msg) {
        $class = 'notice notice-success';
        $message = __($msg, 'sample-text-domain' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }
endif;
