<?php
namespace Nuki\Models\Data;

class File {
  
  /**
   * Contains file path
   * 
   * @var string 
   */
  private $path;
  
  /**
   * Contains file name
   * 
   * @var string 
   */
  private $name;
  
  /**
   * Contains file extension
   * 
   * @var string 
   */
  private $extension;
  
  /**
   * Contains size
   * 
   * @var int 
   */
  private $size;
  
  /**
   * Contains file encoding
   * 
   * @var string 
   */
  private $encoding;
  
  /**
   * Contains mime type
   * 
   * @var string 
   */
  private $mime;

  /**
   * Construct file by path
   * 
   * @param string $filepath
   */
  public function __construct(string $filepath, int $size) {
    if (!file_exists($filepath)) {
      throw new \Nuki\Exceptions\Base('Given file does not or you do not have access');
    }
    
    //Set path
    $this->path = $filepath;
    
    $mime = new \finfo(FILEINFO_MIME_TYPE);
    //Set mime
    $this->mime = $mime->file();
    
    //Set encoding
    $encoding = new \finfo(FILEINFO_MIME_ENCODING);
    $this->encoding = $encoding->file($filepath);

    //Set size
    $this->size = $size;
  }
  
  /**
   * Set file name
   * if name not give, it will be generated
   * 
   * @param string|bool $name
   * @return void
   */
  public function setName($name = false) {
    if ($name === false || empty($name)) {
      $this->name = \Nuki\Handlers\Core\Assist::randomString();
      return;
    }
    
    $this->name = $name;
  }
  
  /**
   * Override setted extension
   * 
   * @param string $extension
   * @return void
   */
  public function setExtension($extension) {
    $this->extension = $extension;
  }
}
