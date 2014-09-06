<?php

namespace ssa\runner\converter;

/**
 * FileEncode is used for return file content.
 * This encoder can encode 2 type of return
 * 
 * It can be a file path, or the return of FileResolver
 *
 * @author thomas
 */
class FileEncoder implements Encoder {
    /**
     * current file content type
     * @var type 
     */
    private $contentType = null;
    
    public function encode($data) {
        if (gettype($data) == 'string') {
            $this->contentType = mime_content_type($data);
            return file_get_contents($data);
        } else {
            $this->contentType = $data['type'];
            return file_get_contents($data['tmp_name']);
        }
    }

    /**
     * {@inherits}
     */
    public function getHeaders() {
        return array(
          'Content-type' => $this->contentType
        );
    }

}
