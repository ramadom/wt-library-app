<?php

namespace WT\Library\Controller;


class LibraryResponse  implements \JsonSerializable {

  private $message;
  private $data;

  public function __construct($data = null, $message = null) {
    $this->message = $message;
    $this->data = $data;
  }

  #[\Override]
  public function jsonSerialize(): mixed {
    $json = [];
    $json['message'] = $this->message;
    $json['data'] = $this->data;
    return $json;
  }
}
