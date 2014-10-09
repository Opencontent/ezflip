<?php

/**
 * @see https://github.com/Yumpu/Yumpu-SDK
 */

class YumpuConnector
{
    public $config = array(
        'token' => '',
        'returnFormat' => 'array',
        'debug' => false,
        'useCurl' => true,
        'logFilePath' => 'yumpu.log'
    );
    protected $yumpuEndpoints = array(
        'user/get' => 'http://api.yumpu.com/2.0/user.json',
        'user/post' => 'http://api.yumpu.com/2.0/user.json',
        'user/put' => 'http://api.yumpu.com/2.0/user.json',
        'document/get' => 'http://api.yumpu.com/2.0/document.json',
        'document/post/file' => 'http://api.yumpu.com/2.0/document/file.json',
        'document/post/url' => 'http://api.yumpu.com/2.0/document/url.json',
        'document/progress' => 'http://api.yumpu.com/2.0/document/progress.json',
        'documents/get' => 'http://api.yumpu.com/2.0/documents.json',
        'document/delete' => 'http://api.yumpu.com/2.0/document.json',
        'document/put' => 'http://api.yumpu.com/2.0/document.json',
        'collection/get' => 'http://api.yumpu.com/2.0/collection.json',
        'collection/post' => 'http://api.yumpu.com/2.0/collection.json',
        'collection/put' => 'http://api.yumpu.com/2.0/collection.json',
        'collection/delete' => 'http://api.yumpu.com/2.0/collection.json',
        'collections/get' => 'http://api.yumpu.com/2.0/collections.json',
        'section/get' => 'http://api.yumpu.com/2.0/collection/section.json',
        'section/post' => 'http://api.yumpu.com/2.0/collection/section.json',
        'section/put' => 'http://api.yumpu.com/2.0/collection/section.json',
        'section/delete' => 'http://api.yumpu.com/2.0/collection/section.json',
        'sectionDocument/post' => 'http://api.yumpu.com/2.0/collection/section/document.json',
        'sectionDocument/delete' => 'http://api.yumpu.com/2.0/collection/section/document.json',
        'categories/get' => 'http://api.yumpu.com/2.0/document/categories.json',
        'countries/get' => 'http://api.yumpu.com/2.0/countries.json',
        'languages/get' => 'http://api.yumpu.com/2.0/document/languages.json',
        'search/get' => 'http://search.yumpu.com/2.0/search.json'
    );

    /**
     * @see http://developers.yumpu.com/api/document/post-file/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function postDocumentFile( $data )
    {
        if ( !isset( $data['title'] ) )
        {
            $data['title'] = basename( $data['file'], '.pdf' );
        }

        $params = array(
            'action' => 'document/post/file',
            'method' => 'POST',
            'data' => $data
        );

        if ( isset( $data['token'] ) )
        {
            $params['token'] = $data['token'];
        }

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/document/post-url/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function postDocumentUrl( $data )
    {
        if ( !isset( $data['title'] ) )
        {
            $data['title'] = basename( $data['file'], '.pdf' );
        }
        $params = array(
            'action' => 'document/post/url',
            'method' => 'POST',
            'data' => $data
        );
        if ( isset( $data['token'] ) )
        {
            $params['token'] = $data['token'];
        }

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/document-progress/get/
     *
     * @param $progressId
     *
     * @return array|stdClass
     */
    public function getDocumentProgress( $progressId )
    {
        $params = array(
            'action' => 'document/progress',
            'method' => 'GET',
            'data' => array(
                'id' => $progressId
            )
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/document/get/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function getDocument( $data )
    {
        $params = array(
            'action' => 'document/get',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/documents/get/
     *
     * @param array $data
     *
     * @return array|stdClass
     */
    public function getDocuments( $data = array() )
    {
        $params = array(
            'action' => 'documents/get',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/document/delete/
     *
     * @param $id
     *
     * @return array|stdClass
     */
    public function deleteDocument( $id )
    {
        $params = array(
            'action' => 'document/delete',
            'data' => array(
                'id' => $id
            ),
            'method' => 'POST',
            'customRequest' => 'DELETE'
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/document/put/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function putDocument( $data )
    {
        $params = array(
            'action' => 'document/put',
            'data' => $data,
            'method' => 'POST',
            'customRequest' => 'PUT'
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/user/post/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function postUser( $data )
    {
        $params = array(
            'action' => 'user/post',
            'method' => 'POST',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/user/get/
     *
     * @param array $data
     *
     * @return array|stdClass
     */
    public function getUser( $data = array() )
    {
        $params = array(
            'action' => 'user/get',
            'data' => $data,
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/user/put/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function putUser( $data )
    {
        $params = array(
            'action' => 'user/put',
            'method' => 'POST',
            'customRequest' => 'PUT',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/collection/post/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function postCollection( $data )
    {
        $params = array(
            'action' => 'collection/post',
            'method' => 'POST',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/collection/put/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function putCollection( $data )
    {
        $params = array(
            'action' => 'collection/put',
            'method' => 'POST',
            'customRequest' => 'PUT',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/collection/delete/
     *
     * @param $id
     *
     * @return array|stdClass
     */
    public function deleteCollection( $id )
    {
        $params = array(
            'action' => 'collection/delete',
            'data' => array(
                'id' => $id,
            ),
            'method' => 'POST',
            'customRequest' => 'DELETE'
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/collection/get/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function getCollection( $data )
    {
        $params = array(
            'action' => 'collection/get',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/collections/get/
     *
     * @param array $data
     *
     * @return array|stdClass
     */
    public function getCollections( $data = array() )
    {
        $params = array(
            'action' => 'collections/get',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/section/post/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function postSection( $data )
    {
        $params = array(
            'action' => 'section/post',
            'method' => 'POST',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/section/put/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function putSection( $data )
    {
        $params = array(
            'action' => 'section/put',
            'method' => 'POST',
            'customRequest' => 'PUT',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/section/delete/
     *
     * @param $id
     *
     * @return array|stdClass
     */
    public function deleteSection( $id )
    {
        $params = array(
            'action' => 'section/delete',
            'data' => array(
                'id' => $id,
            ),
            'method' => 'POST',
            'customRequest' => 'DELETE'
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/section/get/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function getSection( $data )
    {
        $params = array(
            'action' => 'section/get',
            'data' => $data,
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/section-document/post/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function postSectionDocument( $data )
    {
        $params = array(
            'action' => 'sectionDocument/post',
            'method' => 'POST',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/section-document/delete/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function deleteSectionDocument( $data )
    {
        $params = array(
            'action' => 'sectionDocument/delete',
            'method' => 'POST',
            'customRequest' => 'DELETE',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/section-catogories/get/
     *
     * @return array|stdClass
     */
    public function getCategories()
    {
        $params = array(
            'action' => 'categories/get',
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/countries/get/
     *
     * @return array|stdClass
     */
    public function getCountries()
    {
        $params = array(
            'action' => 'countries/get',
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/document-languages/get/
     *
     * @return array|stdClass
     */
    public function getLanguages()
    {
        $params = array(
            'action' => 'languages/get',
        );

        return $this->executeRequest( $params );
    }

    /**
     * @see http://developers.yumpu.com/api/search/get/
     *
     * @param $data
     *
     * @return array|stdClass
     */
    public function search( $data )
    {
        $params = array(
            'action' => 'search/get',
            'data' => $data
        );

        return $this->executeRequest( $params );
    }

    /**
     * @param $params
     *
     * @return array|stdClass
     * @throws Exception
     */
    protected function executeRequest( $params )
    {

        if ( isset( $params['data']['token'] ) && !empty( $params['data']['token'] ) )
        {
            $params['token'] = $params['data']['token'];
            unset( $params['data']['token'] );
        }
        else
        {
            $params['token'] = $this->config['token'];
        }
        if ( !isset( $params['method'] ) || empty( $params['method'] ) )
        {
            $params['method'] = 'GET';
        }

        $url = $this->getActionUrl( $params );

        if ( empty( $url ) )
        {
            return false;
        }

        if ( isset( $params['data'] ) && !empty( $params['data'] ) )
        {
            if ( isset( $params['data']['file'] ) )
            {
                if ( version_compare( PHP_VERSION, '5.5.0' ) >= 0 )
                {
                    $params['data']['file'] = new CurlFile( $params['data']['file'] );
                }
                else
                {
                    $params['data']['file'] = '@' . $params['data']['file'];
                }
            }
            if ( isset( $params['data']['page_teaser_image'] ) )
            {
                $params['data']['page_teaser_image'] = '@' . $params['data']['page_teaser_image'];
            }
        }

        if ( $this->isCurlInstalled() )
        {
            $response = $this->doCurl( $url, $params );
        }
        else
        {
            throw new Exception( "Yumpu Connector needs curl extension" );
        }

        $check = json_decode( $response, true );
        if ( isset( $check['errors'] ) )
        {
            $error = array();
            foreach ( $check['errors'] as $field )
            {
                foreach ( $field as $message )
                {
                    $error[] = $message;
                }
            }
            throw new Exception( implode( ', ', $error ) );
        }

        if ( is_string( $response ) )
        {
            if ( $this->config['returnFormat'] != 'json' )
            {
                $response = json_decode( $response, true );
            }
        }

        return $response;
    }

    /**
     * @param $params
     *
     * @return string
     */
    protected function getActionUrl( $params )
    {
        if ( !isset( $params['action'] ) || empty( $params['action'] ) )
        {
            $this->log( 'DEBUG', 'Trying to make a request to Yumpu without an action' );

            return false;
        }

        if ( !isset( $this->yumpuEndpoints[$params['action']] ) )
        {
            $this->log( 'DEBUG', 'Trying to make a request to Yumpu with an undefined action' );

            return false;
        }

        $url = $this->yumpuEndpoints[$params['action']];
        if ( isset( $params['data'] ) && isset( $params['method'] ) && !empty( $params['data'] ) && ( $params['method'] === 'GET' ) )
        {
            if ( strpos( $url, '?' ) === false )
            {
                $url .= '?' . http_build_query( $params['data'] );
            }
            else
            {
                $url .= '&' . http_build_query( $params['data'] );
            }
        }

        return $url;
    }

    /**
     * @param string $type
     * @param string $message
     */
    protected function log( $type, $message )
    {
        if ( $this->config['debug'] )
        {
            eZLog::write( "[$type] $message", $this->config['logFilePath'] );
        }
    }

    /**
     * @return bool
     */
    private function isCurlInstalled()
    {
        if ( in_array( 'curl', get_loaded_extensions() ) )
        {
            return true;
        }

        return false;
    }

    /**
     * @param string $url
     * @param array $params
     *
     * @return mixed curl response
     */
    private function doCurl( $url, $params )
    {
        $headers = array( 'X-ACCESS-TOKEN: ' . $params['token'] );

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

        if ( isset( $params['data'] ) && !empty( $params['data'] ) )
        {
            if ( $params['method'] == 'POST' )
            {
                if ( is_array( $params['data'] ) )
                {
                    if ( !isset( $params['data']['file'] ) && !isset( $params['data']['page_teaser_image'] ) )
                    {
                        curl_setopt( $ch, CURLOPT_POST, true );
                        $params['data'] = http_build_query( $params['data'] );
                    }
                    curl_setopt( $ch, CURLOPT_POSTFIELDS, $params['data'] );
                }
            }
        }
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        if ( isset( $params['customRequest'] ) )
        {
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $params['customRequest'] );
        }
        else
        {
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $params['method'] );
        }

        $this->log( "DEBUG", "Curl params: " . print_r( $params, true ) );

        $response = curl_exec( $ch );

        if ( $response === false )
        {
            $this->log( "ERROR", "Yumpu response: " . curl_error( $ch ) );
            throw new Exception( curl_error( $ch ) );
        }
        else
        {
            $this->log( "DEBUG", "Yumpu response: " . print_r( json_decode( $response, true ), true ) );
        }
        curl_close( $ch );

        return $response;
    }


}