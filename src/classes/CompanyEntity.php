<?php

class CompanyEntity {
  protected $id;
  protected $name;
  protected $description;
  protected $address;

  /**
   * Accept an array of data matching properties of this class
   * and create the class
   *
   * @param array $data The data to use to create
   */
  public function __construct($data) {
    // no id if we're creating
    if (isset($data['id'])) {
      $this->id = $data['id'];
    }
    $this->name = $data['name'];
    $this->description = $data['description'];
    $this->address = $data['address'];
  }

  public function getId() {
    return $this->id;
  }

  public function getName() {
    return $this->name;
  }

  public function getDescription() {
    return $this->description;
  }

  public function getShortDescription() {
    return substr($this->description, 0, 20);
  }

  public function getAddress() {
    return $this->address;
  }
}
