<?php

namespace PluginMaster\Request;

use PluginMaster\Contracts\Request\Request as RequestContract;

class Request implements RequestContract
{

    protected $data = [];
    protected $headers = [];

    function __construct() {
        $this->setAjaxData();
        $this->setGetData();
        $this->setPostData();
        $this->setRequestHeaders();
    }


    private function setPostData() {
        foreach ( $_POST as $key => $value ) {
            $this->data[ $key ] = $value;
        }

    }


    private function setAjaxData() {
        $inputJSON = file_get_contents( 'php://input' );
        if ( empty( $_POST ) && $inputJSON ) {
            $input = json_decode( $inputJSON, true );
            if ( $input && gettype( $input ) === 'array' ) {
                foreach ( $input as $key => $value ) {
                    $this->data[ $key ] = $value;
                }
            }
        }
    }


    private function setGetData() {
        foreach ( $_GET as $key => $value ) {
            $this->data[ $key ] = $value;
        }

    }

    private function setRequestHeaders() {
        foreach ( $_SERVER as $key => $value ) {
            if ( substr( $key, 0, 5 ) <> 'HTTP_' ) {
                continue;
            }
            $header                   = str_replace( ' ', '-', ucwords( str_replace( '_', ' ', strtolower( substr( $key, 5 ) ) ) ) );
            $this->headers[ $header ] = $value;
        }
    }


    public function isMethod( $method ) {
        if ( strtoupper( $method ) === $_SERVER['REQUEST_METHOD'] ) {
            return true;
        }
        return false;
    }


    /**
     * set all requested data as this class property;
     */
    public function all() {
        return $this->data;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get( $key ) {
        return $this->data[ $key ] ?? null;
    }


    /**
     * @param $key
     * @return mixed|null
     */
    public function header( $key ) {
        return $this->headers[ $key ] ?? null;
    }

    public function url() {
        return (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    /**
     * @param $property
     * @return mixed|null
     */
    public function __get( $property ) {
        return $this->data[ $property ] ?? null;
    }


}
