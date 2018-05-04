<?php

class CompanyMapper extends Mapper {
  public function getCompanies() {
    $sql = 'SELECT * FROM companies';

    $results = [];
    $stmt = $this->db->query($sql);
    while ($row = $stmt->fetch()) {
      $results[] = new CompanyEntity($row);
    }

    return $results;
  }

  /**
   * Get one company by ID
   *
   * @param int $company_id The ID of the company
   * @return CompanyEntity  The company
   */
  public function getCompanyById($company_id) {
    $sql = 'SELECT * FROM companies WHERE id = :company_id';

    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute(['company_id' => $company_id]);

    if ($result) {
      return new CompanyEntity($stmt->fetch());
    }
  }

  public function createCompany(CompanyEntity $company) {
    $sql = 'INSERT INTO companies (name, description, address) VALUES (:name, :description, :address)';

    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute([
      'name' => $company->getName(),
      'description' => $company->getDescription(),
      'address' => $company->getAddress(),
    ]);

    if (!$result) {
      throw new Exception('could not create company');
    }
  }

  public function deleteCompanyById($company_id) {
    $sql = 'DELETE FROM companies WHERE id = :company_id';

    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute(['company_id' => $company_id]);

    if (!$result) {
      throw new Exception('could not delete company');
    }
  }
}
